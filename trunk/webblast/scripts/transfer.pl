#!/usr/bin/perl

use lib "/opt/rocks/lib/perl5/site_perl/5.8.8";
use DBI;
use File::Copy;
#perl scripts/transfer.pl ".$concatPath." ".$userDropBoxPath." ".$this->jobid." ".$userNetid)

$concatPath = $ARGV[0];
$dropBoxPath = $ARGV[1];
$jobid = $ARGV[2];
$netid = $ARGV[3];
$sqlUser = $ARGV[4];
$sqlPass = $ARGV[5];
$database = $ARGV[6];
$email = $ARGV[7];


$statusCompleted=3;

$fileToCopy=$concatPath.$netid."_".$jobid.".result";
$newFile=$dropBoxPath.$netid."_".$jobid.".result";

copy($fileToCopy,$newFile);

$dbhp = DBI->connect("DBI:mysql:".$database.":localhost',$sqlUser,$sqlPass,{RaiseError => 1});

$queryConfigPaths = "SELECT url, transfercompletemsg, transfercompletesubj, fromemail FROM config ORDER BY id DESC LIMIT 1";
$sthp=$dbhp->prepare($queryConfigPaths);
$sthp->execute();
@configPaths = $sthp->fetchrow_array();
$webURL=$configPaths[0];
$transferCompleteMsg=$configPaths[1];
$transferCompleteSubj=$configPaths[2];
$fromEmail = $configPaths[3];

$queryQueryResultsId="UPDATE blast_jobs SET status= $statusCompleted WHERE id=$jobid";
print $queryQueryResultId;
$sthp = $dbhp->prepare($queryQueryResultsId);
$sthp->execute();

if((-s $fileToCopy) != (-s $newFile))
{
	$transferStatus="File Sizes Do Not Match".(-s $fileToCopy)."!=".(-s $newFile);
}
else{
	$transferStatus="Job ".$jobid." transfer complete.\nFiles Transfered To: ".$newFile;
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
$content = $updatedTransferCompleteMsg;
$to = "To: ".$email."\n";
                                
print $to;
open(SENDMAIL, "|$sendmail") or die "Cannot open $sendmail: $!";
print SENDMAIL $from;
print SENDMAIL $to;
print SENDMAIL $subject;
print SENDMAIL $content;
close(SENDMAIL);

