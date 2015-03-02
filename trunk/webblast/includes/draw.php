<?php
include "includes/txt2html.php";
$listPerPage = 20;

if(isset($_GET['index']))
{
	$index=$_GET['index'];	
}
else
{
	$index = 1;
}

$numberOfIndexPoints = ceil($_GET['chunksize'] / $listPerPage);


for($noip = 1; $noip < $numberOfIndexPoints; $noip++)
{
	echo " <a href=\"index.php?view=".$_GET['view']."&queryid=".$_GET['queryid']."&job=".$_GET['job']."&chunksize=".$_GET['chunksize']."&index=".$noip."\">";
	if($index == $noip)
	{
		echo "<font color=\"black\">".$noip."</font>";
	}
	else
	{
		echo $noip;
	}
	echo "</a>";
}

echo "<br><br>";
for($i=$index*$listPerPage; $i<=($index*$listPerPage+$listPerPage); $i++)
{	
	if($i<$_GET['chunksize'])
	{
		echo "<table cellspacing=\"10\">";
		echo "<tr><td><img src=\"drawResults.php?job=".$_GET['job']."&queryid=".$_GET['queryid']."&query_num=".$i."\"></td></tr>";
		echo "</table>";
		echo "<br>";
	}
}
?>
