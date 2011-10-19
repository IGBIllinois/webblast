#!/usr/bin/perl
use Bio::SeqIO;
use DBI;
use Config::IniFiles;

$fastaFilePath=$ARGV[0];
$jobid = $ARGV[1];
$chunkSize= $ARGV[2];
$configFilePath = $ARGV[3];

#Load configuration from ini file
$cfg = new Config::IniFiles( -file => $configFilePath );

$sqlUser = $cfg->val('sql_config','sql_user');
$sqlPass = $cfg->val('sql_config','sql_pass');
$database = $cfg->val('sql_config','sql_database');
$sqlHost = $cfg->val('sql_config','sql_host');
$queryPath = $cfg->val('head_paths','query_chunks');

$updateJobStatusInterval=200;

$rowLen=50;
$submitStatus=6;
$newStatus=1;

$dbhp = DBI->connect("DBI:mysql:".$database.":".$sqlHost,$sqlUser,$sqlPass,{RaiseError => 1});

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
