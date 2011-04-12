
<br>
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
	$htmlString .= "<tr><td colspan=".$numCols."><img src=\"includes/graph.php?g=".$type."&m=load_one&z=small&c=Classroom%20Cluster&h=clcluster.local&l=e2ecff&v=0.09&x=0&n=0&r=hour&st=".Time()."\"></td></tr>";
	$htmlString .="<tr><th colspan=5>Computation Nodes</th>";
	$statusStarted = 2;
	$queryStartNodes = "SELECT hostname FROM nodes WHERE status=".$statusStarted;
	$startNodes = $sqlDataBase->query($queryStartNodes);

	$i=0;
	foreach($startNodes as $id=>$nodeInfo)
	{
		if($i % $numCols == 0)
		{
			$htmlString.= "</tr><tr>";
		}

		$htmlString .="<td><img src=\"includes/graph.php?g=".$type."&m=load_one&z=small&c=Classroom%20Cluster&h=".$nodeInfo['hostname'].".local&l=e2ecff&v=0.09&x=0&n=0&r=hour&st=".Time()."\"></td>";
		$i++;
	}

	$htmlString .= "<td spawn=".($i%$numCols).">";
	$htmlString .=  "</tr></table>";
		
	return $htmlString;
}
echo StatusTable("User Load Report","",5,$sqlDataBase);
echo "<br><br>";
echo StatusTable("Memory Report","mem_report",5,$sqlDataBase);
echo "<br><br>";
echo StatusTable("Network Report","network_report",5,$sqlDataBase);
?>
