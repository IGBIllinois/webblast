<b>User Jobs:</b>
(<a href="admin.php?view=jobs">Click To Refresh</a>)
<br>
<br>
<?php

include "includes/txt2html.php";
$statusDeleting=10;
$statusCompleted=3;
$queryJobsArray= "SELECT j.name,j.submitDate,j.completeDate,j.queriesadded,j.queriescompleted,j.id,j.status, s.name AS statusname, u.netid, j.priority FROM blast_jobs j, users u, status s WHERE j.userid=u.id AND s.id=j.status AND j.status!=".$statusDeleting." ORDER BY priority DESC,id ASC";
$userJobsArray = $sqlDataBase->query($queryJobsArray);


if(isset($_GET['action']))
{
	if($_GET['action']=='incprior')
	{
		$jobToInc = new Job($sqlDataBase,$config_array);
		$jobToInc->LoadJob($_GET['job']);
		$jobToInc->SetPriority($jobToInc->GetPriority()+1);	
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

	if($_GET['action']=='resume')
        {
                $jobToResume= new Job($sqlDataBase,$config_array);
                $jobToResume->LoadJob($_GET['job']);
                $jobToResume->ResumeJob();
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
		
		$scpHost = "";
                $scpUser = "";
                $scpPass = "";
                $scpPath = "";
	}

	if($_GET['action']=='transfercostum' || isset($_POST['scpSend']))
        {
                echo "<form action=\"admin.php?view=jobs&job=".$_GET['job']."&action=transfercustom\" method=\"POST\">";
		echo "<table>";
		echo "<tr><th colspan=2><b>Secure File Copy Form (SCP)</b></th></tr>";
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
	
	$queryJobsArray= "SELECT j.name,j.submitDate,j.completeDate,j.queriesadded,j.queriescompleted,j.id,j.status, s.name AS statusname, u.netid, j.priority FROM blast_jobs j, users u, status s WHERE j.userid=u.id AND s.id=j.status AND j.status!=".$statusDeleting." ORDER BY priority DESC,id ASC";
	$userJobsArray = $sqlDataBase->query($queryJobsArray);

}
?>
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
<b>User</b>
</th>
<th>
<b>Job Name</b>
</th>
<th>
<b>Submit Date</b>
</th>
<th>
<b>Completed Date</b>
</th>
<th>
<b>Progress</b>
</th>
<th>
<b>Status</b>
</th>
<th>
<b>Options</b>
</th>
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
		echo "<td><center><a href=\"admin.php?view=jobs&job=".$assoc['id']."&action=incprior\"><img src=\"images/plus-icon.png\"></a><a href=\"admin.php?view=jobs&job=".$assoc['id']."&action=decprior\"><image src=\"images/minus-icon.png\"></a> </center></td>";
		echo "<td><center><b>".$assoc['priority']."</b></center>";
		echo "<td>".$assoc['id']."</td>";
		echo "<td>".$assoc['netid']."</td>";
		echo "<td>".$assoc['name']."</td>";
		echo "<td>".$assoc['submitDate']."</td>";
		echo "<td>".$assoc['completeDate']."</td>";
		//echo "<td><a href=\"admin.php?view=queries&job=".$assoc['id']."&action=showqueries\">".$assoc['queriescompleted']." / ".$assoc['queriesadded']."</a></td>";
		echo "<td><a class=\"progress_bar\" href=\"admin.php?view=queries&job=".$assoc['id']."&action=showqueries\"><div class=\"progress-container\"><div style=\"width: ".(($assoc['queriescompleted'] / $assoc['queriesadded'])*100)."%\"> ".$assoc['queriescompleted']."/".$assoc['queriesadded']." </div></div></a></td>";
		echo "<td>".$assoc['statusname']."</td>";
		echo "<td>";
		echo "<a href=\"admin.php?view=jobs&job=".$assoc['id']."&action=delete\">Delete</a>";
		echo " | <a href=\"admin.php?view=jobs&job=".$assoc['id']."&action=reset\">Retry</a>";
		echo " | <a href=\"admin.php?view=jobs&job=".$assoc['id']."&action=cancel\">Cancel</a>";
		echo " | <a href=\"admin.php?view=jobs&job=".$assoc['id']."&action=resume\">Resume</a>";
		echo "</td><td>";
		if($assoc['status'] == $statusCompleted)
		{
			echo "<a href=\"admin.php?view=jobs&job=".$assoc['id']."&action=concat\">Download Raw</a>";	
			echo " | <a href=\"admin.php?view=jobs&job=".$assoc['id']."&action=csv\">CSV</a>";
			//echo " | <a href=\"admin.php?view=jobs&job=".$assoc['id']."&action=transfer\">To Dropbox</a>";
			echo " | <a href=\"admin.php?view=jobs&job=".$assoc['id']."&action=transfercostum\">Send To</a>";
		}
		echo "</td></tr>";
	
	}
}
?>
</TABLE>
