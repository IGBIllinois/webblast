#!/usr/bin/perl
#Update blastdbs on slaves manually

for($count = 0; $count <24 ; $count++)
{
	$pid=system("rsync -au /export/home/blastdbs/ncbi_archive_test/*.pal compute-0-$count:/state/partition1/blastdbs/ &");
	print "$pid \n"
}


