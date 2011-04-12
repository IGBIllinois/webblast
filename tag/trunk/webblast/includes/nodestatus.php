<br>
<br>
<b>Nodes Status</b>
<br>
<br>
<TABLE border=1>
<tr>
<td>
<b>ID</b>
</td>
<td>
<b>Status</b>
</td>
<td>
<b>Slave Node</b>
</td>
<td>
<b>Process ID</b>
</td>
<td>
<b>Commmand</b>
</td>
<td>
<b>Processes</b>
</td>
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
