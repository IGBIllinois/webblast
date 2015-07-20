<?php

if(isset($_GET['action']))
{
	$userDatabase = new Database($sqlDataBase,$config_array);
	$databaseId = mysql_real_escape_string($_GET['dbid']);
	$userDatabase->LoadDatabase($databaseId);
	
	switch ($_GET['action']) {

		case "delete":
			$userDatabase->DeleteDatabase();
			break;
		case "share":
			$userDatabase->shareDatabase();
			break;
		case "unshare":
			$userDatabase->unShareDatabase();
			break;
	}
}
?>

<p>You can unshare your database with other users by clicking on unshare under the Options column.)</p>

<table class='table table-stripped'>
<tr>
<th>User</th>
<th>Name</th>
<th>Type</th>
<th>Description</th>
<th>Options</th>
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

