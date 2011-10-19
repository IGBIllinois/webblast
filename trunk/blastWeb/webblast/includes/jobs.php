<?php
include "includes/txt2html.php";

$statusDeleting=10;
$queryJobsArray= "SELECT j.name,j.submitDate,j.completeDate,j.queriesadded,j.queriescompleted,j.id,j.status, s.name AS statusname,j.priority FROM blast_jobs j, users u, status s WHERE u.netid=\"".$_SESSION['username']."\" AND j.userid=u.id AND s.id=j.status AND j.status!=".$statusDeleting." ORDER BY id DESC";
$userJobsArray = $sqlDataBase->query($queryJobsArray);
$statusCompleted=3;

if(isset($_GET['action']))
{
   $queryJobPermission = "SELECT u.netid FROM blast_jobs j, users u WHERE j.id=".$_GET['job']." AND j.userid=u.id AND u.netid=\"".$_SESSION['username']."\"";
   if($sqlDataBase->countQuery($queryJobPermission) > 0 )
   {
	if($_GET['action']=='incprior')
        {
                $jobToInc = new Job($sqlDataBase,$config_array);
                $jobToInc->LoadJob($_GET['job']);
		$blastnId = 1;
		if( ((($jobToInc->GetQueriesAdded() < 10000 && $jobToInc->GetBlastId()==$blastnId) || ($jobToInc->GetQueriesAdded() < 4000 && $jobToInc->GetBlastId()!= $blastnId)) && $jobToInc->GetPriority()<1) 
			|| ((($jobToInc->GetQueriesAdded() < 1000 && $jobToInc->GetBlastId()==$blastnId) || ($jobToInc->GetQueriesAdded() < 400 && $jobToInc->GetBlastId()!= $blastnId)) && $jobToInc->GetPriority()<2)  )
		{
                	$jobToInc->SetPriority($jobToInc->GetPriority()+1);
		}
		else
		{
			echo "<br>
				<table>
				<tr>
					<td colspan=3><center><b>Priority Rules</b></center></td>
				</tr>
				<tr>
					<td><b>Priority #</b></td>
					<td><b># Queries</b></td>
					<td><b>Program</b></td>
				</tr>
				<tr>
					<td>1</td>
					<td><10000</td>
					<td>BLASTN</td>
				</tr>
				<tr>
                                        <td>1</td>
                                        <td><4000</td>
                                        <td>BLASTX,BLASTP,TBLASTN,TBLASTX</td>
                                </tr>	
				<tr>
                                        <td>2</td>
                                        <td><1000</td>
                                        <td>BLASTN</td>
                                </tr>
                                <tr>
                                        <td>2</td>
                                        <td><400</td>
                                        <td>BLASTX,BLASTP,TBLASTN,TBLASTX</td>
                                </tr>
				</table><br><br>";
		}
        }

        if($_GET['action']=='decprior')
        {
                $jobToDec = new Job($sqlDataBase,$config_array);
                $jobToDec->LoadJob($_GET['job']);
                if(($jobToDec->GetPriority()-1)>=0)
                {
                        $jobToDec->SetPriority($jobToDec->GetPriority()-1);
                }
        }

	if($_GET['action']=='delete')
	{
		$jobToDelete= new Job($sqlDataBase,$config_array);
		$jobToDelete->LoadJob($_GET['job']);
		$jobToDelete->DeleteJob();
	}
	
	if($_GET['action']=='reset')
	{
		$jobToReset= new Job($sqlDataBase,$config_array);
                $jobToReset->LoadJob($_GET['job']);
                $jobToReset->ResetJob();
	}
	
	if($_GET['action']=='cancel')
	{
		$jobToCancel= new Job($sqlDataBase,$config_array);
                $jobToCancel->LoadJob($_GET['job']);
                $jobToCancel->CancelJob();
	}
	
	if($_GET['action']=='concat')
	{
		$httphost  = $_SERVER['HTTP_HOST'];
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		$extra = "download.php?job=".$_GET['job']."&filetype=result";
		header("Location: http://$httphost$uri/$extra");
	}
	
	if($_GET['action']=='csv')
	{
                $httphost  = $_SERVER['HTTP_HOST'];
                $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
                $extra = "download.php?job=".$_GET['job']."&filetype=csv";
                header("Location: http://$httphost$uri/$extra");

	}

	if($_GET['action']=='transfer')
	{
		$jobTransfer = new Job($sqlDataBase,$config_array);
		$jobTransfer->LoadJob($_GET['job']);
		$jobTransfer->TransferToDropBox();
	}
	
	if(isset($_POST['scpSend']))
        {
                $scpHost = escapeshellcmd($_POST['scpHost']);
                $scpUser = escapeshellcmd($_POST['scpUser']);
                $scpPass = escapeshellcmd($_POST['scpPass']);
                $scpPath = escapeshellcmd($_POST['scpPath']);

                $job = new Job($sqlDataBase,$config_array);
                $job->LoadJob($_POST['jobToSCP']);
                $_GET['job']=$_POST['jobToSCP'];
		$job->SCPJob($scpHost,$scpPath,$scpUser,$scpPass);
        }
	else
        {
		//Set Default SCP variables
		$scpDefaultHost = "file-server.igb.illinois.edu";
		$firstLetter = substr($_SESSION['username'],0,1);
		if(in_array($firstLetter,range('a','m')))
		{
        		$scpPathRange =  "a-m";
		}
		elseif(in_array($firstLetter,range('n','z')))
		{
		        $scpPathRange = "n-z";
		}


                $scpHost = "file-server.igb.uiuc.edu";
                $scpUser = $_SESSION['username'];
                $scpPass = $_SESSION['password'];
                $scpPath = "/file-server/home/".$scpPathRange."/".$_SESSION['username']."/";
        }

        if($_GET['action']=='transfercostum' || isset($_POST['scpSend']))
        {
                echo "<form action=\"index.php?view=jobs&job=".$_GET['job']."&action=transfercustom\" method=\"POST\">";
                echo "<table>";
		echo "<th colspan=\"2\">Secure File Copy (SCP)</th>";
                echo "<tr><td>Job #: </td><td><select name=\"jobToSCP\">";
                foreach($userJobsArray as $id=>$assoc)
                {
                        echo "<option value=".$assoc['id'];
                        if($assoc['id']==$_GET['job'])
                        {
                                echo " SELECTED";
                        }
                        echo ">".$assoc['id']."</option>";
                }
                echo "<tr><td>Host: </td><td> <input type=\"text\" name=\"scpHost\" value=\"".$scpHost."\"></td></tr>";
                echo "<tr><td>Path: </td><td><input type=\"text\" name=\"scpPath\" value=\"".$scpPath."\"></td></tr>";
                echo "<tr><td>Username:</td><td> <input type=\"text\" name=\"scpUser\" value=\"".$scpUser."\"></td></tr>";
                echo "<tr><td>Password:</td><td> <input type=\"password\" name=\"scpPass\" value=\"".$scpPass."\"></td></tr>";
                echo "<tr><td></td><td><input type=\"submit\" name=\"scpSend\" value=\"Transfer\"></td></tr>";
                echo "</table>";

                echo "</form>";
        }
	$queryJobsArray= "SELECT j.name,j.submitDate,j.completeDate,j.queriesadded,j.queriescompleted,j.id,j.status, s.name AS statusname,j.priority FROM blast_jobs j, users u, status s WHERE u.netid=\"".$_SESSION['username']."\" AND j.userid=u.id AND s.id=j.status AND j.status!=".$statusDeleting." ORDER BY id DESC";
	$userJobsArray = $sqlDataBase->query($queryJobsArray);
	
   }	
}
?>
<a href="index.php?view=jobs">Click To Refresh Jobs</a><br><br>
Use the +/- sign to increase/decrease a job's priority.
<TABLE CELLPADDING="5" border=1>
<tr>
<th>
+/-
</th>
<th>
<b>Priority</b>
</th>
<th>
<b>Job ID</b>
</th>
<th>
<b>Job Name</b>
</th>
<th>
<b>Submit Date</b>
</th>
<th>
<b>Complete Date</b>
</th>
<th>
<b>Progress</b>
</th>
<th>
<b>Status</b>
</th>
<th>
<b>Options</b>
</tj>
<th>
<b>Results</b>
</th>
</tr>
<?php
if($userJobsArray)
{
	foreach($userJobsArray as $id=>$assoc)
	{
		echo "<tr>";
		echo "<td><center><a href=\"index.php?view=jobs&job=".$assoc['id']."&action=incprior\"><img src=\"images/plus-icon.png\"></a><a href=\"index.php?view=jobs&job=".$assoc['id']."&action=decprior\"><image src=\"images/minus-icon.png\"></a> </center></td>";
                echo "<td><center><b>".$assoc['priority']."</b></center>";
		echo "<td>".$assoc['id']."</td>";
		echo "<td>".$assoc['name']."</td>";
		echo "<td>".$assoc['submitDate']."</td>";
		echo "<td>".$assoc['completeDate']."</td>";
		//echo "<td><a href=\"index.php?view=queries&job=".$assoc['id']."&action=showqueries\">".$assoc['queriescompleted']." / ".$assoc['queriesadded']."</a></td>";
		echo "<td><a href=\"index.php?view=queries&job=".$assoc['id']."&action=showqueries\"><div class=\"progress-container\"><div style=\"width: ".(($assoc['queriescompleted'] / $assoc['queriesadded'])*100)."%\"> ".$assoc['queriescompleted']."/".$assoc['queriesadded']." </div></div></a></td>";
		echo "<td>".$assoc['statusname']."</td>";
		echo "<td>";
		echo "<a href=\"index.php?view=jobs&job=".$assoc['id']."&action=delete\">Delete</a>";
		//echo " | <a href=\"index.php?view=jobs&job=".$assoc['id']."&action=reset\">Retry</a>";
		echo " | <a href=\"index.php?view=jobs&job=".$assoc['id']."&action=cancel\">Cancel</a>";
		echo "</td><td>";
		if($assoc['status'] == $statusCompleted)
		{
			echo "<a href=\"index.php?view=jobs&job=".$assoc['id']."&action=concat\">Download</a>";	
			//echo " | <a href=\"index.php?view=jobs&job=".$assoc['id']."&action=transfer\">To Drop Box</a>";
			echo " | <a href=\"index.php?view=jobs&job=".$assoc['id']."&action=transfercostum\">Transfer</a>";
			echo " | <a href=\"index.php?view=jobs&job=".$assoc['id']."&action=csv\">CSV</a>";
			echo " | <a href=\"index.php?view=csvheader&job=".$assoc['id']."\">CSV Titles</a>";
		}
		echo "</td></tr>";
	
	}
}
?>
</TABLE>
