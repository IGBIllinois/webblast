<?php
if(isset($_POST['submitAdminLogon']))
{
	if($authen->Authenticate($_POST['loginname'],$_POST['loginpass'],$adminGroup))
	{
		$queryCheckFirstTimeLogin="SELECT id FROM users WHERE netid=\"".$_POST['loginname']."\"";
		if($sqlDataBase->countQuery($queryCheckFirstTimeLogin)==0)
		{
			$queryAddUser="INSERT INTO users (first,last,netid,email,description) VALUES (\"\",\"\",\"".$_POST['loginname']."\",\"".$_POST['loginname']."@igb.illinois.edu\",\"\")";
			$sqlDataBase->nonSelectQuery($queryAddUser);
		}
		$_SESSION['usernameAdmin']=$_POST['loginname'];
		$_SESSION['passwordAdmin']=$_POST['loginpass'];
		$_SESSION['group']="cnrg";
		$userid = $sqlDataBase->singleQuery($queryCheckFirstTimeLogin);
		$_SESSION['userid']=$userid;
		
	}
}

if(isset($_POST['submitAdminLogout']))
{
	unset($_SESSION['usernameAdmin']);
	unset($_SESSION['passwordAdmin']);
	unset($_SESSION['group']);
	unset($_SESSION['userid']);
}

?>
