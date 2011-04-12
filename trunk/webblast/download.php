<?php

function __autoload($class_name) {
    require_once 'classes/' . $class_name . '.php';
}

include "includes/config.php";

//initialize ldap authentication object
$authen=new LdapAuth($host,$peopleDN,$groupDN,$ssl,$port);

//Initialize database
$sqlDataBase= new SQLDataBase('localhost',$sqlDataBase,$sqlUserName,$sqlPassword);

include "includes/authenticate.php";

if(isset($_SESSION['username']) && isset($_SESSION['password']))
{
        if($authen->Authenticate($_SESSION['username'],$_SESSION['password'],""))
        {

		$queryResultsPath="SELECT resultsdestinationpath FROM config ORDER BY id DESC LIMIT 1";
                $resultsPath=$sqlDataBase->SingleQuery($queryResultsPath);
                $file = $resultsPath.$_SESSION['username']."_".$_GET['job'].".".$_GET['filetype'];
		$fileName= $_SESSION['username']."_".$_GET['job'].".".$_GET['filetype'];
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
	}else{
		include "includes/downloadlogin.php";

	}
}
else{
	include "includes/downloadlogin.php";
}
?>
