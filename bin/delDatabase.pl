#!/usr/bin/perl
use DBI;
use Cwd;
use File::Basename;
use Config::IniFiles;

#get all the required inputs
$dbid = $ARGV[0]
$userid = $ARGV[1];
$configFilePath = $ARGV[2];

#Load configuration from ini file
$cfg = new Config::IniFiles( -file => $configFilePath );
$sqlUser = $cfg->val('sql_config','sql_user');
$sqlPass = $cfg->val('sql_config','sql_pass');
$database = $cfg->val('sql_config','sql_database');
$sqlHost = $cfg->val('sql_config','sql_host');
$databasesPath = $cfg->val('head_paths','databases_path');

#connect to mysql database
$dbhp = DBI->connect("DBI:mysql:".$database.":".$sqlHost,$sqlUser,$sqlPass,{RaiseError => 1});

#get email config
$queryDatabasesName = "SELECT dbname FROM dbs WHERE userid=".$userid." AND id=".$dbid.";
$sthp = $dbhp->prepare($queryDatabasesName);
$sthp->execute();
@databasesName = $sthp->fetchrow_array();
$databaseName = $datbasesName[0];

if($databaseName && $databasesPath)
{
	#system("rm -rf ".$databasesPath.$databasesName."*");
	print "rm -rf ".$databasesPath.$databasesName."*"
}

$sthp->finish();
$dbhp->disconnect();
