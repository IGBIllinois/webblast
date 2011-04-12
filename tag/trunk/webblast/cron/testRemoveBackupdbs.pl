#!/usr/bin/perl

my $slaveBackupdbs = "/state/partition1/backupdbs/";
system("cluster-fork --bg rm -f ".$slaveBackupdbs."*");
