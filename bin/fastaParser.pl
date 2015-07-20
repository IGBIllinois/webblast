#!/usr/bin/perl
use Bio::SeqIO;
use DBI;
use Config::IniFiles;

$fastaFilePath=$ARGV[0];
$jobid = $ARGV[1];
$chunkSize= $ARGV[2];
$configFilePath = $ARGV[3];
$wgetURL = $ARGV[4];
$userid = $ARGV[5];

#Load configuration from ini file
$cfg = new Config::IniFiles( -file => $configFilePath );

$sqlUser = $cfg->val('sql_config','sql_user');
$sqlPass = $cfg->val('sql_config','sql_pass');
$database = $cfg->val('sql_config','sql_database');
$sqlHost = $cfg->val('sql_config','sql_host');
$queryPath = $cfg->val('head_paths','query_chunks_path');

$updateJobStatusInterval=200;

$rowLen=50;
$submitStatus=6;
$newStatus=1;

$errorMessage="";

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

if($wgetURL)
{
	$errorCode = system("wget ".$wgetURL." -O ".$fastaFilePath);
        if($errorCode != 0)
        {
                $errorMessage = "Failed to download file from URL ".$wgetURL;
        }
}

if($errorCode==0)
{
	#Use bioperl sequence IO to parse the fasta file into individual queries.
	$in  = Bio::SeqIO->new(-file => $fastaFilePath , '-format' => 'Fasta');
	$queriesAdded=0;
	$currChunkSize=0;
	$fastaString="";
	$count=1;
	#Run through the individual queries sequence IO parsed.
	while ( my $seq = $in->next_seq() ) 
	{
		#if chunk size reached wrap it up in a file and create a database entry for it..	
		if($currChunkSize>=$chunkSize)
		{
	                SubmitChunk($fastaString,$currChunkSize);
	
	                $queriesAdded=$queriesAdded+$currChunkSize;
	                $currChunkSize=0;
	                $fastaString="";
		}
		
		#If chunk size is not reached add more fasta queries to it.
		if($currChunkSize<$chunkSize)
		{
			#add new query chunk
			$fastaString = $fastaString.">".$seq->id." ".$seq->desc."\n";

		        $rowLen=50;
	       		$key=0;

	       		while($key < length($seq->seq)){
	                	$fastaString = $fastaString.substr($seq->seq,$key,$rowLen)."\n";
	                	$key=$key+$rowLen;
	        	}

			$currChunkSize++;
		}
	
		#Update progress of parsing on the database every chunksize*interval queries
		if(($queriesAdded % $chunkSize*$updateJobStatusInterval) == 0)
		{
			$queryUpdateJob = "UPDATE blast_jobs SET queriesadded=$queriesAdded WHERE id=$jobid";
			$sthp = $dbhp->prepare($queryUpdateJob);
			$sthp->execute()
		}
	}

	SubmitChunk($fastaString,$currChunkSize);
	$queriesAdded=$queriesAdded+$currChunkSize;
	UpdateJobProgress($queriesAdded);
	unlink($fastaFilePath);
}

if($errorCode!=0)
{
	#send email of failed upload
        sendEmail($userEmail, $fromEmail, $errorMessage);
}

sub SubmitChunk
{
	my ($submitFastaString,$submitChunkSize) = @_;
	
	$queryAddQuery="INSERT INTO blast_queries (jobid,statusid,chunksize) VALUES (".$jobid.",".$newStatus.",".$submitChunkSize.")";
        $sthp = $dbhp->prepare($queryAddQuery);
        $sthp->execute();
        $queryid = $dbhp->{ q{mysql_insertid}};

        open (FASTAFILE, '>'.$queryPath.$jobid."/".$queryid.".fasta");
        print FASTAFILE $submitFastaString;
        close (FASTAFILE);
}

sub UpdateJobProgress
{
	my ($queriesAddedNum) = @_;
	$queryUpdateJob = "UPDATE blast_jobs SET queriesadded=$queriesAddedNum WHERE id=$jobid";
	$sthp = $dbhp->prepare($queryUpdateJob);
	$sthp->execute()
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

