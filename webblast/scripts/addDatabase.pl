#!/usr/bin/perl
use DBI;
use Cwd;
use File::Basename;
use Config::IniFiles;

#get all the required inputs
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
$databaseId = $ARGV[11];
$configFilePath = $ARGV[12];
$wgetURL = $ARGV[13];

$errorMessage = "";
$errorCode = 0;

#Load configuration from ini file
$cfg = new Config::IniFiles( -file => $configFilePath );
$sqlUser = $cfg->val('sql_config','sql_user');
$sqlPass = $cfg->val('sql_config','sql_pass');
$database = $cfg->val('sql_config','sql_database');
$sqlHost = $cfg->val('sql_config','sql_host');
$userDbsPath = $cfg->val('head_paths','databases_path')."USER/";
$taxonomyPath = $cfg->val('formatdb_settings','taxonomy_path');
$dbVolumeSize = $cfg->val('formatdb_settings','volume_size');
$formatdbBin = $cfg->val('formatdb_settings','formatdb_bin_path');
$sendmailBin = $cfg->val('head_paths','sendmail_bin_path');

($filename,$userDbsPath) = fileparse($dbFile);

print $sqlUser." ".$sqlPass." ".$database." ".$sqlHost;
#connect to mysql database
$dbhp = DBI->connect("DBI:mysql:".$database.":".$sqlHost,$sqlUser,$sqlPass,{RaiseError => 1});

#get email config
$queryConfigFromMail = "SELECT fromemail FROM config ORDER BY id DESC LIMIT 1";
$sthp = $dbhp->prepare($queryConfigFromMail);
$sthp->execute();
@configFromMail = $sthp->fetchrow_array();
$fromEmail = $configFromMail[0];

#get user email
$queryUsersEmail = "SELECT email FROM users WHERE id=".$userid;
$sthp = $dbhp->prepare($queryUsersEmail);
$sthp->execute();
@usersEmail = $sthp->fetchrow_array();
$userEmail = $usersEmail[0];

#change directory to databases directory so we can run formatdb
chdir($userDbsPath);

if($wgetURL)
{
	$errorCode = system("wget ".$wgetURL." -O ".$dbFile);
	if($errorCode != 0)
	{
		$errorMessage = "Failed to download database from URL ".$wgetURL;
	}
}

if($errorCode==0)
{
	$command = CreateCommandString();

      	#run the formatdb command on the uploaded database
    	print $command;
     	$errorCode = system($command);
      	if($errorCode != 0)
      	{
         	$errorMessage = "IGB BLAST (new DB failed)","Failed to create new database using formatdb\n".$command;
    	}
}


#check if an error occured
if($errorCode != 0)
{
	#send email of failed upload
	sendEmail($userEmail, $fromEmail, $errorMessage);
}
else
{
	#Delete the uploaded file if the database was uploaded correctly
	unlink($dbFile);
	
	#Change the database status in the mysql database
	$dbStatusActive = 1;
	
	$queryUpdateDB = "UPDATE dbs SET active=".$dbStatusActive." WHERE id=".$databaseId;
	$sthp = $dbhp->prepare($queryUpdateDB);
	$sthp->execute();

	#send e-mail of successful database upload	
	sendEmail($userEmail, $fromEmail, "IGB BLAST (new DB installed successfully)","Your database ".$dbTitle." (".$dbName.") is now active on the IGB BLAST server\nFor problems please contact IGB helpdesk ".$fromEmail);	
	
}


$sthp->finish();
$dbhp->disconnect();


#Subroutines
###############################################################################################################################

#Generate the formatdb command to run
sub CreateCommandString
{
	my $fileName = basename($dbFile);
	$shellCommand = $formatdbBin." -i ".$dbFile." -p ".$dbType." -n \"".$fileName."\" -o ".$dbSeqId." -a ".$dbASNFormat." -v ".$dbVolumeSize." -b ".$dbASNMode." -e ".$dbInSeqEntry." -s ".$dbCreateIndexes." -t \"".$dbTitle."\"";
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
        my $sendmail = $sendmailBin." -t";

        open(MAIL, "|$sendmail") or die "Cannot open $sendmail: $!";;
        print MAIL "From: $from\n";
        print MAIL "To: $to\n";
        print MAIL "Subject: $subject\n\n";
        print MAIL "$message\n";
        close(MAIL);

}
