<?php

if(isset($_POST['submitUserConfig']))
{
	$queryUpdateUser="UPDATE users SET netid=\"".$_POST['netId']."\", email=\"".$_POST['eMail']."\", dropboxpath=\"".$_POST['dropBoxPath']."\", description=\"".$_POST['description']."\" WHERE id=".$_POST['userid'];
	$sqlDataBase->nonSelectQuery($queryUpdateUser);
}
if(isset($_POST['submitSelectUser']))
{
	$queryUserInfo="SELECT * FROM users WHERE id=".$_POST['selectedUser'];
	$userInfoArray=$sqlDataBase->query($queryUserInfo);
}else{
	$queryUserInfo="SELECT * FROM users ORDER BY id LIMIT 1";
	$userInfoArray=$sqlDataBase->query($queryUserInfo);
}

$queryUsers="SELECT * FROM users";
$usersArray=$sqlDataBase->query($queryUsers);
?>
<b>User Configuration</b>
<br>
<br>
Select User:
<FORM ACTION="admin.php?view=userconf" METHOD="POST">
<SELECT name="selectedUser">
<?php
foreach($usersArray as $id=>$assoc)
{
	echo "<OPTION VALUE=".$assoc[id].">".$assoc['netid']."</OPTION>";
}
?>
</SELECT>
<INPUT TYPE="submit" VALUE="Select" NAME="submitSelectUser">
</FORM>

<br>
User Details:
<FORM ACTION="admin.php?view=userconf" METHOD="POST">
<TABLE border=1>
<tr>
<td>
Userid:
</td>
<td>
<INPUT TYPE="TEXT" VALUE="<?php echo $userInfoArray[0]["netid"]; ?>" NAME="netId" SIZE=45>
</td>
</tr>
<tr>
<td>
E-Mail:
</td>
<td>
<INPUT TYPE="TEXT" VALUE="<?php echo $userInfoArray[0]["email"]; ?>" NAME="eMail" SIZE=45>
</td>
</tr>
<tr>
<td>
Drop Box:
</td>
<td>
<INPUT TYPE="TEXT" VALUE="<?php echo $userInfoArray[0]["dropboxpath"]; ?>" NAME="dropBoxPath" SIZE=45>
</td>
</tr>
<tr>
<td>
Notes:
</td>
<td>
<TEXTAREA NAME="description" cols="52" rows="5"><?php echo $userInfoArray[0]["description"]; ?></TEXTAREA>
</td>
</tr>
<tr>
<td>
</td>
<td>
<INPUT TYPE="hidden" VALUE="<?php echo $userInfoArray[0]["id"]; ?>" NAME="userid">
<INPUT TYPE="submit" VALUE="Update User" NAME="submitUserConfig">
</td>
</tr>
</TABLE>
</FORM>
