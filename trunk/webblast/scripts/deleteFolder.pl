#!/usr/bin/perl

$folderForDeletion = $ARGV[0];
$deletionFolder = $ARGV[1];
$jobid = $ARGV[2];
$mkdir = $ARGV[3];
$type = $ARGV[4];


system("mv ".$folderForDeletion." ".$deletionFolder.$jobid."_".$type);
if($mkdir)
{
	system("mkdir $folderForDeletion");
	system("chmod -R 2777 $folderForDeletion");
}
system("rm -rf ".$deletionFolder.$jobid."_".$type);



