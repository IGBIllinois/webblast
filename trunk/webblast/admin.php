<?php
session_start();
error_reporting(E_ALL);
include "includes/header.php";

function __autoload($class_name) {
    require_once 'classes/' . $class_name . '.php';
}
include "includes/config.php";

//Initialize database
echo 0;
$sqlDataBase= new SQLDataBase($config_array['sql_config']['sql_host'],$config_array['sql_config']['sql_database'],$config_array['sql_config']['sql_user'],$config_array['sql_config']['sql_pass']);
echo 1;
//initialize ldap authentication object
$authen=new Auth($sqlDataBase,$config_array);
echo 2;
//Authenticate submit login
include "includes/authenticate.php";
echo 3;
if($authen->AuthSessionAdmin())
{
                include "includes/logout.php";
		echo "<table class=\"content\"><tr><td class=\"content\">";
		if(!isset($_GET['view']))
		{
			$_GET['view']="databases";
		}
                include "includes/navigationAdmin.php";
		echo "</td><td class=\"content_right\">";

                if(isset($_GET['view']))
                {
                       
                        if($_GET['view']=='scheduler')
                        {
                                include "includes/scheduler.php";
                        }
                        elseif($_GET['view']=='databases')
                        {
                                include "includes/databasesAdmin.php";
                        }
			elseif($_GET['view']=='nodestatus')
			{
				include "includes/nodestatus.php";
			}
			elseif($_GET['view']=='jobs')
			{
				include "includes/jobsAdmin.php";
			}
			elseif($_GET['view']=='queries')
			{
				include "includes/queriesAdmin.php";
			}
			elseif($_GET['view']=='results')
			{
				include "includes/results.php";
			}
			elseif($_GET['view']=='draw')
                        {
                                include "includes/drawAdmin.php";
                        }
			elseif($_GET['view']=='paths')
			{
				include "includes/configPathsAdmin.php";
			}
			elseif($_GET['view']=='query')
                        {
                                include "includes/query.php";
                        }
			elseif($_GET['view']=='userconf')
			{
				include "includes/userConfigAdmin.php";
			}
			elseif($_GET['view']=='hostview')
			{
				include "includes/host_view.php";
			}
			elseif($_GET['view']=='uploaddatabase')
			{
				include "includes/databaseUploadAdmin.php";
			}
			elseif($_GET['view']=='clusterstatus')
			{
				include "includes/clusterStatus.php";
			}
			
                }
                else {
			include "includes/databasesAdmin.php";
                }
		echo "</td></tr></table";
}
else {
        include "includes/loginAdmin.php";
}
include "includes/footer.php";
?>

