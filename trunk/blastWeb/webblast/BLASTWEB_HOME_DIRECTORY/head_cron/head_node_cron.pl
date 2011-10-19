#!/usr/bin/perl
#Masterblast.pl
#This code manages the slavenodes, it decides whether
#a slave node should be running or shutdown.
#It also checks each slave node's PIDs to make sure that they didn't crash.
#When a job's progress matches a completed state,
#the job is finalized by concatinating the output into a single file.
#Conflicts are also resolved if found.
###################################################################################################

use DBI;
use Config::IniFiles;

#NEED TO CHANGE MANUALLY
############################
$configFilePath = "/export/home/blastweb/webblast.conf";

#Load configuration from ini file
$cfg = new Config::IniFiles( -file => $configFilePath );

$sqlUser = $cfg->val('sql_config','sql_user');
$sqlPass = $cfg->val('sql_config','sql_pass');
$database = $cfg->val('sql_config','sql_database');
$sqlHost = $cfg->val('sql_config','sql_host');

$pathToSubmitScript = $cfg->val('scripts','slave_scripts');
$perlBin = $cfg->val('perl_bin','perl_bin_path');

$resultsPath     = $cfg->val('head_paths','output_chunks_path');
$csvResultsPath  = $cfg->val('head_paths','csv_chunks_path');
$resultsDestPath = $cfg->val('head_paths','finalized_results_path');
$queryPath       = $cfg->val('head_paths','input_chunks_path');
$deletePath      = $cfg->val('head_paths','delete_path');

#Static variables
#################
$statusSubmitted=6;
$statusNew=1;
$statusRunning=2;
$statusStopped=5;
$statusCompleted=3;
$statusFinalizing=8;
$statusDeleting=10;
$commandStop=1;
$commandStart=2;

#Configuration variables
########################
$webURL;
$jobCompleteMsg;
$jobCompleteSubj;
$perlFilesPath;
$maxJobAge;
$fromEmail;

#Initialize the database connection
##############################################
$dbh= DBI->connect("DBI:mysql:".$sqlDatabase.":".$sqlHost,$sqlUser,$sqlPass,{RaiseError=>1});
$dbh->{mysql_auto_reconnect} = 1;

GetConfiguration();
FixConflicts ();
CheckRunningNodes();
$nodesRunning=GetRunningNodes();
$schedule=GetScheduledNodes();
CheckJobsCompleted();
ApplyNodeSchedule($schedule,$nodesRunning);
CheckJobProgressIncramentConflicts();
ApplyUpdates();
RemoveOldJobs();

#Close database connections
#############################################
$dbh->disconnect();

#Checks all nodes with running status are actually running
#If a node is not actually running the node status is changed to stopped and command to stop
###########################################################################################
sub CheckRunningNodes {
	#Get Running Nodes
	$queryRunningNodesInfo ="SELECT hostname,pid,id FROM nodes WHERE status=$statusRunning AND commandid=$commandStart";
	$sth = $dbh->prepare($queryRunningNodesInfo);
	$sth->execute();
	while ( @runningNodesInfo = $sth->fetchrow_array() ) {
		#Check PID of running nodes
		my $pid = fork();
		if ($pid) {

			#parent code
			push( @$childs, $pid );
		}
		elsif ( $pid == 0 ) {

			#initiate a mysql connection to server
			$dbc = DBI->connect("DBI:mysql:".$sqlDatabase.":".$sqlHost,$sqlUser,$sqlPass,{RaiseError=>1});
		
			$exit = system("ssh -x ".$runningNodesInfo[0]." ps ".$runningNodesInfo[1]);
			if($exit==0)
			{
				#PID request was successful leave node info as is.
				print "\n$output | node: $runningNodesInfo[2]";
			}else{
				#The PID returned nothing update nodes to stopped delete it
				$queryMarkNodeStopped="DELETE FROM nodes WHERE id=$runningNodesInfo[2]";
				$sthc=$dbc->prepare($queryMarkNodeStopped);
				$sthc->execute();
				print "\nNode # $runningNodesInfo[2] has failed, updating status.";
	
				#Cancel in progress queries for this node
				$queryCancelQueriesInProgress="UPDATE blast_queries SET statusid=$statusNew, reservenode=0, reservepid=0 WHERE reservenode=$runningNodesInfo[2] AND statusid=$statusRunning";
				$sthc=$dbc->prepare($queryCancelQueriesInProgress);
				$sthc->execute();
				
				#clean up
				$sthc->finish();
			}
			#clean up database handle
			$dbc->disconnect();
			exit(0);
		}else{
			die "Couldn\'t fork: $!\n";
		}
	}
	foreach(@$childs){
		waitpid($_,0);
	}
}

#Gets the number of running nodes
###########################################
sub GetRunningNodes {
	#Get Number of running nodes
	$queryCountRunningNodes ="SELECT COUNT(*) FROM nodes WHERE (status=$statusSubmitted OR status=$statusRunning) AND commandid=$commandStart";
	$sth = $dbh->prepare($queryCountRunningNodes);
	$sth->execute();
	@nodesRunning = $sth->fetchrow_array();
	return $nodesRunning[0];
}

#Gets the number of nodes that should be running now according to schedule
##########################################################################
sub GetScheduledNodes {
	#Get number of nodes to run from schedule
	$queryScheduledNodes =
	  "SELECT nodes FROM schedule WHERE date<=NOW() ORDER BY date DESC LIMIT 1";
	$sth = $dbh->prepare($queryScheduledNodes);
	$sth->execute();
	@schedule = $sth->fetchrow_array();
	$sth->finish();
	return $schedule[0];
}

#Gets the configuration variables from the database
###################################################
sub GetConfiguration {
	$queryConfigPaths ="SELECT url, jobcompletemsg, jobcompletesubj, perlfilespath, maxjobage, fromemail FROM config ORDER BY id DESC LIMIT 1";
	$sth = $dbh->prepare($queryConfigPaths);
	$sth->execute();
	@configPaths    = $sth->fetchrow_array();
	$sth->finish();
	$webURL          = $configPaths[0];
	$jobCompleteMsg  = $configPaths[1];
	$jobCompleteSubj = $configPaths[2];
	$perlFilesPath   = $configPaths[3];
	$maxJobAge 	 = $configPaths[4];
	$fromEmail	 = $configPaths[5];	
	
}

#Check whether a job has completed
#if the job is completed it begins the concatination process.
#Checks whether any concatination processes have completed and send an email to the user if completed.
#######################################################################################################
sub CheckJobsCompleted {
	#Check jobs completed
	$queryNewJobs ="SELECT j.id,j.queriescompleted,j.queriesadded,j.submitpid,u.netid, j.status, u.email,j.token FROM blast_jobs j, users u WHERE (j.status=$statusNew OR j.status=$statusFinalizing) AND u.id=j.userid";
	print "\n".$queryNewJobs;
	$sthj = $dbh->prepare($queryNewJobs);
	$sthj->execute();

	while ( @jobsArray = $sthj->fetchrow_array() ) {
		$processStatus = `ps -ef | grep " $jobsArray[3] " | grep -v grep`;
		print "Jobs IDs:".$jobsArray[0]."\n";
		if ($processStatus) {
			print "Job submitting ID ".$jobsArray[0].":".$processStatus."\n";
			#do nothing job is still being submitted
		}
		else {
			if ($jobsArray[1] == $jobsArray[2]) {
				print "completed job found id=$jobsArray[0]";
				if ( $jobsArray[5] == $statusNew ) {

					#Mark job as finalizing
					$querySetJobToCompleted ="UPDATE blast_jobs SET status=$statusFinalizing WHERE id=$jobsArray[0]";
					$sth = $dbh->prepare($querySetJobToCompleted);
					$sth->execute();

					#Concatinate results and store file in destination path
					system( "nice ".$perlBin." "
						  . $perlFilesPath
						  . "concat.pl "
						  . $jobsArray[0] . " "
						  . $jobsArray[4] . " "
						  . $resultsPath . " "
						  . $resultsDestPath
						  . " result"
						  . " "
						  . $sqlUser
						  . " "
						  . $sqlPass
						  . " "
				 		  . $database
						  . " "
						  . $sqlHost
						  . " &" );
					print "nice ".$perlBin." "
                                                  . $perlFilesPath
                                                  . "concat.pl "
                                                  . $jobsArray[0] . " "
                                                  . $jobsArray[4] . " "
                                                  . $resultsPath . " "
                                                  . $resultsDestPath
                                                  . " result"
                                                  . " "
                                                  . $sqlUser
                                                  . " "
                                                  . $sqlPass
                                                  . " "
                                                  . $database
						  . " "
						  . $sqlHost
                                                  . " &";
					$error =
					  system( "nice ".$perlBin." "
						  . $perlFilesPath
						  . "concat.pl "
						  . $jobsArray[0] . " "
						  . $jobsArray[4] . " "
						  . $csvResultsPath . " "
						  . $resultsDestPath
						  . " csv" 
						  . " "
                                                  . $sqlUser
                                                  . " "
                                                  . $sqlPass
                                                  . " "
                                                  . $database
						  . " "
						  . $sqlHost
                                                  . " &" );
					print $error;

					#give concat.pl script time to update job status
					sleep 3;
				}

	   			#Check if the concatination process is done before sending user an e-mail
				$queryConcatStatus ="SELECT concatpidresult,concatpidcsv FROM blast_jobs WHERE id="
				  . $jobsArray[0];
				$sth = $dbh->prepare($queryConcatStatus);
				$sth->execute();

				@concatStatusArray = $sth->fetchrow_array();
				system("ps $concatStatusArray[1]");
				print "\nChecking concat status:".$?;
				if ( $? != 0 ) {
					system("ps $concatStatusArray[0]");
					if ( $? != 0 ) {
						#Mark job as completed
						$querySetJobToCompleted ="UPDATE blast_jobs SET status=$statusCompleted, concatpidresult=0, concatpidcsv=0, completeDate=NOW() WHERE id=$jobsArray[0]";
						$sth = $dbh->prepare($querySetJobToCompleted);
						$sth->execute();

						$updatedJobCompleteMsg  = $jobCompleteMsg;
						$updatedJobCompleteSubj = $jobCompleteSubj;

						$jobReplace = $jobsArray[0];
						$updatedJobCompleteSubj =~ s/\[job\]/$jobReplace/g;
						$updatedJobCompleteMsg  =~ s/\[job\]/$jobReplace/g;

						$csvWeburl = $webURL
						  . "download.php?job="
						  . $jobsArray[0]
						  . "&filetype=csv"
						  . "&token="
						  . $jobsArray[7];
						$resultWeburl = $webURL
						  . "download.php?job="
						  . $jobsArray[0]
						  . "&filetype=result"
						  . "&token="
                                                  . $jobsArray[7];

						$updatedJobCompleteMsg =~ s/\[url\]/$webURL/g;
						$updatedJobCompleteMsg =~ s/\[csvurl\]/$csvWeburl/g;
						$updatedJobCompleteMsg =~
						  s/\[resulturl\]/$resultWeburl/g;

						#send user an email informing him of job completion
						$sendmail = "/usr/sbin/sendmail -t";
						$from     = "From: ".$fromEmail."\n";
						$subject = "Subject: " . $updatedJobCompleteSubj . "\n";
						$content = $updatedJobCompleteMsg;
						$to = "To: " . $jobsArray[6] . "\n";

						open( SENDMAIL, "|$sendmail" )
						  or die "Cannot open $sendmail: $!";
						print SENDMAIL $from;
						print SENDMAIL $to;
						print SENDMAIL $subject;
						print SENDMAIL $content;
						close(SENDMAIL);
					}
				}
			}
		}
	}
	$sth->finish();
}

#Applies the schedule so nodes are shutdown or started according to schedule
############################################################################
sub ApplyNodeSchedule {
	my $schedule = shift;
	my $nodesRunning = shift;
	
	#Add or remove nodes depending on schedule
	if ( $schedule < $nodesRunning ) {

		#Stop nodes
		$nodesToStop = $nodesRunning - $schedule;
		print "\nStopping $nodesToStop nodes";
		$queryStopNodes ="UPDATE nodes SET commandid=$commandStop WHERE status=$statusRunning OR status=$statusSubmitted ORDER BY status DESC LIMIT $nodesToStop";
		$sth = $dbh->prepare($queryStopNodes);
		$sth->execute();

	}
	elsif ( $schedule > $nodesRunning ) {

		#start nodes
		$nodesToStart = $schedule - $nodesRunning;
		for($countNodes=1; $countNodes<=$nodesToStart; $countNodes++) 
		{
			system("/opt/torque/bin/qsub -l nodes=1:ppn=8 -v WEBBLASTCONF=".$configFilePath." ".$pathToSubmitScript."SubmitBlastWebNode.sh");
			print "\n/opt/torque/bin/qsub -l nodes=1:ppn=8 -v WEBBLASTCONF=".$configFilePath." ".$pathToSubmitScript."SubmitBlastWebNode.sh";
		}
	}
	$sth->finish();
}

#Fixes conflicts between node status and commands
#######################################################
sub FixConflicts {
	#Fix conflicts
	$queryNodesConflicts ="UPDATE nodes SET commandid=$commandStop WHERE status=$statusStopped";
	$sth = $dbh->prepare($queryNodesConflicts);
	$sth->execute();
}

#Checks that the job progress displayed is the actual job progress
##################################################################
sub CheckJobProgressIncramentConflicts {
	#Check for job progress incrament conflicts
	$queryCountCompleteFailed ="SELECT j.id FROM blast_jobs j WHERE j.status=1 AND ((SELECT COUNT(statusid) FROM blast_queries WHERE jobid=j.id AND (statusid=1 OR statusid=4 OR statusid=2))=0 OR j.queriescompleted>j.queriesadded)";
	$sth = $dbh->prepare($queryCountCompleteFailed);
	$sth->execute();
	while ( @failedJobComplete = $sth->fetchrow_array() ) {
		print "\nProgress conflict found".$failedJobComplete[0];
		$queryUpdateJobProgress = "UPDATE blast_jobs SET queriescompleted=(SELECT SUM(chunksize) FROM blast_queries WHERE jobid="
		  . $failedJobComplete[0]
		  . "), queriesadded=(SELECT SUM(chunksize) FROM blast_queries WHERE jobid="
                  . $failedJobComplete[0]
                  . ") WHERE id="
		  . $failedJobComplete[0];
		$sthj = $dbh->prepare($queryUpdateJobProgress);
		$sthj->execute();
	}
}

#Removes oldest expired job
#############################

sub RemoveOldJobs
{
	#Get all jobs older than maxJobAge
	$queryOldJobs = "SELECT j.id, u.netid FROM blast_jobs j, users u WHERE TIMESTAMPDIFF(DAY,j.completeDate,NOW())>".$maxJobAge." AND j.status=3 AND u.id=j.userid LIMIT 1";	
	$sth = $dbh->prepare($queryOldJobs);
	$sth->execute();
	@oldJob = $sth->fetchrow_array();
	if(@oldJob)
	{
		print "\nDeleting job number: ".$oldJob[0];
		$queryUpdateJobStatus = "UPDATE blast_jobs SET status=".$statusDeleting." WHERE id=".$oldJob[0];
		print "\n".$queryUpdateJobStatus;
		$sth->finish();
		$sthj = $dbh->prepare($queryUpdateJobStatus);
		$sthj->execute();
		DeleteFolder($resultsPath.$oldJob[0],$deletePath,$oldJob[0],0,"results");
		DeleteFolder($queryPath.$oldJob[0],$deletePath,$oldJob[0],0,"query");
		DeleteFolder($csvResultsPath.$oldJob[0],$deletePath,$oldJob[0],0,"csv");
		DeleteFile($resultsDestPath,$oldJob[1]."_".$oldJob[0].".csv");
		DeleteFile($resultsDestPath,$oldJob[1]."_".$oldJob[0].".result");	
		DeleteJob($oldJob[0]);
	}
	
	
}

sub DeleteFolder
{
	my $folderForDeletion = shift;
	my $deletionFolder = shift;
	my $jobid = shift;
	my $mkdir = shift;
	my $type = shift;

	system("mv ".$folderForDeletion." ".$deletionFolder.$jobid."_".$type);
	print "\nmv ".$folderForDeletion." ".$deletionFolder.$jobid."_".$type;
	if($mkdir)
	{
	        system("mkdir $folderForDeletion");
	        system("chmod -R 2777 $folderForDeletion");
	}
	system("rm -rf ".$deletionFolder.$jobid."_".$type);
	print "\nrm -rf ".$deletionFolder.$jobid."_".$type;
}

sub DeleteJob
{
	my $jobid = shift;

	$rowsAffected=1;
	print "starting loop";
	while($rowsAffected > 0)
	{
	        $queryDeleteQueries="DELETE FROM blast_queries WHERE jobid=".$jobid." LIMIT 2000";
	        $sth = $dbh->prepare($queryDeleteQueries);
	        $rowsAffected = $sth->execute();
	        sleep 2;
	}

}

sub DeleteFile
{
	my $dir = shift;
	my $filename = shift;
	system("rm -f ".$dir.$filename);
	print "\nrm -f ".$dir.$filename;
}

#Apply updated databases on slave nodes
########################################

sub ApplyUpdates
{
	$queryUpdates = "SELECT id, updatedate FROM dbupdates WHERE updatestatus=".$statusNew." ORDER BY id DESC LIMIT 1";
	$sth = $dbh->prepare($queryUpdates);
	$sth->execute();
	@updates = $sth->fetchrow_array();
	if(@updates)
	{
		print "\nDataBase Update is ready ".$updates[1];
	}
	$sth->finish();
}

sub GenerateQsubScripts
{

}

sub CopySlaveloop
{
	
}
