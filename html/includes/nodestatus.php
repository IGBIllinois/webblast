<b>Nodes Status</b>
<br>
<br>
<TABLE border=1>
<tr>
<th>
<b>ID</b>
</th>
<th>
<b>Status</b>
</th>
<th>
<b>Slave Node</b>
</th>
<th>
<b>Process ID</b>
</th>
<th>
<b>Commmand</b>
</th>
<th>
<b>Processes</b>
</th>
</tr>
<?php
$queryNodeStatus="SELECT n.id,s.name, c.command, n.hostname, n.pid, n.processes FROM nodes n, status s, commands c WHERE s.id=n.status AND c.id=n.commandid ORDER BY n.id ASC";
$nodeStatusArray=$sqlDataBase->query($queryNodeStatus);
foreach($nodeStatusArray as $id=>$assoc)
{
	echo "<tr>";
	echo "<td>".$assoc['id']."</td>";
	echo "<td>".$assoc['name']."</td>";
	echo "<td>".$assoc['hostname']."</td>";
	echo "<td>".$assoc['pid']."</td>";
	echo "<td>".$assoc['command']."</td>";
	echo "<td>".$assoc['processes']."</td>";
	echo "</tr>";
}
?>


</TABLE>
