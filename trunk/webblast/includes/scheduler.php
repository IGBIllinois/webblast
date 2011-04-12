<?php
if(isset($_GET['action']))
{
	if($_GET['action']=="delete" && isset($_GET['schedid']))
	{
		$queryDeleteSchedule="DELETE FROM schedule WHERE id=".$_GET['schedid']."";
		$sqlDataBase->nonSelectQuery($queryDeleteSchedule);	
	}
}

if(isset($_POST['submitScheduler']))
{
	$date = date("Y-m-d H:i:s",mktime($_POST['hour'],$_POST['minutes'],0,$_POST['month'],$_POST['day'],$_POST['year']));
	$queryAddSchedule="INSERT INTO schedule (date,nodes,description) VALUES(\"".$date."\",".$_POST['nodes'].",\"".$_POST['description']."\")";
	$sqlDataBase->nonSelectQuery($queryAddSchedule);

}

?>
<FORM ACTION="admin.php?view=scheduler" METHOD="POST">
<b>Schedule the number of nodes to run after a chosen time.</b><br><br>
Time: <INPUT TYPE="text" size="2" name="hour" VALUE="<?php echo date("G"); ?>">:<INPUT TYPE="text" size="2" name="minutes" VALUE="<?php echo date("i"); ?>"><br>
Date: <INPUT TYPE="text" size="2" name="day" VALUE="<?php echo date("j"); ?>">/<INPUT TYPE="text" size="2" name="month" VALUE="<?php echo date("n"); ?>">/<INPUT TYPE="text" size="4" name="year" VALUE="<?php echo date("Y"); ?>"><br>
Nodes to run: <INPUT type="text" name="nodes" VALUE=0 SIZE=3><br>
Description: <INPUT type="text" name="description" SIZE=20><br>
<br>
<INPUT TYPE="submit" NAME="submitScheduler" VALUE="Submit"><br>
<br>
<b>Schedule</b>
<TABLE border=1>
<tr>
<th>
<b>Date/Time</b>
</th>
<th>
<b>Nodes</b>
</th>
<th>
<b>Description</b>
</th>
<th>
<b>Options</b>
</th>
<?php
$querySchedule="SELECT date,nodes,id,description FROM schedule ORDER BY date DESC";
$scheduleArray=$sqlDataBase->query($querySchedule);
foreach($scheduleArray as $id=>$assoc)
{
	echo "<tr>";
	echo "<td>".$assoc['date']."</td>";
	echo "<td>".$assoc['nodes']."</td>";
	echo "<td>".$assoc['description']."</td>";
	echo "<td><a href=\"admin.php?view=scheduler&action=delete&schedid=".$assoc['id']."\">Delete</a></td>";
	echo "<tr>";
}




?>
</FORM>
