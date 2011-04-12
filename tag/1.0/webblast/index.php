<?php
session_start();
include "includes/header.php";
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
		include "includes/logout.php";
		include "includes/ncbiLastUpdate.php";
		include "includes/navigation.php";
		if(isset($_GET['view']))
		{
			if($_GET['view']=='jobs')
			{
				include "includes/jobs.php";
			}
			elseif($_GET['view']=='queries')
			{
				include "includes/queries.php";
			}
			elseif($_GET['view']=='results')
			{
				include "includes/results.php";
			}
			elseif($_GET['view']=='query')
			{
				include "includes/query.php";
			}
			elseif($_GET['view']=='csvheader')
			{
				include "includes/csvheader.php";
			}
			elseif($_GET['view']=='managedatabases')
                        {
                                include "includes/databases.php";
                        }
			elseif($_GET['view']=='createdatabase')
                        {
                                include "includes/databaseUpload.php";
                        }
			elseif($_GET['view']=='clusterstatus')
                        {
                                include "includes/clusterStatus.php";
                        }
			else
			{	
				include "includes/uploadfasta_simple.php";
                                include "includes/mainform_simple.php";
			}
		}
		else {
			include "includes/uploadfasta_simple.php";
     	                include "includes/mainform_simple.php";
		}
		
	}
	else {
		include "includes/login.php";
	}
}
else {
	include "includes/login.php";
}
include "includes/footer.php";
?>
