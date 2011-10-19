<?php

function __autoload($class_name) {
    require_once 'classes/' . $class_name . '.php';
}

include "includes/config.php";

//initialize ldap authentication object
$authen=new LdapAuth($config_array['ldap_config']['ldap_host'],$config_array['ldap_config']['ldap_peopleDN'],$config_array['ldap_config']['ldap_groupDN'],$config_array['ldap_config']['ldap_ssl'],$config_array['ldap_config']['ldap_port']);

//Initialize database
$sqlDataBase= new SQLDataBase($config_array['sql_config']['sql_host'],$config_array['sql_config']['sql_database'],$config_array['sql_config']['sql_user'],$config_array['sql_config']['sql_pass']);

include "includes/authenticate.php";

if(isset($_SESSION['username']) && isset($_SESSION['password']))
{
        if($authen->Authenticate($_SESSION['username'],$_SESSION['password'],""))
        {

                $resultsPath=$config_array['head_paths']['finalized_results_path'];
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
