<?php
include "includes/txt2html.php";

$queryResultsPath="SELECT webresultspath FROM config ORDER BY id DESC";
$resultsPath=$sqlDataBase->singleQuery($queryResultsPath);

$resultsContents=file($resultsPath.$_GET['job']."/".$_GET['queryid'].".result");
$htmlResults = implode($resultsContents);

echo "<pre>".$htmlResults."</pre>";



?>
