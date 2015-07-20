#!/usr/bin/perl

use DBI;
use File::Slurp;
use Config::IniFiles;

$jobid = $ARGV[0];
$netid = $ARGV[1];
$sourcePath = $ARGV[2];
$destinationPath = $ARGV[3];
$ext = $ARGV[4];
$configFilePath = $ARGV[5];


print $configFilePath;
#Load configuration from ini file
$cfg = new Config::IniFiles( -file => $configFilePath );

$sqlUser = $cfg->val('sql_config','sql_user');
$sqlPass = $cfg->val('sql_config','sql_pass');
$database = $cfg->val('sql_config','sql_database');
$sqlHost = $cfg->val('sql_config','sql_host');

print $database;

$maxBuffer=1;

$statusCompleted=3;

$dbhp = DBI->connect("DBI:mysql:".$database.":".$sqlHost,$sqlUser,$sqlPass,{RaiseError => 1});

$queryUpdateConcatPid="UPDATE blast_jobs SET concatpid".$ext."=".$$." WHERE id=".$jobid;
$sthp = $dbhp->prepare($queryUpdateConcatPid);
$sthp->execute();

$queryQueryResultsId="SELECT id FROM blast_queries WHERE jobid=$jobid AND statusid=$statusCompleted";
$sthp = $dbhp->prepare($queryQueryResultsId);
$sthp->execute();

$bufferCount=0;
$lineBuffer="";
@lines=();
overwrite_file($destinationPath.$netid."_".$jobid.".".$ext,"");

while(@arrayQueryResultsId=$sthp->fetchrow_array())
{
	#If buffer size have been reached append lines to file and reset the buffer counter
	#if($bufferCount>=$maxBuffer)
	#{
	#	append_file($destinationPath.$netid."_".$jobid.".".$ext, @lines);
        #        @lines=();
        #        $bufferCount=0
	#}
	#Add lines to buffer
	#@lines= (@lines,read_file($sourcePath.$jobid."/".$arrayQueryResultsId[0].".".$ext, err_mode => 'quiet'));
	#$bufferCount++;
	print "cat ". $sourcePath.$jobid."/".$arrayQueryResultsId[0].".".$ext." >> ". $destinationPath.$netid."_".$jobid.".".$ext;
	system("cat ". $sourcePath.$jobid."/".$arrayQueryResultsId[0].".".$ext." >> ". $destinationPath.$netid."_".$jobid.".".$ext);
}
append_file($destinationPath.$netid."_".$jobid.".".$ext, @lines);
$sthp->finish();
$dbhp->disconnect();
