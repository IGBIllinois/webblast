
<?php

/*Type can be set to"
cpu_report
mem_report
load_report
network_report
packet_report
*/


function StatusTable($title,$type, $numCols, SQLDataBase $sqlDataBase)
{

	$htmlString = "<b>".$title.":</b>";
	$htmlString .= "<table>";
	$htmlString .="<tr><th colspan=".$numCols.">Head Node</th></tr>";
	$htmlString .= "<tr><td colspan=".$numCols."><img src=\"includes/graph.php?g=".$type."&m=load_one&z=small&c=IGB%20Biology%20Cluster&h=biocluster.local&l=e2ecff&v=0.09&x=0&n=0&r=hour&st=".Time()."\"></td></tr>";
	$htmlString .="<tr><th colspan=".$numCols.">Computation Nodes</th>";
	$statusStarted = 2;
	$queryStartNodes = "SELECT hostname FROM nodes WHERE status=".$statusStarted;
	$startNodes = $sqlDataBase->query($queryStartNodes);

	$i=0;
	if(isset($startNodes))
	{
		foreach($startNodes as $id=>$nodeInfo)
		{
			if($i % $numCols == 0)
			{
				$htmlString.= "</tr><tr>";
			}

			$htmlString .="<td><img src=\"includes/graph.php?g=".$type."&m=load_one&z=small&c=IGB%20Biology%20Cluster&h=".$nodeInfo['hostname'].".local&l=e2ecff&v=0.00&x=0&n=0&r=hour&st=".Time()."\"></td>";
			$i++;
		}
	}

	$extraBoxes=$i%$numCols;
	if($extraBoxes)
	{
		$htmlString .= "<td spawn=".(($i)%$numCols).">";
	}
	$htmlString .=  "</tr></table>";
		
	return $htmlString;
}
?>
<b>Job Queue:</b>
<TABLE border=1 CELLPADDING="5">
<tr>
	<th>
	<b>Job ID</b>
	</th>
	<th>
	<b>Program</b>
</th>
<th>
	<b>Submit Date</b>
</th>
<th>
	<b>Progress</b>
</th>
</tr>
<?php
$statusDeleting=10;
$statusNew = 1;
$statusCompleted=3;
$queryJobsArray= "SELECT j.name,j.submitDate,j.completeDate,j.queriesadded,j.queriescompleted,j.id,j.status, s.name AS statusname, u.netid, b.command FROM blast_jobs j, users u, status s, blasts b WHERE j.userid=u.id AND s.id=j.status AND j.status=".$statusNew." AND b.id = j.blastid ORDER BY id DESC";
$userJobsArray = $sqlDataBase->query($queryJobsArray);

if(isset($userJobsArray))
{
        foreach($userJobsArray as $id=>$assoc)
        {
                echo "<tr>";
                echo "<td><center>".$assoc['id']."</center></td>";
		echo "<td><center>".$assoc['command']."</center></td>";
                echo "<td>".$assoc['submitDate']."</td>";
                //echo "<td><a href=\"admin.php?view=queries&job=".$assoc['id']."&action=showqueries\">".$assoc['queriescompleted']." / ".$assoc['queriesadded']."</a></td>";
                echo "<td><div class=\"progress-container\"><div style=\"width: ".(($assoc['queriescompleted'] / $assoc['queriesadded'])*100)."%\"> ".$assoc['queriescompleted']."/".$assoc['queriesadded']." </div></div></td>";
                echo "</tr>";

        }
}
?>
</TABLE>
<br>
<?php
echo StatusTable("Load Report","load_report",4,$sqlDataBase);
echo "<br><br>";
echo StatusTable("CPU Report","cpu_report",4,$sqlDataBase);
echo "<br><br>";
echo StatusTable("Memory Report","mem_report",4,$sqlDataBase);
echo "<br><br>";
echo StatusTable("Network Report","network_report",4,$sqlDataBase);
?>
