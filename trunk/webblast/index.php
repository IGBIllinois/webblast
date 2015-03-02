<?php
session_start();
error_reporting(-1);
ini_set('upload_max_filesize', '2000MB');
ini_set('post_max_size', '2000MB');
include "includes/header.php";

function __autoload($class_name) {
	require_once 'classes/' . $class_name . '.php';
}

include "includes/config.php";

//Initialize database
$sqlDataBase= new SQLDataBase($config_array['sql_config']['sql_host'],$config_array['sql_config']['sql_database'],$config_array['sql_config']['sql_user'],$config_array['sql_config']['sql_pass']);

//initialize ldap authentication object
$authen=new Auth($sqlDataBase,$config_array);

//Authenticate submit login
include "includes/authenticate.php";

if($authen->AuthSession())
{
		include "includes/logout.php";
		echo "<font color=\"blue\"><b>You may now increase/decrease your job's priority using the +/- signs under the View Jobs tab.
<br>This feature is best used when your job is small and there are very large jobs ahead of it in the queue.</b></font><br><br>";

		//include "includes/ncbiLastUpdate.php";
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
			elseif($_GET['view']=='draw')
			{
				include "includes/draw.php";
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
			elseif($_GET['view']=='class')
                        {
				include "includes/uploadfasta_simple.php";
                                include "includes/class_simple.php";
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
include "includes/footer.php";
?>
