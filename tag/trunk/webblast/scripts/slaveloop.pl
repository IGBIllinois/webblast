#!/usr/bin/perl
#Program to start a blasting mysql submissions on slave nodes
#Results will be stored in CSV and -m format chosen by the user
#############################################################

#setup the environment paths to the blast binary files and search databases
###########################################################################
BEGIN {
        $ENV{BLASTDIR} = '/opt/Bio/ncbi/bin';
        $ENV{BLASTDATADIR} = '/state/partition1/blastdbs';
}


use lib "/opt/rocks/lib/perl5/site_perl/5.8.8";
use Bio::Perl;
use Bio::Tools::Run::StandAloneBlast;
use Bio::SearchIO;
use DBI;
use Sys::Hostname;
use File::Path;

#static values on slavenodes
############################
$nodeNumber = $ARGV[0];
$sqlUser = $ARGV[1];
$sqlPass = $ARGV[2];
$database = $ARGV[3];

$nodePid=$$;
$blastDir='/opt/Bio/ncbi/bin/';
$blastDataDir='/state/partition1/blastdbs/';
$headNode='clcluster';

#Static status and command variables
####################################
$startCommand=2;
$statusNew=1;
$statusRunning=2;
$statusCompleted=3;
$statusError=7;

#Settings
#########
$numCPUs = 4;
$sleepTime = 30;


#Config paths
#############
$queryPath;
$resultsPath;
$csvResultsPath;

#Setup the database connection
##############################
$dbhp = DBI->connect("DBI:mysql:".$database.":".$headNode,$sqlUser,$sqlPass,{RaiseError => 1});
$dbhp->{mysql_auto_reconnect} = 1;

#Get Configuration paths from database
######################################
GetConfigPaths();

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
StartForking($numCPUs);

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
        $sthc = $dbhc->prepare($queryNodeInfo);
        $sthc->execute();
        if(@nodeInfoChild=$sthc->fetchrow_array())
	{	
		if($nodeInfoChild[0] == $startCommand)
		{
			return 1;
		}else{
			return 0;
		}
	}else{
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
	$queryReserveQuery ="UPDATE blast_queries SET reservenode=$nodeNumber, reservepid=$$ WHERE statusid=$statusNew AND reservenode=0 AND reservepid=0 LIMIT 1";
	$sthc         = $dbhc->prepare($queryReserveQuery);
	$rowsAffected = $sthc->execute();
	if ( $rowsAffected > 0 ) {

		#Select reserved query for this process
		$queryNextBlastQuery ="SELECT q.id,j.id,j.paramsenabled,b.command,d.dbname,d.dbpath,j.e,j.m,j.FU,j.GU,j.EU,j.XU,j.IU,j.q,j.r,j.v,j.b,j.f,j.g,j.QU,j.DU,j.a,j.CU,j.JU,j.MU,j.WU,j.z,j.KU,j.YU,j.SU,j.l,j.UU,j.y,j.ZU,j.n,j.AU,j.w,j.t,j.paramsenabled,q.chunksize FROM blast_queries q,blasts b,dbs d, blast_jobs j WHERE b.id=j.blastid AND d.id=j.dbid AND j.id=q.jobid AND q.statusid=$statusNew AND j.status=$statusNew AND q.reservenode=$nodeNumber AND q.reservepid=$$";
		$sthcQueries = $dbhc->prepare($queryNextBlastQuery);
		$sthcQueries->execute();
		
		#Begin looping over all reserved queries
		while ( @nextBlastQuery = $sthcQueries->fetchrow_array() ) {

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
				$blastDataDir . $nextBlastQuery[4],
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
			$status = `$commandToRun`;

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

#Get Configuration paths from the database
##########################################
sub GetConfigPaths {
	$queryConfigPaths ="SELECT resultspath, queriespath,csvresultspath FROM config ORDER BY id DESC LIMIT 1";
	$sthp = $dbhp->prepare($queryConfigPaths);
	$sthp->execute();
	@configPaths   = $sthp->fetchrow_array();
	$queryPath      = $configPaths[1];
	$resultsPath    = $configPaths[0];
	$csvResultsPath = $configPaths[2];
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

			#create a mysql connection to server
			my $dbhc = DBI->connect("DBI:mysql:".$database.":clcluster.igb.uiuc.edu",$sqlUser,$sqlPass,{RaiseError => 1});
			$dbhc->{mysql_auto_reconnect} = 1;
			$queryAddCPUToNode= "UPDATE nodes SET processes=1+processes WHERE id=$nodeNumber";
			$sthc = $dbhc->prepare($queryAddCPUToNode);
			$sthc->execute();

			while(CheckNodeStartCommand($dbhc))
			{
				eval {
					#Process to run
					RunBlastJob($dbhc);
				};
				if($@)
				{
					print $@;
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
	$queryUpdateStatusRunning ="UPDATE nodes SET status=2,processes=0, hostname=\'$hostname\', pid=$$ WHERE id=$nodeNumber";
	$sthp = $dbhp->prepare($queryUpdateStatusRunning);
	$sthp->execute();
}

#Set the node status to stopped in the database
###############################################
sub SetNodeStopped {
	#update the node status to stopped
	$queryUpdateStatusStopped =
	  "UPDATE nodes SET status=5, hostname=\'\', pid=0 WHERE id=$nodeNumber";
	$sthp = $dbhp->prepare($queryUpdateStatusStopped);
	$sthp->execute();
}
