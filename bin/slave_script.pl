#!/usr/bin/env perl
#Program to start a blasting mysql submissions on slave nodes
#Results will be stored in CSV and -m format chosen by the user
#############################################################

#setup the environment paths to the blast binary files and search databases
###########################################################################

use Bio::Perl;
use Bio::Tools::Run::StandAloneBlast;
use Bio::SearchIO;
use DBI;
use Sys::Hostname;
use File::Path;
use Config::IniFiles;

#NEED TO CHANGE MANUALLY
############################

$configFilePath = $ARGV[0];
#$configFilePath = "/export/home/blastweb/webblast.conf";

#Load configuration from ini file
$cfg = new Config::IniFiles( -file => $configFilePath );

$sqlUser = $cfg->val('sql_config','sql_user');
$sqlPass = $cfg->val('sql_config','sql_pass');
$database = $cfg->val('sql_config','sql_database');
$sqlHost = $cfg->val('sql_config','sql_host');
$sqlPort = $cfg->val('sql_config','sql_port');

$blastDir=$cfg->val('blast_bin','blast_bin_path');
$blastDataDir=$cfg->val('slave_paths','databases_path');

$queryPath      = $cfg->val('slave_paths','query_chunks_path');
$resultsPath    = $cfg->val('slave_paths','result_chunks_path');
$csvResultsPath = $cfg->val('slave_paths','csv_chunks_path');

$cpuCores = $cfg->val('node_settings','num_cpu_cores');

print $sqlHost;

#Node process info
##################
$nodeNumber = $ENV{'BLAST_NODE_ID'};
$jobId = $ENV{'PBS_JOBID'};
$nodePid=substr($jobId, 0, index($jobId, '.'));
$numForks=0;

#Static status and command variables
####################################
$startCommand=2;
$statusNew=1;
$statusRunning=2;
$statusCompleted=3;
$statusError=7;
$statusPause=8;

#Settings
#########
$sleepTime = 30;


#Setup the database connection
##############################
$dbhp = DBI->connect("DBI:mysql:".$database.":".$sqlHost.":".$sqlPort,$sqlUser,$sqlPass,{RaiseError => 1});
$dbhp->{mysql_auto_reconnect} = 1;

#Get Slave node  host name
##########################
$hostname=hostname();
$remove=".local";
$hostname=~s/$remove//g;

#Set node Status to running
##########################
SetNodeRunning();

#Start forking job loops to each CPU
####################################
StartForking($cpuCores);

#Set node status to stopped
###########################
SetNodeStopped();

#Close database connections when done
#####################################
$sthp->finish();
$dbhp->disconnect();


#Subroutines

#Generates a blast command string from a parameter array
########################################################
sub GenerateBlastCommand
{
	(%paramsAvail,$paramsSet) = @_;

	$blastCommand=$blastDir."blastall ";
	
	@paramSetArr=split(' ',$paramSet);
	
	foreach my $paramSet (@paramSetArr)
	{
		if(length($paramSet)==2)
		{
			chop($paramSet);
		}
		$blastCommand=$blastCommand."-".$paramSet." ".$paramsAvail{$paramSet}." ";
	}

	return $blastCommand;
					
}

#Check whether a node should be started or shutdown
###################################################
sub CheckNodeStartCommand
{
	my $dbhc 		= shift;
	$queryNodeInfo="SELECT commandid FROM nodes WHERE id=$nodeNumber AND pid=$nodePid";
	print $queryNodeInfo;
        $sthc = $dbhc->prepare($queryNodeInfo);
        $sthc->execute();
        if(@nodeInfoChild=$sthc->fetchrow_array())
	{	
		if($nodeInfoChild[0] == $startCommand)
		{
			print "\nNode set to start";
			return 1;
		}else{
			print "\nNode set to stop";
			return 0;
		}
	}else{
		print "\ncould not fetch node";
		return 0;
	}
}

#Start executing the blast job
##############################
sub RunBlastJob {
	my $dbhc                = shift;
	#initialize blastdata to empty string
	$blastData = "";

	#Reserve a query for this process
	$queryReserveQuery ="UPDATE blast_queries SET reservenode=$nodeNumber, reservepid=$$ WHERE statusid=$statusNew AND reservenode=0 AND reservepid=0 ORDER BY priority DESC,id ASC LIMIT 1";
	$sthc         = $dbhc->prepare($queryReserveQuery);
	$rowsAffected = $sthc->execute();
	if ( $rowsAffected > 0 ) {

		#Select reserved query for this process
		$queryNextBlastQuery ="SELECT q.id,j.id,j.paramsenabled,b.command,d.dbname,d.dbpath,j.e,j.m,j.FU,j.GU,j.EU,j.XU,j.IU,j.q,j.r,j.v,j.b,j.f,j.g,j.QU,j.DU,j.a,j.CU,j.JU,j.MU,j.WU,j.z,j.KU,j.YU,j.SU,j.l,j.UU,j.y,j.ZU,j.n,j.AU,j.w,j.t,j.paramsenabled,q.chunksize,d.userid FROM blast_queries q,blasts b,dbs d, blast_jobs j WHERE b.id=j.blastid AND d.id=j.dbid AND j.id=q.jobid AND q.statusid=$statusNew AND j.status=$statusNew AND q.reservenode=$nodeNumber AND q.reservepid=$$";
		$sthcQueries = $dbhc->prepare($queryNextBlastQuery);
		$sthcQueries->execute();
		
		$dbDirType = "";
		#Begin looping over all reserved queries
		while ( @nextBlastQuery = $sthcQueries->fetchrow_array() ) {
			if($nextBlastQuery[40]>0)
			{	
				$dbDirType = "USER/";
			}
			else
			{
				$dbDirType = "NCBI/";
			}
			$outfile =
			    $resultsPath
			  . $nextBlastQuery[1] . "/"
			  . $nextBlastQuery[0]
			  . ".result";
			$csvoutfile =
			    $csvResultsPath
			  . $nextBlastQuery[1] . "/"
			  . $nextBlastQuery[0] . ".csv";

			%paramsAvailable = (
				"p",
				$nextBlastQuery[3],
				"d",
				$blastDataDir . $dbDirType . $nextBlastQuery[4],
				"i",
				$queryPath
				  . $nextBlastQuery[1] . "/"
				  . $nextBlastQuery[0]
				  . ".fasta",
				"o",
				$outfile,
				"e",
				$nextBlastQuery[6],
				"m",
				$nextBlastQuery[7],
				"F",
				$nextBlastQuery[8],
				"G",
				$nextBlastQuery[9],
				"E",
				$nextBlastQuery[10],
				"X",
				$nextBlastQuery[11],
				"I",
				$nextBlastQuery[12],
				"q",
				$nextBlastQuery[13],
				"r",
				$nextBlastQuery[14],
				"v",
				$nextBlastQuery[15],
				"b",
				$nextBlastQuery[16],
				"f",
				$nextBlastQuery[17],
				"g",
				$nextBlastQuery[18],
				"Q",
				$nextBlastQuery[19],
				"D",
				$nextBlastQuery[20],
				"a",
				$nextBlastQuery[21],
				"C",
				$nextBlastQuery[22],
				"J",
				$nextBlastQuery[23],
				"M",
				$nextBlastQuery[24],
				"W",
				$nextBlastQuery[25],
				"z",
				$nextBlastQuery[26],
				"K",
				$nextBlastQuery[27],
				"Y",
				$nextBlastQuery[28],
				"S",
				$nextBlastQuery[29],
				"l",
				$nextBlastQuery[30],
				"U",
				$nextBlastQuery[31],
				"y",
				$nextBlastQuery[32],
				"Z",
				$nextBlastQuery[33],
				"n",
				$nextBlastQuery[34],
				"A",
				$nextBlastQuery[35],
				"w",
				$nextBlastQuery[36],
				"t",
				$nextBlastQuery[37]
			);
			$paramSet     = $nextBlastQuery[38];
			$commandToRun = GenerateBlastCommand( %paramsAvailable, $paramSet );

			$queryStatus = 0;

			#Set query status to running so no other node picks it up
			$queryUpdateStatus ="UPDATE blast_queries SET statusid=$statusRunning, starttime=NOW() WHERE id=$nextBlastQuery[0]";
			$sthc = $dbhc->prepare($queryUpdateStatus);
			$sthc->execute();
			print "\nUpdate query status $nextBlastQuery[0] Running on child $$";

			#Run blastall command
			print "Running command: $commandToRun";
			$processStartTime = time;
			$status = `$commandToRun`;
			$processRunTime = time - $processStartTime;

			#if process only ran for 5 seconds or less then sleep a random integer beteween 0 and 10 to prevent all nodes
			#from querying the database too fast and crashing it	
			if($processRunTime < 5)
			{
				sleep(int(rand(10)));
			}

			#Check command status
			if ( $? != 0 ) {
				$error = "Command: $commandToRun\nFailed to execute: $status \n";

				$queryStatus = $statusError;

				open( ERRORFILE, '>>'.$outfile);
						print ERRORFILE $error;
						close (ERRORFILE);
					}else{
						$queryStatus=$statusCompleted;
					        		
                                                #Read parsed blast_report
						$blast_report = new Bio::SearchIO(-format=>'blast', -file => $outfile );
					
						#submit to database if parsed results are available
						open (CSVRESULTFILE, '>'.$csvoutfile);
						$csvResults="";
						eval {
							while($result=$blast_report->next_result)
							{	
								$csvResults = $csvResults."\"".$result->query_name()."\",\"".$result->query_accession()."\",\"".$result->query_length()."\",\"".$result->database_letters()."\",\"".$result->database_entries()."\",\"".$result->num_hits()."\"";
	        						$queryDescription=$result->query_description();
	        						$queryDescription=~s/"/""/g;
	        						$csvResults=$csvResults.",\"".$queryDescription."\"";
	
        							while( my $hit = $result->next_hit() ) {
                							$csvResults = $csvResults.",\"".$hit->name()."\",\"".$hit->length()."\",\"".$hit->accession()."\",\"".$hit->algorithm()."\",\"".$hit->raw_score()."\",\"".$hit->significance()."\",\"".$hit->bits()."\",\"".$hit->num_hsps()."\",\"".$hit->locus()."\"";
                							if(my $hsp = $hit->next_hsp())
                							{
                        							$csvResults=$csvResults.",\"".(($hit->frame() + 1) * $hsp->strand("hit"))."\",\"".$hsp->strand("query")."\",\"".$hsp->strand("hit")."\",\"".$hsp->percent_identity()."\",\"".($hsp->num_conserved()/$hsp->hsp_length())."\",\"".$hsp->gaps()."\",\"".$hsp->start("query")."\",\"".$hsp->end("query")."\",\"".$hsp->start("hit")."\",\"".$hsp->end("hit")."\"";
                							}
                							$hitDescription=$hit->description;
                							$hitDescription=~s/"/""/g;
                							$csvResults=$csvResults.",\"".$hitDescription."\"";
									$taxon="";
									if($hitDescription =~ m/[\[](.*?)[\]]/)
									{
        									$taxon = $1;
									}
									$csvResults=$csvResults.",\"".$taxon."\"";
        							}
        							$csvResults=$csvResults."\n";
							}
						};
						if($@)
						{
							$csvResults=$@;
						}
						print CSVRESULTFILE $csvResults;
                                                close (CSVRESULTFILE);
						
					}
					
						#Update the blast query status to queryStatus variable
						$queryCompleteStatus="UPDATE blast_queries SET statusid=$queryStatus, endtime=NOW() WHERE id=$nextBlastQuery[0]";
						$sthc= $dbhc->prepare($queryCompleteStatus);
						$rowsAffected = $sthc->execute();
					
						#Update the blast job progress
						$queryJobComplete="UPDATE blast_jobs SET queriescompleted=queriescompleted+$nextBlastQuery[39] WHERE id=$nextBlastQuery[1]";
						$sthc=$dbhc->prepare($queryJobComplete);
						$rowsAffected = $sthc->execute();
				}
			}else{
				#If no reserved jobs are availabe then sleep for sleepTime variable seconds
				print "Sleeping $sleepTime seconds";
				sleep($sleepTime);
			}
}

#Start forking loops to each CPU where the jobs are run
#######################################################
sub StartForking {
	my $numCPUs                = shift;

	#Start a forked process for each CPU on the node
	print "starting to fork";
	for ( 1 .. $numCPUs ) {

		#create a fork
		my $pid = fork();
		if ($pid) {

			#parent code
			push( @childs, $pid );
		}
		elsif ( $pid == 0 ) {

			#child process
			print "starting child process = $$ \n";
			my $forkStatus = 1;
			$numForks++;
			my $nodeStartCommand;
			#create a mysql connection to server
			my $dbhc = DBI->connect("DBI:mysql:".$database.":".$sqlHost,$sqlUser,$sqlPass,{RaiseError => 1});
			$dbhc->{mysql_auto_reconnect} = 1;
			$queryAddCPUToNode= "UPDATE nodes SET processes=1+processes WHERE id=$nodeNumber";
			$sthc = $dbhc->prepare($queryAddCPUToNode);
			$sthc->execute();

			while($numForks > 0)
			{
				$nodeStartCommand = CheckNodeStartCommand($dbhc);
				#if node status is set to stop wait for all forks to be ready before killing the nodes
				#otherwise you have rogue blast jobs everywehre, also allows you to restart a node that was marked stop
				if($nodeStartCommand)
				{
					if($forkStatus==0)
					{
						$forkStatus=1;
						$numForks++;
					}
					eval {
                                        	#Process to run
                                        	RunBlastJob($dbhc);
                                	};
                                	if($@)
                                	{
                                	        print $@;
                                	}
				}else{
					if($forkStatus==1)
                                        {
                                                $forkStatus=0;
                                                $numForks--;
                                        }else{
						sleep(int(rand(10)));	
					}
				}
			}
			#Loop is over remove process from node
			$queryRemoveCPUFromNode= "UPDATE nodes SET processes=processes-1 WHERE id=$nodeNumber";
			unlink($newRawBlastFile);
			$sthc->finish();
			$dbhc->disconnect();
			exit(0);
		}else{
			die "Couldn\'t fork: $!\n";
		}
	}
	#Wait for children to finish before closing the parent process to prevent brain eating zombies
	foreach(@childs){
		waitpid($_,0);
	}
}

#Set the node status to running in the database
###############################################
sub SetNodeRunning {
	#Update the node status to running
	$queryUpdateStatusRunning ="UPDATE nodes SET status=2,processes=0,hostname=\'$hostname\',pid=$nodePid  WHERE id=$nodeNumber";
	print $queryUpdateStatusRunning;
	$sthp = $dbhp->prepare($queryUpdateStatusRunning);
	$sthp->execute();
	print "\nNode number ".$nodeNumber." started\n";
}

#Set the node status to stopped in the database
###############################################
sub SetNodeStopped {
	#update the node status to stopped
	$queryDeleteStoppedNode ="DELETE FROM nodes WHERE id=$nodeNumber";
	print "\ndelete node number ".$nodeNumber."\n";
	$sthp = $dbhp->prepare($queryDeleteStoppedNode);
	$sthp->execute();
}
