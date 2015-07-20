<?php
$fileName="results".$_SESSION['username']."_".$_GET['job'].".txt";

if(!file_exists($fileName))
{
	$handle = fopen($fileName,'a');

	$queryJobResults="SELECT results FROM blast_query WHERE jobid=".$_GET['job'];
	$jobResultsArray = $sqlDataBase->query($queryJobResults);

	foreach($jobResultsArray as $id=>$assoc)
	{
		fwrite($handle,$assoc['results']);
	
	}
	$fclose($handle);
}

$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$extra = $filename;
echo "created file";
//header("Location: http://$host$uri/results/$extra");
//exit;

?>
