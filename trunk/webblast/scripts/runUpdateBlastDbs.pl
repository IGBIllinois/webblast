#!/usr/bin/perl
#Update blastdbs on slaves

for($count = 0; $count <24 ; $count++)
{
	$pid=system("rsync -au /export/home/blastdbs/ncbi_archive_test/nr* compute-0-$count:/state/partition1/rsyncdbs/ &");
	print "$pid \n"
}


