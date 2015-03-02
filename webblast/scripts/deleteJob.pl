#!/usr/bin/perl
use DBI;
use Config::IniFiles;

$jobid = $ARGV[0];
$configFilePath = $ARGV[1];

#Load configuration from ini file
$cfg = new Config::IniFiles( -file => $configFilePath );

$sqlUser = $cfg->val('sql_config','sql_user');
$sqlPass = $cfg->val('sql_config','sql_pass');
$database = $cfg->val('sql_config','sql_database');
$sqlHost = $cfg->val('sql_config','sql_host');


$dbhp = DBI->connect("DBI:mysql:".$database.":".$sqlHost,$sqlUser,$sqlPass,{RaiseError => 1});

$rowsAffected=1;
print "starting loop";
while($rowsAffected > 0)
{
	$queryDeleteQueries="DELETE FROM blast_queries WHERE jobid=".$jobid." LIMIT 2000";
	$sthp = $dbhp->prepare($queryDeleteQueries);
	$rowsAffected = $sthp->execute();
	sleep 2;
}

#$queryDeleteJob="DELETE FROM blast_jobs WHERE id=$jobid";
#$sthp = $dbhp->prepare($queryDeleteJob);
#$sthp->execute();

$sthp->finish();
#$dbhp->disconnect();


