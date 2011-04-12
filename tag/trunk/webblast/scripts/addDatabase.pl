#!/usr/bin/perl
use DBI;
use Cwd;

$dbName = $ARGV[0];
$dbTitle = $ARGV[1];
$dbType = $ARGV[2];
$dbFile = $ARGV[3];
$dbSeqId = $ARGV[4];
$dbASNFormat = $ARGV[5];
$dbASNMode = $ARGV[6];
$dbInSeqEntry = $ARGV[7];
$dbCreateIndexes = $ARGV[8];
$dbTaxonomicInfo = $ARGV[9];

$sqlUser = $ARGV[10];
$sqlPass = $ARGV[11];
$database = $ARGV[12];

$userid = $ARGV[13];

$formatdbPath ="/opt/Bio/ncbi/bin/formatdb"; 

$dbhp = DBI->connect("DBI:mysql:".$database.":localhost",$sqlUser,$sqlPass,{RaiseError => 1});

$queryConfigPaths = "SELECT userdbspath, taxonomypath, dbvolumesize, fromemail FROM config ORDER BY id DESC LIMIT 1";
$sthp = $dbhp->prepare($queryConfigPaths);
$sthp->execute();
@configPaths = $sthp->fetchrow_array();

$userDbsPath = $configPaths[0];
$taxonomyPath = $configPaths[1];
$dbVolumeSize = $configPaths[2];
$fromEmail = $configPaths[3];

print $dbVolumeSize;

$queryUsersEmail = "SELECT email FROM users WHERE id=".$userid;
$sthp = $dbhp->prepare($queryUsersEmail);
$sthp->execute();
@usersEmail = $sthp->fetchrow_array();
$userEmail = $usersEmail[0];

$command = CreateCommandString();

print $command;
$errorCode = system($command);
print "\nErrorCode(".$errorCode.")";
if($errorCode != 0)
{
	sendEmail($userEmail, $fromEmail, "IGB BLAST (new DB failed)","Failed to create new database using formatdb\n".$command);
}
else
{
	unlink($dbFile);
	$dbStatusUploaded = 2;
	$dbTypeLetter = "n";
	if($dbType eq "T")
	{
		$dbTypeLetter = "p";
	}
	$queryInsertDB = "INSERT INTO dbs (dbname, type, description, active, userid)VALUES(\"".$dbName."\",\"".$dbTypeLetter."\",\"".$dbTitle."\",".$dbStatusUploaded.",".$userid.")";
	$sthp = $dbhp->prepare($queryInsertDB);
	$sthp->execute();
}


$sthp->finish();
$dbhp->disconnect();


#Subroutines
###############################################################################################################################
sub CreateCommandString
{
	$shellCommand = $formatdbPath." -i ".$dbFile." -p ".$dbType." -o ".$dbSeqId." -a ".$dbASNFormat." -v ".$dbVolumeSize." -b ".$dbASNMode." -e ".$dbInSeqEntry." -s ".$dbCreateIndexes." -t \"".$dbTitle."\"";
	if($dbTaxonomicInfo ne 0)
	{
		$shellCommand .= " -T ".$taxonomyPath.$dbTaxonomicInfo;
	}
	
	return $shellCommand
}

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
