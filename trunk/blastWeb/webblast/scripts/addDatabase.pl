#!/usr/bin/perl
use DBI;
use Cwd;
use File::Basename;
use Config::IniFiles;

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
$userid = $ARGV[10];
$configFilePath = $ARGV[11];

#Load configuration from ini file
$cfg = new Config::IniFiles( -file => $configFilePath );

$sqlUser = $cfg->val('sql_config','sql_user');
$sqlPass = $cfg->val('sql_config','sql_pass');
$database = $cfg->val('sql_config','sql_database');
$sqlHost = $cfg->val('sql_config','sql_host')
$userDbsPath = $cfg->val('head_paths','databases_path');
$taxonomyPath = $cfg->val('head_paths','taxonomy_path');;

($filename,$userDbsPath) = fileparse($dbFile);
$formatdbPath ="/opt/Bio/ncbi/bin/formatdb"; 

$dbhp = DBI->connect("DBI:mysql:".$database.":".$sqlHost,$sqlUser,$sqlPass,{RaiseError => 1});

$queryConfigPaths = "SELECT dbvolumesize, fromemail FROM config ORDER BY id DESC LIMIT 1";
$sthp = $dbhp->prepare($queryConfigPaths);
$sthp->execute();
@configPaths = $sthp->fetchrow_array();
$dbVolumeSize = $configPaths[0];
$fromEmail = $configPaths[1];

print $dbVolumeSize;

$queryUsersEmail = "SELECT email FROM users WHERE id=".$userid;
$sthp = $dbhp->prepare($queryUsersEmail);
$sthp->execute();
@usersEmail = $sthp->fetchrow_array();
$userEmail = $usersEmail[0];
chdir($userDbsPath);
$command = CreateCommandString();

print $command;
$errorCode = system($command);
print "\nErrorCode(".$errorCode.")";
if($errorCode != 0)
{
	sendEmail($userEmail, $fromEmail, "IGB BLAST (new DB failed)","Failed to create new database using formatdb\n".$command." error code ".$errorCode);
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
	$queryInsertDB = "INSERT INTO dbs (dbname, type, description, active, userid,last_update)VALUES(\"".$dbName."\",\"".$dbTypeLetter."\",\"".$dbTitle."\",".$dbStatusUploaded.",".$userid.",NOW())";
	$sthp = $dbhp->prepare($queryInsertDB);
	$sthp->execute();
}


$sthp->finish();
$dbhp->disconnect();


#Subroutines
###############################################################################################################################
sub CreateCommandString
{
	my $fileName = basename($dbFile);
	$shellCommand = $formatdbPath." -i ".$dbFile." -p ".$dbType." -n \"".$fileName."\" -o ".$dbSeqId." -a ".$dbASNFormat." -v ".$dbVolumeSize." -b ".$dbASNMode." -e ".$dbInSeqEntry." -s ".$dbCreateIndexes." -t \"".$dbTitle."\"";
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
