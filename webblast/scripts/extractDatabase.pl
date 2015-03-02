#!/usr/bin/perl

use lib "/opt/rocks/lib/perl5/site_perl/5.8.8";
use DBI;

$fileName = $ARGV[0];
$dbid = $ARGV[1];
$sqlUser = $ARGV[2];
$sqlPass = $ARGV[3];
$sqlDatabase = $ARGV[4];
$sqlHost = $ARGV[5];

$statusCompleted=3;

$dbhp = DBI->connect("DBI:mysql:".$sqlDatabase.":".$sqlHost,$sqlUser,$sqlPass,{RaiseError => 1});
$dbhp->{mysql_auto_reconnect} = 1;

$queryConfigPaths = "SELECT userdbspath FROM config ORDER BY id DESC LIMIT 1";
$sthp=$dbhp->prepare($queryConfigPaths);
$sthp->execute();
@configPaths = $sthp->fetchrow_array();
$userDbsPath=$configPaths[0];

$fileNameExt = $fileName;
$ext = ($fileNameExt =~ m/([^.]+)$/)[0];

if($ext == "tar" || $ext == "gz" || $ext == "tar\.gz")
{
	system("tar xzvf ".$fileName." -C ".$userDbsPath);
	print "tar xzvf ".$fileName." -C ".$userDbsPath;
}

if($ext == "zip")
{
	system("unzip ".$filename." -d ".$userDbsPath);
	print "unzip ".$filename." -d ".$userDbsPath;
}

system(rm -f $fileName);

for($count = 0; $count <24 ; $count++)
{
        system("rsync -au /export/home/blastdbs/userdbs/* compute-0-$count:/state/partition1/blastdbs/");
}

$queryUpdateToActive = "UPDATE dbs SET active=1 WHERE id=".$dbid;
$sthp=$dbhp->prepare($queryUpdateToActive);
$sthp->execute();


