#!/usr/bin/perl
use DBI;

$jobid = $ARGV[0];
$sqlUser = $ARGV[1];
$sqlPass = $ARGV[2];
$database = $ARGV[3];

$dbhp = DBI->connect("DBI:mysql:".$database.":localhost",$sqlUser,$sqlPass,{RaiseError => 1});

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


