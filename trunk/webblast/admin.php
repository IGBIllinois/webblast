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

include "includes/authenticateAdmin.php";

if(isset($_SESSION['usernameAdmin']) && isset($_SESSION['passwordAdmin']) && isset($_SESSION['group']))
{
        if($authen->Authenticate($_SESSION['usernameAdmin'],$_SESSION['passwordAdmin'],"cnrg"))
        {
                include "includes/logoutAdmin.php";
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
}
else {
        include "includes/loginAdmin.php";
}
include "includes/footer.php";
?>

