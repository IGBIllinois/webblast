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
<a href="index.php?view=queries&job=<?php echo $_GET['job']; ?>&show=new">New</a> | <a href="index.php?view=queries&job=<?php echo $_GET['job']; ?>&show=errors">Errors</a> | <a href="index.php?view=queries&job=<?php echo $_GET['job']; ?>&show=running">Running</a> | <a href="index.php?view=queries&job=<?php echo $_GET['job']; ?>&show=completed">Completed</a> | <a href="index.php?view=queries&job=<?php echo $_GET['job']; ?>&show=canceled">Canceled</a>

<br>
<br>

<b>
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
			echo "<a href=\"index.php?view=queries&job=".$_GET['job']."&show=".$_GET['show']."&page=".($_GET['page']-1)."\"> Previous </a> | ";
		}
	}
	else{
		$_GET['page']=0;
		echo " Page (<b>".$_GET['page']."</b>) ";
	}
	echo "<a href=\"index.php?view=queries&job=".$_GET['job']."&show=".$_GET['show']."&page=".($_GET['page']+1)."\"> Next </a>";
	
	if(isset($_GET['action']))
	{
		if($_GET['action']=="retryquery")
		{
			if($_GET['show']!="running")
			{
				$queryJob= new Job($sqlDataBase);
				$queryJob->LoadJob($_GET['job']);
				$queryJob->ResetQuery($_GET['queryid']);	
			}
		}
	}
?>


<TABLE border=1>
<tr>
<th>
<b>Query ID</b>
</th>
<th>
<b>Chunk Size</b>
</th>
<th>
<b>Query Status</b>
</th>
<th>
<b>Run Time</b>
</th>
<th>
<b>Query Results</b>
</th>
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

$queryJobPermission = "SELECT u.netid FROM blast_jobs j, users u WHERE j.id=".$_GET['job']." AND j.userid=u.id AND u.netid=\"".$_SESSION['username']."\"";
if($sqlDataBase->countQuery($queryJobPermission) > 0 )
{
	$queryBlastQueries="SELECT q.chunksize, s.name, q.id, q.endtime, q.starttime FROM blast_queries q, status s  WHERE q.jobid=".$_GET['job']." AND q.statusid=".$showStatus." AND s.id=q.statusid AND q.statusid=".$showStatus." LIMIT ".($_GET['page']*50).",50";
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
                         echo "<td>".$assoc['name']."</td>";
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
                                echo "n/a";
			 }
			 echo "</td>";
                         echo "<td><a href=\"index.php?view=results&queryid=".$assoc['id']."&job=".$_GET['job']."\">Results</a> | <a href=\"index.php?view=query&queryid=".$assoc['id']."&job=".$_GET['job']."\">View Query</a>";
			 if($_GET['show']!="running")
			 {
				echo " | <a href=\"index.php?view=queries&queryid=".$assoc['id']."&job=".$_GET['job']."&show=".$_GET['show']."&page=".$_GET['page']."&action=retryquery\">Retry Query</a>";
			 }
                         echo "</tr>";
                }
        }
	
	
}
?>

</TABLE>
