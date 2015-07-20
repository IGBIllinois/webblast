#!/usr/bin/perl
#Program to start a blasting mysql submissions

#setup the environment paths to the blast binary files and search databases
BEGIN {
        $ENV{BLASTDIR} = '/opt/Bio/ncbi/bin';
        $ENV{BLASTDATADIR} = '/state/partition1/blastdbs';
}


use lib "/opt/rocks/lib/perl5/site_perl/5.8.8";
use Bio::Perl;
use Bio::SearchIO;
use File::Path;

$outfile=$ARGV[0];
$csvfile=$ARGV[1];
					        		
#Read parsed blast_report
$blast_report = new Bio::SearchIO(-format=>'blast', -file => $outfile );
					
#submit to database if parsed results are available
$csvResults="";
open (CSVRESULTFILE, '> '.$csvfile);
while($result=$blast_report->next_result)
{
							$csvresults="";
	                                                $csvResults = $csvResults."\"".$result->query_name()."\",\"".$result->query_accession()."\",\"".$result->query_length()."\",\"".$result->database_letters()."\",\"".$result->database_entries()."\",\"".$result->num_hits()."\"";
                                                        $queryDescription=$result->query_description();
                                                        $queryDescription=~s/"/""/g;
                                                        $csvResults=$csvResults.",\"".$queryDescription."\"";
                                                        while( my $hit = $result->next_hit() ) {
                                                                $csvResults = $csvResults.",\"".$hit->name()."\",\"".$hit->length()."\",\"".$hit->accession()."\",\"".$hit->algorithm()."\",\"".$hit->raw_score()."\",\"".$hit->significance()."\",\"".$hit->bits()."\",\"".$hit->num_hsps()."\",\"".$hit->locus()."\"";
                                                                if(my $hsp = $hit->next_hsp())
                                                                {
                                                                        $csvResults=$csvResults.",\"".(($hit->frame() + 1) * $hsp->strand("hit"))."\",\"".$hsp->strand("query")."\",\"".$hsp->strand("hit")."\",\"".$hsp->hsp_length()."\",\"".$hsp->percent_identity()."\",\"".($hsp->num_conserved()/$hsp->hsp_length())."\",\"".$hsp->gaps()."\",\"".$hsp->start("query")."\",\"".$hsp->end("query")."\",\"".$hsp->start("hit")."\",\"".$hsp->end("hit")."\"";
                                                                }
                                                                $hitDescription=$hit->description;
                                                                $hitDescription=~s/"/""/g;
                                                                $csvResults=$csvResults.",\"".$hitDescription."\"";
                                                                $taxon="";
                                                                if($hitDescription =~ m/[\[](.*?)[\]]/)
                                                                {
                                                                        $taxon = $1;
                                                                }
                                                                $csvResults=$csvResults.",\"".$taxon."\"";
                                                        }
                                                        $csvResults=$csvResults."\n";
							print CSVRESULTFILE $csvResults;
							$csvResults="";
}
close (CSVRESULTFILE);
