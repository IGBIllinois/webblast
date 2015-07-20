<?php

function __autoload($class_name) {
    require_once 'classes/' . $class_name . '.php';
}

include "includes/config.php";

$sqlDataBase= new SQLDataBase('localhost',$sqlDataBase,$sqlUserName,$sqlPassword);

$queryResultsPath="SELECT webresultspath FROM config ORDER BY id DESC";
$resultsPath=$sqlDataBase->singleQuery($queryResultsPath);

$im = shell_exec("/var/www/html/webblast/scripts/RenderBlast.pl ".$resultsPath.$_GET['job']."/".$_GET['queryid'].".result ".$_GET['query_num']);
echo $im;
?>
