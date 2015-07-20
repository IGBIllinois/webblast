<?php
$queryJob = new Job($sqlDataBase,$config_array);
$queryJob->LoadJob($_GET['job']);
$queryContents = $queryJob->GetQueryString($_GET['queryid']);
$queryString = implode($queryContents);

echo "<pre>".$queryString."</pre>";

?>
