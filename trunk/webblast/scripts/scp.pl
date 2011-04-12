#!/usr/bin/perl

use lib "/opt/rocks/lib/perl5/site_perl/5.8.8";
use DBI;
use Net::SCP::Expect;
#perl scripts/transfer.pl ".$concatPath." ".$userDropBoxPath." ".$this->jobid." ".$userNetid)

$concatPath = $ARGV[0];
$scpHost = $ARGV[1];
$scpUser = $ARGV[2];
$scpPass= $ARGV[3];
$scpPath = $ARGV[4];
$jobid = $ARGV[5];
$netid = $ARGV[6];
$email = $ARGV[7];
$sqlUser = $ARGV[8];
$sqlPass = $ARGV[9];
$database = $ARGV[10];


$error="";

$statusCompleted=3;

$fileToSend=$concatPath.$netid."_".$jobid.".result";
$csvToSend = $concatPath.$netid."_".$jobid.".csv";


eval {
	my $scp = Net::SCP::Expect->new(host=>$scpHost,user=>$scpUser,password=>$scpPass);
	$scpResult = $scp->scp($fileToSend,$scpPath);
	$scpResult = $scp->scp($csvToSend,$scpPath);
};
if($@) {
	$error = $@;
}

$dbhp = DBI->connect("DBI:mysql:".$database.":localhost",$sqlUser,$sqlPass,{RaiseError => 1});

$queryConfigPaths = "SELECT url, transfercompletemsg, transfercompletesubj, fromemail FROM config ORDER BY id DESC LIMIT 1";
$sthp=$dbhp->prepare($queryConfigPaths);
$sthp->execute();
@configPaths = $sthp->fetchrow_array();
$webURL=$configPaths[0];
$transferCompleteSubj=$configPaths[2];
$fromEmail =$configPaths[3];

$queryQueryResultsId="UPDATE blast_jobs SET status= $statusCompleted WHERE id=$jobid";
$sthp = $dbhp->prepare($queryQueryResultsId);
$sthp->execute();

if($scpResult)
{
	$transferStatus = "Job #".$jobid." transfer complete. \nFiles transfered to ".$scpHost." to path ".$scpPath;
}
else
{
	$transferStatus = "Job #".$jobid." failed to transfer. \nTransfer attempted to ".$scpHost." to path ".$scpPath.".\n\n".$error;
}

#Send user an email informing him of job completion

$updatedTransferCompleteSubj=$transferCompleteSubj;
$updatedTransferCompleteMsg=$transferCompleteMsg;

$updatedTransferCompleteSubj=~s/\[job\]/$jobid/g;
$updatedTransferCompleteMsg=~s/\[job\]/$jobid/g;

$csvWeburl=$webURL."download.php?job=".$jobid."&filetype=csv";
$resultWeburl=$webURL."download.php?job=".$jobid."&filetype=result";

$updatedTransferCompleteMsg=~s/\[url\]/$webURL/g;
$updatedTransferCompleteMsg=~s/\[csvurl\]/$csvWeburl/g;
$updatedTransferCompleteMsg=~s/\[resulturl\]/$resultWeburl/g;


$sendmail = "/usr/sbin/sendmail -t";
$from = "From: ".$fromEmail."\n";
$subject = "Subject: ".$updatedTransferCompleteSubj."\n";
$content = $transferStatus;
$to = "To: ".$email."\n";

print $to;
open(SENDMAIL, "|$sendmail") or die "Cannot open $sendmail: $!";
print SENDMAIL $from;
print SENDMAIL $to;
print SENDMAIL $subject;
print SENDMAIL $content;
close(SENDMAIL);


