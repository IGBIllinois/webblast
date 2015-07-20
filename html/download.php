<?php
session_start();
function __autoload($class_name) {
    require_once 'classes/' . $class_name . '.php';
}

include "includes/config.php";
//Initialize database
error_log("User Name ".$_SESSION['userid']." ".$_SESSION['username']." ".$_SESSION['username'],0);
$sqlDataBase= new SQLDataBase($config_array['sql_config']['sql_host'],$config_array['sql_config']['sql_database'],$config_array['sql_config']['sql_user'],$config_array['sql_config']['sql_pass']);
$authen = new Auth($sqlDataBase,$config_array);
error_log($_SESSION['userid']." ".$_SESSION['username'],0);
if(isset($_GET['job']) && isset($_GET['filetype']) && (isset($_GET['token']) || (isset($_SESSION['username']) && isset($_SESSION['userid']))))
{
	error_log("webblast authorized",0);
	if(isset($_GET['token']))
	{
		$token = mysql_real_escape_string($_GET['token']);
	}

	$jobid = mysql_real_escape_string($_GET['job']);
	$fileType = mysql_real_escape_string($_GET['filetype']);
        if($authen->AuthToken($token,$jobid) || $authen->AuthSession())
        {
		$returnJob = new Job($sqlDataBase,$config_array);
		$returnJob->LoadJob($jobid);
		error_log("job authorization ".$returnJob->GetUserId(),0);	
		if(isset($_GET['token']) || $returnJob->GetUserId()==$_SESSION['userid'])
		{
			error_log("job authorized",0);	
			if($fileType=="result")
			{			
                		$file = $returnJob->GetResultsFilePath();
				error_log($file,0);
			}elseif($fileType=="csv")
			{
				$file = $returnJob->GetCSVResultsFilePath();
				error_log($file,0);
			}
			$fileName= "JOB_".$jobid.".".$fileType;
			error_log($file,0);
                	if(!file_exists($file))
                	{
                	        echo "No file found";
                	}
                	else{	
                	        header("Content-Disposition: attachment; filename=" . urlencode($fileName));
                	        header("Content-Type: application/force-download");
                	        header("Content-Type: application/octet-stream");
                	        header("Content-Type: application/download");
                	        header("Content-Description: File Transfer");
                	        header("Content-Length: " . filesize($file));
                	        flush(); // this doesn't really matter.

                	        $fp = fopen($file, "r");
                	        while (!feof($fp))
                	        {
                	           	echo fread($fp, 65536);
               		             	 flush(); // this is essential for large downloads
                        	}
                        	fclose($fp);
			}
		}
	}else{
		echo "Bad token or file was not found.";

	}
}
else{
	include "Bad token or file was not found.";
}
?>
