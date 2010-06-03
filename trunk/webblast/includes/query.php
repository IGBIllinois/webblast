<?php
$queryQueriesPath="SELECT webqueriespath FROM config ORDER BY id DESC";
$queriesPath=$sqlDataBase->singleQuery($queryQueriesPath);

$queryContents=file($queriesPath.$_GET['job']."/".$_GET['queryid'].".fasta");
$queryString = implode($queryContents);

echo "<pre>".$queryString."</pre>";


?>
