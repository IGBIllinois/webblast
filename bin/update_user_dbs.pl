#!/usr/bin/perl
use DBI;
use Cwd;


#SQL authentication information
###############################
$sqlUser = blastWeb;
$sqlPass = igb123;
$sqlDatabase = blastWeb;
$sqlHost = "biocluster.igb.illinois.edu";

#Static values
#####################################
$slaveNodeNamePattern = "compute-0-";
$numberOfNodes = 23;
$slaveNodeDbsPath = "/state/partition1/blastdbs/";
$errorCode = 0;
$errorString = "";

$dbhp = DBI->connect("DBI:mysql:".$sqlDatabase.":".$sqlHost,$sqlUser,$sqlPass,{RaiseError => 1});
$dbhp->{mysql_auto_reconnect} = 1;

$queryConfigPaths = "SELECT userdbspath, fromemail FROM config ORDER BY id DESC LIMIT 1";
$sthp = $dbhp->prepare($queryConfigPaths);
$sthp->execute();
@configPaths = $sthp->fetchrow_array();

$userDbsPath = $configPaths[0];
$fromEmail = $configPaths[1];



#Get all databases that were just uploaded and converted using formatdb successfully.
#Then rsync them into the slave nodes to slaveNodeDbsPath.
#Once they are rsynced correctly update the database to active and e-mail the user who uploaded it.
###################################################################################################
$statusUploaded = 2;
$queryUploadedDbNames = "SELECT d.id, d.dbname, d.userid, d.description, u.email FROM dbs d, users u WHERE u.id=d.userid AND active=".$statusUploaded;
$sthp = $dbhp->prepare($queryUploadedDbNames);
$sthp->execute();

while(@uploadedDbNames = $sthp->fetchrow_array())
{	
	$errorCode=0;
	$dbid = $uploadedDbNames[0];
	$dbName = $uploadedDbNames[1];
	$userid = $uploadedDbNames[2];
	$description = $uploadedDbNames[3];
	$userEmail = $uploadedDbNames[4];

	$statusTransfering = 3;
	$queryUpdateDB = "UPDATE dbs SET active=".$statusTransfering." WHERE id=".$dbid;
        $sthl = $dbhp->prepare($queryUpdateDB);
        $sthl->execute();

	for($nodeNumber = 0; $nodeNumber <=$numberOfNodes ; $nodeNumber++)
	{
	        if(system("rsync -au ".$userDbsPath.$dbName.".* ".$slaveNodeNamePattern.$nodeNumber.":".$slaveNodeDbsPath)!=0)
		{
			$errorCode=1;
			$errorString = $errorString."Rsync compute-0-".$nodeNumber." failed.\n";
		}
		print "\nrsync -au ".$userDbsPath.$dbName.".* ".$slaveNodeNamePattern.$nodeNumber.":".$slaveNodeDbsPath;
	        
	}
	
	if($errorCode != 0)
	{
		sendEmail($userEmail, $fromEmail, "IGB BLAST (new DB failed)","Failed to synchronize database with slave node\nPlease contact ".$fromEmail);
		sendEmail($fromEmail, $fromEmail, "IGB BLAST (new DB failed)","Failed to synchronize database with slave node\nFor". $dbName." (database id ".$dbid.") error message sent to ".$userEmail."\n".$errorString);
		$statusError = 4;
		$queryUpdateDB = "UPDATE dbs SET active=".$statusError." WHERE id=".$dbid;
	        $sthl = $dbhp->prepare($queryUpdateDB);
	        $sthl->execute();


	}
	else
	{
		$statusActive = 1;
                $queryUpdateDB = "UPDATE dbs SET active=".$statusActive." WHERE id=".$dbid;
                $sthl = $dbhp->prepare($queryUpdateDB);
                $sthl->execute();
		sendEmail($userEmail, $fromEmail, "IGB BLAST (new DB installed successfully)","Your database ".$description." (".$dbName.") is now active on the IGB BLAST\nFor problems please contact IGB helpdesk ".$fromEmail);

	}
}




#Get all databases marked as deleted.
#Remove the database from userdbs on the head node.
#Remove the database from the slave nodes.
######################################################################################################
$statusDeleting= 5;
$queryDeletedDbNames = "SELECT id, dbname, userid, description FROM dbs WHERE active=".$statusDeleting;
$sthp = $dbhp->prepare($queryDeletedDbNames);
$sthp->execute();

print "\nstarting deleted files loop";
while(@deletedDbNames = $sthp->fetchrow_array())
{
	$errorCode = 0;
	$dbid = $deletedDbNames[0];
	$dbName = $deletedDbNames[1];
	$userid = $deletedDbNames[2];
	$description = $deletedDbNames[3];
	print "Data base name".$dbName;
	if($dbName ne "")
	{
		print "\nrm -f ".$userDbsPath.$dbName.".*\n";
		$errorCode = system("rm -f ".$userDbsPath.$dbName.".*");
		print "\nError code ".$errorCode;
		if($errorCode == 0)
		{
			system("cluster-fork --bg rm -f ".$slaveNodeDbsPath.$dbName.".*");
		}
		else
		{
			$errorCode=1;
		}
		
	}
	else
	{
		$errorCode = 1;
	}

	if($errorCode)
	{
		sendEmail($fromEmail, $fromEmail, "IGB BLAST failed to delete ".$dbName, "Error deleting ".$dbName);
	}
	else
	{
		$statusDeleted = 6;
                $queryUpdateDB = "UPDATE dbs SET active=".$statusDeleted." WHERE id=".$dbid;
                $sthl = $dbhp->prepare($queryUpdateDB);
                $sthl->execute();
	}
}




$sthp->finish();
$dbhp->disconnect();

#simple Email Function
# ($to, $from, $subject, $message)
sub sendEmail
{
        my $to = shift;
        my $from = shift;
        my $subject = shift;
        my $message = shift;
        my $sendmail = '/usr/sbin/sendmail -t';

        open(MAIL, "|$sendmail") or die "Cannot open $sendmail: $!";;
        print MAIL "From: $from\n";
        print MAIL "To: $to\n";
        print MAIL "Subject: $subject\n\n";
        print MAIL "$message\n";
        close(MAIL);

}
