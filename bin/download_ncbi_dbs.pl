#!/usr/bin/perl
use Digest::MD5;
use Archive::Extract;
use strict;
use warnings;
use Net::FTP;
use Getopt::Long;
use Pod::Usage;
use File::stat;
use File::Basename;
use DBI;
use Config::IniFiles;

#NEED TO CHANGE MANUALLY
############################

$configFilePath = $ARGV[0];

#Load configuration from ini file
$cfg = new Config::IniFiles( -file => $configFilePath );

$sqlUser = $cfg->val('sql_config','sql_user');
$sqlPass = $cfg->val('sql_config','sql_pass');
$database = $cfg->val('sql_config','sql_database');
$sqlHost = $cfg->val('sql_config','sql_host');

$databasesPath=$cfg->val('head_paths','databases_path');
$temporaryDirectory = $cfg->val('head_paths','temp_directory');


#Input Arguments
#databases to update
####################
my $numArgs = $#ARGV + 1;
my @dbsToUpdate = @ARGV;

#EMAIL Notice options
#####################
my $toMail = "nevoband\@igb.illinois.edu";
my $fromMail = "biocluster\@igb.illinois.edu";

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

my $try=0;
my $ftp;
my $maxTries=5;

#Destinations directories
#########################

my $errorDetails="";
my $fileStatus=1;
my $errorOccured=0;
my @extractedDbFiles;
my $count;
my $forkMoveDetails;
my @suffix = qw(.tar.gz);
my @nodMD5Exceptions = ("vector.tar.gz");
my %ncbiDbs;

	chdir($temporaryDirectory);
	
	#Get a list of files available to download form NCBI FTP
	my @filesToDownload = get_files_to_download();
	@filesToDownload = sort(@filesToDownload);
	
	my $dbFile;
	
	#Lets create a nice organized hash of all dbs and their related file parts
	##########################################################################
	foreach $dbFile (@filesToDownload)
	{
		my $dbName = fileparse($dbFile,@suffix);
		my $dbNameShort="";
		my $filePart;
		my $junkPath;
		($dbNameShort,$junkPath,$filePart) = fileparse($dbName,qr/\.[^.]*/);
		
		if(!exists $ncbiDbs{$dbNameShort})
		{
			$ncbiDbs{$dbNameShort} = ();
		}

		if($filePart ne "")
		{
			push @{$ncbiDbs{$dbNameShort} },$filePart;
		}
		else
		{
			push @{$ncbiDbs{$dbNameShort} },"";
		}
	}	

	my $shortNameDb;

	#print all dbs to download hashed
	##################################
	for $shortNameDb ( keys %ncbiDbs ) {
		if(defined $ncbiDbs{$shortNameDb})
		{
    			print "$shortNameDb: @{ $ncbiDbs{$shortNameDb} }\n";
		}
		else
		{
			print "$shortNameDb: single file.\n";
		}
	}
		
	for $shortNameDb (keys %ncbiDbs)
	{
		$try=0;
		my $junkPath="";
		my $filePart="";
	   if(updateDb($shortNameDb))
	   {
		foreach $filePart (@{ $ncbiDbs{$shortNameDb} })
		{
			my $file = $shortNameDb.$filePart.".tar.gz";
			$try = 0;
			if($fileStatus!=0)
			{	
				#main loop to download dbs, check md5 sum and extract tar gz
				#if all goes well then move on to next step if not then try again until max tries is reached.
				#############################################################################################
				do {
					$fileStatus=1;
					print "\nfile to download: ".$file."\n";
					download($file);
				
					if(open(FILE,$file."\.md5") or is_exception($file))
					{
	       			        	my $md5line = <FILE>;
						close(FILE);
	       				        my $ftpMD5;
	       				        my $fileName;
						
	       				        ($ftpMD5,$fileName) = split(/ +/,$md5line);
	       				       	my $actualMD5=md5sum($file);
						print "\nMD5 File:".$actualMD5." MD5 Expected:".$ftpMD5;	
						#Compare MD5 checksums of file to NCBI available checksum
				                if($actualMD5 eq  $ftpMD5 or is_exception($file))
						{
							$fileStatus=1;
							print "\n$file passed MD5 Check (".$actualMD5.")";
	
							#Extract archive files
       				        	 	my $archiveExtract=Archive::Extract->new( archive => $file );
       		       			  		my $status=$archiveExtract->extract( to => $temporaryDirectory );
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
							$errorDetails=$errorDetails."\nError matching MD5 ".$file." try(".$try.")";
						}
					}else{
						unlink($file);
						$fileStatus=0;
						print "Error opening MD5 file".$file;
					}
						
					$try=$try+1;
				}while($fileStatus == 0 and $try < $maxTries);			
	
				#Delete the tar gzip files downloaded if the files were successfuly extracted
				###########################################################################
				if($fileStatus==1)
				{
					if($shortNameDb)
					{
						unlink<*.tar.gz>
					}
				}
			}
		}

		#Check if jobs are using this database
		#if not then remove the database from the db directory and mv the new dbs from the rsync directory
		########################################################################################################
		if($fileStatus == 1)
               	{
                   	if($shortNameDb)
                       	{
                           	while(RunningJobsCount($shortNameDb) > 0)
                               	{
                                    	print "\nRunning jobs count=".(RunningJobsCount($shortNameDb))." Sleeping 5 seconds";
                                     	sleep(5);
                              	}
				#create temporary directory to mv the old databases to before deleting them
				unless(-d $temporaryDirectory.$shortNameDb)
				{
					mkdir($temporaryDirectory.$shortNameDb);
				}
				#system("mv ".$databasesPath.$shortNameDb.".* ".$temporaryDirectory.$shortnameDb."/");
				print "mv ".$databasesPath.$shortNameDb.".* ".$temporaryDirectory.$shortnameDb."/";
				#system("mv ".$temporaryDirectory.$shortNameDb.".* ".$databasesPath);
				print "mv ".$temporaryDirectory.$shortNameDb.".* ".$databasesPath;
				#system("rm -f ".$temporaryDirectory.$shortNameDb."/".$shortNameDb.".* &");
				print "rm -f ".$temporaryDirectory.$shortNameDb."/".$shortNameDb.".* &";
				
				UpdateDbDate($shortNameDb);
                    	}
           	}
	   }	
	}	
	if($errorOccured)
	{
		my $statusError = 7;
		UpdateStatus($statusError);
		
		print "Send email of failure to download correctly";
		sendEmail($toMail,$fromMail,"Error Updating Blast DBs","Tries: ".$try." Success Code: ".$fileStatus."\nError Details:".$errorDetails);
		
	}else{
		my $statusCompleted = 3;
		UpdateStatus($statusCompleted);	
		
        	print "Send email of success";
        	sendEmail($toMail,$fromMail,"Success Updating Blast DBs","Tries: ".$try." Success Code: ".$fileStatus." Error Details:".$errorDetails);
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
    my $success;
    $ftp = &connect_to_ftp();
    print STDERR "\nMD5 DB ".$ftp->mdtm($dlFile."\.md5")." ".$ftp->mdtm($dlFile);
    if (not -f $dlFile or ((stat($dlFile))->mtime < $ftp->mdtm($dlFile))) {
    	print STDERR "\nDownloading $dlFile...\n ";
	if(abs($ftp->mdtm($dlFile."\.md5")-$ftp->mdtm($dlFile)) < 3600)
	{
		print STDERR "\nMD5 file found on NCBI for $dlFile";
		$success = 0;
		while(!$success)
		{
			eval{
				$ftp->get($dlFile."\.md5");
				$success = 1;
			};
			if($@)
			{
				sleep(60);
			}
			
		}
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

sub updateDb
{
	my $dbName = shift;

	if($numArgs)
	{
        	foreach(@dbsToUpdate)
        	{
        	        if( $dbName eq $_ )
        	        {
				print "\nDbname detected: ".$dbName;
        	                return 1;
        	        }
        	}
	}
	else
	{
		return 1;
	}

        return 0;
}

#Update database update status to ready
sub UpdateStatus
{
	my $statusToSet = shift;

	my $dbh= DBI->connect("DBI:mysql:".$sqlDatabase.":".$sqlHost,$sqlUser,$sqlPass,{RaiseError=>1});
	$dbh->{mysql_auto_reconnect} = 1;
	
	my $querySetUpdateStatus="INSERT INTO dbupdates (updatestatus,updatedate)VALUES(".$statusToSet.",NOW())";
	my $sth=$dbh->prepare($querySetUpdateStatus);
	$sth->execute();
	
	$sth->finish();
	$dbh->disconnect();
}

#Update database last update date
sub UpdateDbDate
{
        my $dbName = shift;

        my $dbh= DBI->connect("DBI:mysql:".$sqlDatabase.":".$sqlHost,$sqlUser,$sqlPass,{RaiseError=>1});
        $dbh->{mysql_auto_reconnect} = 1;

        my $querySetUpdateDate="UPDATE dbs SET last_update=NOW() WHERE dbname=\"".$dbName."\"";
        my $sth=$dbh->prepare($querySetUpdateDate);
        $sth->execute();

        $sth->finish();
        $dbh->disconnect();
}

#Get the number of currently running jobs
sub RunningJobsCount
{
	my $dbName = shift;
	my $statusNew = 1;
	my @runningJobs;
	my $rowCount = 0;
        my $dbh= DBI->connect("DBI:mysql:".$sqlDatabase.":".$sqlHost,$sqlUser,$sqlPass,{RaiseError=>1});
        $dbh->{mysql_auto_reconnect} = 1;
	
        my $querySetUpdateStatus="SELECT bj.id FROM blast_jobs bj, dbs d WHERE d.id=bj.dbid AND bj.status=".$statusNew." AND d.dbname=\"".$dbName."\"";
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

