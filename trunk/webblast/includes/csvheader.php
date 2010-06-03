<?php
$queryJobHits="SELECT b FROM blast_jobs WHERE id=".$_GET['job'];
$jobHits=$sqlDataBase->singleQuery($queryJobHits);

$csvTitles="\"NAME\",\"ACCESSION\",\"LENGTH\",\"LETTERS\",\"DB_ENTRIES\",\"HITS_NUMBER\",\"DESCRIPTION\"";
for($i=1;$i<=$jobHits;$i++)
{
	$csvTitles=$csvTitles.",\"HIT".$i."_NAME\",\"HIT".$i."_LENGTH\",\"HIT".$i."_ACCESSION\",\"HIT".$i."_ALGORITHM\",\"HIT".$i."_RAW_SCORE\",\"HIT".$i."_SIGNIFICANCE\",\"HIT".$i."_BITS\",\"HIT".$i."_NUM_HSPS\",\"HIT".$i."_LOCUS\",\"HIT".$i."_FRAME\",\"HIT".$i."_STRAND_QUERY\",\"HIT".$i."_STRAND_HIT\",\"HIT".$i."_PERCENT_IDENTITY\",\"HIT".$i."_PERCENT_POSITIVES\",\"HIT".$i."_GAPS\",\"HIT".$i."_QUERY_START\",\"HIT".$i."_QUERY_END\",\"HIT".$i."_SBJCT_START\",\"HIT".$i."_SBJCT_END\",\"HIT".$i."_DESCRIPTION\",\"HIT".$i."_TAXON\"";
}

echo "<br><br><b>CSV Titles</b><br>(Copy these to the begining of the CSV file to have titles in your CSV reader)<br><br>";
echo "<br><TEXTAREA NAME=\"OFF\" COLS=100 ROWS=30 WRAP=SOFT>".$csvTitles."</TEXTAREA>";
?>
