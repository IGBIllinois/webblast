<?php

//Authenticate website access
if(isset($_POST['submitLogon']))
{	
	if($authen->Authenticate($_POST['loginname'],$_POST['loginpass'],""))
	{
		$queryCheckFirstTimeLogin="SELECT id FROM users WHERE netid=\"".$_POST['loginname']."\"";
		if($sqlDataBase->countQuery($queryCheckFirstTimeLogin)==0)
		{
			$queryAddUser="INSERT INTO users (first,last,netid,email,description) VALUES (\"\",\"\",\"".$_POST['loginname']."\",\"".$_POST['loginname']."@igb.illinois.edu\",\"\")";
			$sqlDataBase->nonSelectQuery($queryAddUser);
		}
		else
		{
			$_SESSION['username']=$_POST['loginname'];
			$_SESSION['password']=$_POST['loginpass'];
			$userid = $sqlDataBase->singleQuery($queryCheckFirstTimeLogin);
			$_SESSION['userid']=$userid;
		}

	}
	else
	{
		echo "Couldn't authenticate";
	}
}

//Authenticate downloading results have to use this to make downloading useing authenticated 'wget'
if(isset($_SERVER['PHP_AUTH_USER']))
{
	if($authen->Authenticate($_POST['PHP_AUTH_USER'],$_POST['PHP_AUTH_PW'],""))
        {
		echo "authenticated";
                $queryCheckFirstTimeLogin="SELECT id FROM users WHERE netid=\"".$_SERVER['PHP_AUTH_USER']."\"";
                if($sqlDataBase->countQuery($queryCheckFirstTimeLogin)==0)
                {
                        $queryAddUser="INSERT INTO users (first,last,netid,email,description) VALUES (\"\",\"\",\"".$_SERVER['PHP_AUTH_USER']."\",\"".$_SERVER['PHP_AUTH_USER']."@igb.illinois.edu\",\"\")";
                        $sqlDataBase->nonSelectQuery($queryAddUser);
                }
                else
                {
                        $_SESSION['username']=$_SERVER['PHP_AUTH_USER'];
                        $_SESSION['password']=$_SERVER['PHP_AUTH_PW'];
                        $userid = $sqlDataBase->singleQuery($queryCheckFirstTimeLogin);
                        $_SESSION['userid']=$userid;
                }

        }
        else
        {
                echo "Couldn't authenticate";
        }

}

if(isset($_POST['submitlogout']))
{
	unset($_SESSION['username']);
	unset($_SESSION['password']);
	unset($_SESSION['userid']);
}

?>
