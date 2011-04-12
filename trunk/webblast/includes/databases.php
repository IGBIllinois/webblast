<?php

if(isset($_GET['action']))
{
	if($_GET['action']=="delete")
	{
		$statusDeleting = 5;
		$queryDeleteDatabase="UPDATE dbs SET active=".$statusDeleting." WHERE id=".$_GET['dbid']." AND userid=".$_SESSION['userid'];
		$sqlDataBase->nonSelectQuery($queryDeleteDatabase);
	}
	if($_GET['action']=="share")
	{
		$statusActive = 1;
		$queryActivateDatabase="UPDATE dbs SET active=".$statusActive." WHERE id=".$_GET['dbid']." AND userid=".$_SESSION['userid'];
		$sqlDataBase->nonSelectQuery($queryActivateDatabase);
	}
	if($_GET['action']=="unshare")
	{
		$statusNotActive = 0;
		$queryDeactivateDatabase="UPDATE dbs SET active=".$statusNotActive." WHERE id=".$_GET['dbid']." AND userid=".$_SESSION['userid'];;
		$sqlDataBase->nonSelectQuery($queryDeactivateDatabase);
	}
}
?>

<b><font size="2">(You can unshare your database with other users by clicking on unshare under the Options column.)</font></b><br>
<table CELLPADDING="5" border=1>
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
<b>Options</b>
</th>
</tr>
<?php
$queryDatabases="SELECT d.id,d.dbname,d.description,d.type,d.active,u.netid FROM dbs d, users u WHERE d.userid =".$_SESSION['userid']." AND u.id=d.userid";
$databasesArray=$sqlDataBase->query($queryDatabases);

if($databasesArray)
{
	foreach($databasesArray as $id=>$assoc)
	{
		echo "<tr>";
		echo "<td>".$assoc['netid']."</td>";
		echo "<td>".$assoc['dbname']."</td>";
		echo "<td>".$assoc['type']."</td>";
		echo "<td>".$assoc['description']."</td>";
		echo "<td>";
		if($assoc['active']==1 || $assoc['active']==0)
		{
			echo "<a href=\"index.php?view=managedatabases&action=delete&dbid=".$assoc['id']."\">Delete</a> | ";
		}
		if($assoc['active']==1)
		{
			echo "<a href=\"index.php?view=managedatabases&action=unshare&dbid=".$assoc['id']."\">Unshare</a>";
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
			echo "<a href=\"index.php?view=managedatabases&action=share&dbid=".$assoc['id']."\">Share</a>";
		}
		echo "</td>";
		echo "</tr>";
	}
}
?>
</table>

