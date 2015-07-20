<?php
include "includes/txt2html.php";
$resultJob = new Job($sqlDataBase,$config_array);
$resultJob->LoadJob($_GET['job']);
$resultContents = $resultJob->GetResultsString($_GET['queryid']);
$resultString = implode($resultContents);
echo "<pre>".$resultString."</pre>";



?>
