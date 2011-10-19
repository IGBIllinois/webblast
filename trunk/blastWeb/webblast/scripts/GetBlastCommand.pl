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
$jobidSelected = $ARGV[0];
$sqlUser="testuser";
$sqlPass="israel123";
$database = "blastWeb";

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
RunBlastJob();

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

#Start executing the blast job
##############################
sub RunBlastJob {
	#initialize blastdata to empty string
	$blastData = "";

		#Select reserved query for this process
		$queryNextBlastQuery ="SELECT q.id,j.id,j.paramsenabled,b.command,d.dbname,d.dbpath,j.e,j.m,j.FU,j.GU,j.EU,j.XU,j.IU,j.q,j.r,j.v,j.b,j.f,j.g,j.QU,j.DU,j.a,j.CU,j.JU,j.MU,j.WU,j.z,j.KU,j.YU,j.SU,j.l,j.UU,j.y,j.ZU,j.n,j.AU,j.w,j.t,j.paramsenabled,q.chunksize FROM blast_queries q,blasts b,dbs d, blast_jobs j WHERE b.id=j.blastid AND d.id=j.dbid AND j.id=q.jobid AND j.id=".$jobidSelected;

		$sthcQueries = $dbhp->prepare($queryNextBlastQuery);
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
			print "\n".$commandToRun."\n";
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
