<?php
$statusNew=1;
$statusRunning=2;
$statusCompleted=3;
$statusError=7;
$statusCanceled=4;
?>

<br>
<br>
<b>Queries for job #<?php echo $_GET['job']; ?></b>
<br><br>
<a href="admin.php?view=queries&job=<?php echo $_GET['job']; ?>&show=new">New</a> | <a href="admin.php?view=queries&job=<?php echo $_GET['job']; ?>&show=errors">Errors</a> | <a href="admin.php?view=queries&job=<?php echo $_GET['job']; ?>&show=running">Running</a> | <a href="admin.php?view=queries&job=<?php echo $_GET['job']; ?>&show=completed">Completed</a> | <a href="admin.php?view=queries&job=<?php echo $_GET['job']; ?>&show=canceled">Canceled</a>

<br>
<br>
<?php 
	if(isset($_GET['show']))
	{
		echo "Queries ".$_GET['show']; 
	}else{
		echo "Queries completed";
		$_GET['show']="completed";
	}
	echo ":</b>";
	if(isset($_GET['page']))
	{
		echo " Page (<b>".$_GET['page']."</b>) ";
		if($_GET['page']!=0)
		{
			echo "<a href=\"admin.php?view=queries&job=".$_GET['job']."&show=".$_GET['show']."&page=".($_GET['page']-1)."\"> Previous </a> | ";
		}
	}
	else{
		$_GET['page']=0;
		echo " Page (<b>".$_GET['page']."</b>) ";
	}
	echo "<a href=\"admin.php?view=queries&job=".$_GET['job']."&show=".$_GET['show']."&page=".($_GET['page']+1)."\"> Next </a>";
	
	if(isset($_GET['action']))
	{
		if($_GET['action']=="retryquery")
		{
			if($_GET['show']!="running")
			{
				$queryJob= new Job($sqlDataBase,$config_array);
				$queryJob->LoadJob($_GET['job']);
				$queryJob->ResetQuery($_GET['queryid']);	
			}
		}
	}
?>


<TABLE border=1>
<tr>
<td>
<b>Query ID</b>
</td>
<td>
<b>Chunk Size</b>
</td>
<td>
<b>Query Status</b>
</td>
<td>
<b>Run Time</b>
</td>
<td>
<b>Query Results</b>
</td>
</tr>
<?php

if(isset($_GET['show']))
{
	switch ($_GET['show']) {
		case "new":
			$showStatus=$statusNew;
			break;
		case "errors":
                        $showStatus=$statusError;
                        break;
		case "running":
			$showStatus=$statusRunning;
			break;
		case "completed":
			$showStatus=$statusCompleted;
			break;
		case "canceled":
			$showStatus=$statusCanceled;
			break;
		default:
			$showStatus=$statusCompleted;
	}
}else{
	$showStatus=$statusCompleted;
}

	$queryBlastQueries="SELECT q.chunksize, s.name, q.id, q.endtime, q.starttime, q.reservenode FROM blast_queries q, status s WHERE q.jobid=".$_GET['job']." AND q.statusid=".$showStatus." AND s.id=q.statusid AND q.statusid=".$showStatus." LIMIT ".($_GET['page']*50).",50";
	$blastQueries = $sqlDataBase->query($queryBlastQueries);
	
        if(!empty($blastQueries))
        {
                foreach($blastQueries as $id=>$assoc)
                {
			 $startTimeInSeconds=date('U',strtotime($assoc['starttime']));
			 $endTimeInSeconds=date('U',strtotime($assoc['endtime']));			
                         echo "<tr>";
                         echo "<td>".$assoc['id']."</td>";
                         echo "<td>".$assoc['chunksize']."</td>";
                         echo "<td>".$assoc['name']." (".$assoc['reservenode'].")</td>";
			 echo "<td>";
			 if($_GET['show']!="running")
                         {
				$seconds = $endTimeInSeconds-$startTimeInSeconds;
				$hours = floor($seconds / 3600);
    				$minutes = floor($seconds % 3600 / 60);
    				$seconds = $seconds % 60;
			 	echo sprintf("%d:%02d:%02d", $hours, $minutes, $seconds);
			 }
			 else {
				$seconds = time() -$startTimeInSeconds;
                                $hours = floor($seconds / 3600);
                                $minutes = floor($seconds % 3600 / 60);
                                $seconds = $seconds % 60;
                                echo sprintf("%d:%02d:%02d", $hours, $minutes, $seconds);
			 }
			 echo "</td>";
                         echo "<td><a href=\"admin.php?view=results&queryid=".$assoc['id']."&job=".$_GET['job']."\">Results</a> | <a href=\"admin.php?view=query&queryid=".$assoc['id']."&job=".$_GET['job']."\">View Query</a>";
			 if($_GET['show']!="running")
			 {
				echo " | <a href=\"admin.php?view=queries&queryid=".$assoc['id']."&job=".$_GET['job']."&show=".$_GET['show']."&page=".$_GET['page']."&action=retryquery\">Retry Query</a>| <a href=\"admin.php?view=draw&queryid=".$assoc['id']."&job=".$_GET['job']."&chunksize=".$assoc['chunksize']."\">Draw Results</a>";
			 }
                         echo "</tr>";
                }
        }
?>

</TABLE>
