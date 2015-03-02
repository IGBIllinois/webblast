<?php

//Authenticate website access
if(isset($_POST['submitLogon']))
{	
	$username = mysql_real_escape_string($_POST['loginname']);
	$password = $_POST['loginpass'];

	if($authen->AuthLogin($username,$password))
	{
		
	}
	else
	{
		echo "Couldn't authenticate";
	}
}

if(isset($_POST['submitAdminLogon']))
{
        $username = mysql_real_escape_string($_POST['loginname']);
        $password = $_POST['loginpass'];

        if($authen->AuthAdminLogin($username,$password))
        {

        }
        else
        {
                echo "Couldn't authenticate";
        }
}

if(isset($_POST['submitlogout']))
{
	$authen->AuthLogout();
}

?>
