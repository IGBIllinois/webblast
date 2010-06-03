<?php
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
}

if(isset($_POST['submitlogout']))
{
	unset($_SESSION['username']);
	unset($_SESSION['password']);
	unset($_SESSION['userid']);
}

?>
