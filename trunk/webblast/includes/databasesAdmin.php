<?php

if(isset($_POST['submitNCBIDataBase']))
{
	$uploadsDirectory=getcwd()."/uploads/";
	$querySubmitDataBase="INSERT INTO dbs (dbname,type,description,userid,active) VALUES (\"".$_POST['dbName']."\",\"".$_POST['dbType']."\",\"".$_POST['dbDescription']."\",0,1)";
	$sqlDataBase->nonSelectQuery($querySubmitDataBase);
}

if(isset($_POST['submitUserDataBase']))
{
        $uploadsDirectory=getcwd()."/uploads/";
        $querySubmitDataBase="INSERT INTO dbs (dbname,type,description,userid,active) VALUES (\"".$_POST['dbName']."\",\"".$_POST['dbType']."\",\"".$_POST['dbDescription']."\",".$_POST['userid'].",1)";
	echo $querySubmitDataBase;
        $sqlDataBase->nonSelectQuery($querySubmitDataBase);
}

if(isset($_GET['action']))
{
	if($_GET['action']=="delete")
	{
		$statusDeleting = 5;
		$queryDeleteDatabase="UPDATE dbs SET active=".$statusDeleting." WHERE id=".$_GET['dbid'];
		$sqlDataBase->nonSelectQuery($queryDeleteDatabase);
	}
	if($_GET['action']=="activate")
	{
		$statusActive = 1;
		$queryShareDatabase="UPDATE dbs SET active=".$statusActive." WHERE id=".$_GET['dbid'];
		$sqlDataBase->nonSelectQuery($queryShareDatabase);
	}
	if($_GET['action']=="deactivate")
	{
		$statusNotActive = 0;
		$queryUnshareDatabase="UPDATE dbs SET active=".$statusNotActive." WHERE id=".$_GET['dbid'];
		$sqlDataBase->nonSelectQuery($queryUnshareDatabase);
	}
}
?>

<b>Latest NCBI Update On Nodes:</b><br><br>
<?php
$queryAvailableUpdates="SELECT id, updatestatus, updatedate FROM dbupdates ORDER BY id DESC LIMIT 1";
$availableUpdates = $sqlDataBase->query($queryAvailableUpdates);

echo "Last Update: ".$availableUpdates[0]['updatedate'];
if($availableUpdates[0]['updatestatus']==7)
{
	echo "<br>Status: error.";
}
else {
	echo "<br>Status: success.";
}
?>
<br><br>

<b>Add NCBI Database:</b><br><br>
<FORM ACTION="admin.php?view=databases" NAME="DataBaseSubmissionForm" METHOD="POST" enctype="multipart/form-data">
Database name:<INPUT TYPE="TEXT" NAME="dbName">(Name must match database .pin or .nin file name)<br>
Database description: <INPUT TYPE="TEXT" NAME="dbDescription"><br>
Database type: 
<SELECT name="dbType">
<option value="p">Protein</option>
<option value="n">nucleotide</option>
</SELECT><br>
<br>
<INPUT TYPE="submit" value="Submit NCBI Database" name="submitNCBIDataBase"><br>
</FORM>


<?php
$queryUserIds = "SELECT id,netid, first, last FROM users";
$userIds = $sqlDataBase->query($queryUserIds);

?>
<b>Add User Database Manually:</b><br><br>
<FORM ACTION="admin.php?view=databases" NAME="DataBaseSubmissionForm" METHOD="POST" enctype="multipart/form-data">
User: <SELECT name="userid">
<?php
foreach($userIds as $id=>$userid)
{
	echo "<option value=\"".$userid['id']."\">".$userid['netid']."</option>";

}
?>
</SELECT><br>
Database name:<INPUT TYPE="TEXT" NAME="dbName">(Name must match database .pin or .nin file name)<br>
Database description: <INPUT TYPE="TEXT" NAME="dbDescription"><br>
Database type:
<SELECT name="dbType">
<option value="p">Protein</option>
<option value="n">nucleotide</option>
</SELECT><br>
<br>
<INPUT TYPE="submit" value="Submit User Database" name="submitUserDataBase"><br>
</FORM>

<br>
<b>User Databases:</b>
<table border=1>
<tr>
<th>
<b>User</b>
</th>
<th>
<b>Name</b>
</th>
<th>
<b>Type</b>
</th>
<th>
<b>Description</b>
</th>
<th>
<b>Date</b>
</th>
<th>
<b>Options</b>
</th>
</tr>
<?php
$queryDatabases="SELECT d.id,d.dbname,d.description,d.type,d.active,u.netid,last_update FROM dbs d, users u WHERE u.id=d.userid AND userid>0";
$databasesArray=$sqlDataBase->query($queryDatabases);

foreach($databasesArray as $id=>$assoc)
{
	echo "<tr>";
	echo "<td>".$assoc['netid']."</td>";
	echo "<td>".$assoc['dbname']."</td>";
	echo "<td>".$assoc['type']."</td>";
	echo "<td>".$assoc['description']."</td>";
	echo "<td>".$assoc['last_update']."</td>";
	echo "<td><a href=\"admin.php?view=databases&action=delete&dbid=".$assoc['id']."\">Delete</a> | ";
	if($assoc['active']==1)
	{
		echo "<a href=\"admin.php?view=databases&action=deactivate&dbid=".$assoc['id']."\">Unshare</a>";
	}
	elseif($assoc['active']==2){
		echo "Uploaded";
	}
	elseif($assoc['active']==3){
                echo "Transfering";
        }
	elseif($assoc['active']==4)
	{
		echo "Error";
	}
	elseif($assoc['active']==5)
        {
                echo "Deleting";
        }
	elseif($assoc['active']==6)
	{
		echo "Deleted";
	}
	else{
		echo "<a href=\"admin.php?view=databases&action=activate&dbid=".$assoc['id']."\">Share</a>";
	}
	echo "</td>";
	echo "</tr>";
}

?>
</table>

<br>
<b>NCBI Databases:</b>
<table border=1>
<tr>
<th>
<b>Name</b>
</th>
<th>
<b>Type</b>
</th>
<th>
<b>Description</b>
</th>
<th>
<b>Updated</b>
</th>
<th>
<b>Options</b>
</th>
</tr>
<?php
$queryDatabases="SELECT id,dbname,description,type,active,last_update FROM dbs WHERE userid=0";
$databasesArray=$sqlDataBase->query($queryDatabases);

foreach($databasesArray as $id=>$assoc)
{
        echo "<tr>";
        echo "<td>".$assoc['dbname']."</td>";
        echo "<td>".$assoc['type']."</td>";
        echo "<td>".$assoc['description']."</td>";
	echo "<td>".$assoc['last_update']."</td>";
        echo "<td><a href=\"admin.php?view=databases&action=delete&dbid=".$assoc['id']."\">Delete</a> | ";
        if($assoc['active']==1)
        {
                echo "<a href=\"admin.php?view=databases&action=deactivate&dbid=".$assoc['id']."\">Unshare</a>";
        }
        elseif($assoc['active']==2){
                echo "Uploaded";
        }
        elseif($assoc['active']==3){
                echo "Transfering";
        }
        elseif($assoc['active']==4)
        {
                echo "Error";
        }
        elseif($assoc['active']==5)
        {
                echo "Deleting";
        }
        elseif($assoc['active']==6)
        {
                echo "Deleted";
        }
        else{
                echo "<a href=\"admin.php?view=databases&action=activate&dbid=".$assoc['id']."\">Share</a>";
        }
        echo "</td>";
        echo "</tr>";
}

?>
</table>


