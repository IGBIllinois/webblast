#!/usr/bin/perl
use Digest::MD5;
use Archive::Extract;
use strict;
use warnings;
use Net::FTP;
use Getopt::Long;
use Pod::Usage;
use File::stat;
use DBI;

#FTP server settings
#####################
use constant VERSION => 1.2;
use constant NCBI_FTP => "ftp.ncbi.nlm.nih.gov";
use constant BLAST_DB_DIR => "/blast/db";
use constant USER => "anonymous";
use constant PASSWORD => "anonymous";
use constant DEBUG => 0;
$Archive::Extract::PREFER_BIN = 1;

# Process command line options
#############################
my $opt_verbose = 1;
my $opt_quiet = 0;
my $opt_force_download = 0;
my $opt_help = 0;
my $opt_passive = 0;
my $opt_timeout = 600;
my $opt_showall = 0;

#Setup sql connection
#####################
my $user="webBlast";
my $pass="igb123";
my $db = "blastWeb";

my $try=0;
my $ftp;
my $maxTries=4;
my $extractDestination="/export/home/blastdbs/ncbi_archive_test";
my $compressedDownloads = "/export/home/blastdbs/ncbi_compressed";
my $slaveDestination = "/state/partition1/rsyncdbs/";
my $userDbsSource = "/export/home/blastdbs/userdbs";

my $slaveBackupdbs = "/state/partition1/backupdbs/";
my $slaveRsyncdbs = "/state/partition1/rsyncdbs/";
my $slaveBlastdbs = "/state/partition1/blastdbs/";

my $errorDetails="";
my $fileStatus=1;
my @extractedDbFiles;
my $file;
my $count;
my $forkMoveDetails;
my @nodMD5Exceptions = ("vector.tar.gz");
	chdir($compressedDownloads);
	#Remove backup database to clear enough space for new one.
	system("rocks run host compute \"rm -f ".$slaveBackupdbs."*\"");
	
	#Get a list of files available to download form NCBI FTP
	my @filesToDownload = get_files_to_download();
	foreach $file (@filesToDownload)
	{
		$try=0;
		if($fileStatus!=0)
		{
			do {
				$fileStatus=1;

				download($file);
				if(open(FILE,$file."\.md5") or is_exception($file))
				{
       			        	my $md5line = <FILE>;
					close(FILE);
       				        my $ftpMD5;
       				        my $fileName;

       				        ($ftpMD5,$fileName) = split(/ +/,$md5line);
       				       	my $actualMD5=md5sum($file);

					#Compare MD5 checksums of file to NCBI available checksum
			                if($actualMD5 eq  $ftpMD5 or is_exception($file))
					{
						$fileStatus=1;
						print "\n$file passed MD5 Check (".$actualMD5.")";

						#Extract archive files
       			        	 	my $archiveExtract=Archive::Extract->new( archive => $file );
       	       			  		my $status=$archiveExtract->extract( to => $extractDestination );
						print "\nExtracting $file ...";
             	  		 		if($archiveExtract->error(1) ne "")
      	       				   	{
							$fileStatus=0;
							unlink($file);
							$errorDetails=$errorDetails."Error Extracting archive ".$file;
        	       	 			}else{
							$fileStatus=1;
							print "\n$file Extracted";
						}
	       	         			undef $archiveExtract;
					}else{
						$fileStatus=0;
                                                unlink($file);
						$errorDetails=$errorDetails."Error matching MD5 ".$file;
					}
				}else{
					unlink($file);
					$fileStatus=0;
					print "Error opening MD5 file".$file;
				}
				$try=$try+1;		
			}while($fileStatus == 0 and $try < $maxTries);
		}
	}	

	#Rsync new files to slave nodes
	if($fileStatus==1)
	{
		for($count=0;$count<=23;$count++) 
		{
			if(system("rsync -au ".$extractDestination."/* compute-0-".$count.":".$slaveDestination."") != 0)
			{
				$fileStatus=0;
				$errorDetails=$errorDetails."\nFailed to rsync ncbi archive compute-0-$count";
				print "\nFailed to rsync ncbi archive compute-0-$count";
			}
        	}
	}

	#Rsync user dbs to slave nodes
	if($fileStatus==1)
        {
                for($count=0;$count<=23;$count++) 
		{
                	if(system("rsync -au ".$userDbsSource."/* compute-0-".$count.":".$slaveDestination."") != 0)
                        {
                        	$fileStatus=0;
                                $errorDetails=$errorDetails."\nFailed to rsync userdbs compute-0-$count";
                                print "\nFailed to rsync userdbs compute-0-$count";
                        }
		}
        }

	if($fileStatus == 1)
	{
		while(RunningJobsCount() > 0)
                {
                        print "\nRunning jobs count=".RunningJobsCount()." Sleeping 5 seconds";
                        sleep(5);
                }

                $forkMoveDetails = system("rocks run host compute \"mv -f ".$slaveBlastdbs."* ".$slaveBackupdbs." >> downloaddbs.log\"");
		sleep(10);	
                $forkMoveDetails = system("rocks run host compute \"mv -f ".$slaveRsyncdbs."* ".$slaveBlastdbs." >> downloaddbs.log\"");

	}

	if($fileStatus==0)
	{
		my $statusError = 7;
		UpdateStatus($statusError);
		
		print "Send email of failure to download correctly";
		sendEmail("nevoband\@igb.uiuc.edu","clcluster\@igb.uiuc.edu","Error Updating Blast DBs","Tries: ".$try." Success Code: ".$fileStatus."\nError Details:".$errorDetails);
		
	}else{
		my $statusCompleted = 3;
		UpdateStatus($statusCompleted);	
		
        	print "Send email of success";
        	sendEmail("nevoband\@igb.uiuc.edu","clcluster\@igb.uiuc.edu","Success Updating Blast DBs","Tries: ".$try." Success Code: ".$fileStatus." Error Details:".$errorDetails);
	}


#Subroutines
###############################################

#Check File MD5
sub md5sum{
  my $file = shift;
  my $digest = "";
  eval{
    open(FILE, $file) or die "Can't find file $file\n";
    my $ctx = Digest::MD5->new;
    $ctx->addfile(*FILE);
    $digest = $ctx->hexdigest;
    close(FILE);
  };
  if($@){
    print $@;
    return "";
  }else{
    return $digest;
  }
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

# Connects to NCBI ftp server
sub connect_to_ftp
{
    my %ftp_opts;
    $ftp_opts{'Passive'} = 1 if $opt_passive;
    $ftp_opts{'Timeout'} = $opt_timeout if ($opt_timeout >= 0);
    my $ftp = Net::FTP->new(NCBI_FTP, %ftp_opts)
        or die "Failed to connect to " . NCBI_FTP . ": $!\n";
    $ftp->login(USER, PASSWORD)
        or die "Failed to login to " . NCBI_FTP . ": $!\n";
    $ftp->cwd(BLAST_DB_DIR);
    $ftp->binary();
    print STDERR "\nConnected to NCBI";
    return $ftp;
}

# Obtains the list of files to download
sub get_files_to_download
{
    $ftp = &connect_to_ftp();
    my @blast_db_files = $ftp->ls();
    my @retval = ();

    if (DEBUG) {
        print STDERR "DEBUG: Found the following files on ftp site:\n";
        print STDERR "DEBUG: $_\n" for (@blast_db_files);
    }

   for my $file (@blast_db_files) {
       next unless ($file =~ /\.tar\.gz$/);
       		push @retval, $file;
    }
    $ftp->quit;
    return @retval;
}

# Download the requestes files only if they are missing or if they are newer in
# the FTP site.
sub download
{
    my $dlFile = shift;
    $ftp = &connect_to_ftp();
    print STDERR "\nMD5 DB ".$ftp->mdtm($dlFile."\.md5")." ".$ftp->mdtm($dlFile);
    if (not -f $dlFile or ((stat($dlFile))->mtime < $ftp->mdtm($dlFile))) {
    	print STDERR "Downloading $dlFile...\n ";
	if(abs($ftp->mdtm($dlFile."\.md5")-$ftp->mdtm($dlFile)) < 3600)
	{
		print STDERR "\nMD5 file found on NCBI for $dlFile";
		$ftp->get($dlFile."\.md5");
	}
	else
	{
		push(@nodMD5Exceptions,$dlFile);
		unlink($dlFile."\.md5");
		print STDERR "\nMD5 file wasn't updated by NCBI for $dlFile";
	}
      	$ftp->get($dlFile);
    } else {
    	print STDERR "\n$dlFile is up to date.";
    }
    $ftp->quit;
}

sub is_exception
{
	my $fileName = shift;
	
	foreach(@nodMD5Exceptions)
	{
		if( $fileName eq $_ )
		{
			return 1;
		}
	}
	return 0;
}

#Update database update status to ready
sub UpdateStatus
{
	my $statusToSet = shift;

	my $dbh= DBI->connect("DBI:mysql:".$db.":localhost",$user,$pass,{RaiseError=>1});
	$dbh->{mysql_auto_reconnect} = 1;
	
	my $querySetUpdateStatus="INSERT INTO dbupdates (updatestatus,updatedate)VALUES(".$statusToSet.",NOW())";
	my $sth=$dbh->prepare($querySetUpdateStatus);
	$sth->execute();
	
	$sth->finish();
	$dbh->disconnect();
}

#Get the number of currently running jobs
sub RunningJobsCount
{
	my $statusNew = 1;
	my @runningJobs;
	my $rowCount = 0;
        my $dbh= DBI->connect("DBI:mysql:".$db.":localhost",$user,$pass,{RaiseError=>1});
        $dbh->{mysql_auto_reconnect} = 1;
	
        my $querySetUpdateStatus="SELECT id FROM blast_jobs WHERE status=".$statusNew;
        my $sth=$dbh->prepare($querySetUpdateStatus);
        $sth->execute();

	while(@runningJobs = $sth->fetchrow_array())
	{
		$rowCount++;	
	}

	
        $sth->finish();
        $dbh->disconnect();

	return $rowCount;;
}
