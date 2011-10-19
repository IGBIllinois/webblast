<?php

if(isset($_POST['submitConfig']))
{
	$querySubmitPaths="INSERT INTO config (resultspath,queriespath,webqueriespath,webresultspath,webdeletepath,csvresultspath,webcsvresultspath,resultsdestinationpath,fromemail,transfercompletemsg,jobcompletemsg,transfercompletesubj,jobcompletesubj,url,chunksize,perlfilespath,maxjobage,dbvolumesize,taxonomypath,userdbspath) VALUES (\"".$_POST['resultsPath']."\",\"".$_POST['queriesPath']."\",\"".$_POST['webQueriesPath']."\",\"".$_POST['webResultsPath']."\",\"".$_POST['webDeletePath']."\",\"".$_POST['csvResultsPath']."\",\"".$_POST['webCsvResultsPath']."\",\"".$_POST['resultsDestinationPath']."\",\"".$_POST['fromMail']."\",\"".$_POST['transferCompleteMsg']."\",\"".$_POST['jobCompleteMsg']."\",\"".$_POST['transferCompleteSubj']."\",\"".$_POST['jobCompleteSubj']."\",\"".$_POST['url']."\",\"".$_POST['chunkSize']."\",\"".$_POST['perlFilesPath']."\",\"".$_POST['maxJobAge']."\",\"".$_POST['dbVolumeSize']."\",\"".$_POST['taxonomyPath']."\",\"".$_POST['userDbsPath']."\")";
	$sqlDataBase->nonSelectQuery($querySubmitPaths);
}
if(isset($_POST['submitUndoConfig']))
{
	$queryDeleteConfig="DELETE FROM config ORDER BY id DESC LIMIT 1";
	$sqlDataBase->nonSelectQuery($queryDeleteConfig);
}

$queryConfigInfo="SELECT resultspath,queriespath,webqueriespath,webresultspath,webdeletepath,csvresultspath,webcsvresultspath,resultsdestinationpath,fromemail,transfercompletemsg,jobcompletemsg,transfercompletesubj,jobcompletesubj,url,chunksize,perlfilespath, maxjobage, dbvolumesize, taxonomypath, userdbspath FROM config ORDER BY ID DESC LIMIT 1";
$configInfoArray=$sqlDataBase->query($queryConfigInfo);

?>
<b>Configure Data Paths:</b><br><br>

<FORM ACTION="admin.php?view=paths" METHOD="POST">
<TABLE BORDER=1>
<tr>
<th colspan=2>
<b>Slave Node Paths</b>
</th>
</tr>
<tr>
<td>
Chunk Results Path:
</td>
<td>
<INPUT TYPE="TEXT" NAME="resultsPath" VALUE="<?php echo $configInfoArray[0]['resultspath']; ?>" SIZE=45><br>
</td>
</tr>
<td>
Queries Path:
</td>
<td>
<INPUT TYPE="TEXT" NAME="queriesPath" VALUE="<?php echo $configInfoArray[0]['queriespath']; ?>" SIZE=45><br>
</td>
</tr>
<tr>
<td>
CSV Chunk Results: 
</td>
<td>
<INPUT TYPE="TEXT" NAME="csvResultsPath" VALUE="<?php echo $configInfoArray[0]['csvresultspath']; ?>" SIZE=45><br>
</td>
<tr>
<th colspan=2>
<b>Master Node Paths</b>
</th>
</tr>
<tr>
<td>
Perl Scripts Path:<br>(concat.pl/masterblast.pl/<br>slaveloop.pl):
</td>
<td>
<INPUT TYPE="TEXT" NAME="perlFilesPath"  VALUE="<?php echo $configInfoArray[0]['perlfilespath']; ?>" SIZE=45><br>
</td>
</tr>
<tr>
<td>
Queries Path: 
</td>
<td>
<INPUT TYPE="TEXT" NAME="webQueriesPath"  VALUE="<?php echo $configInfoArray[0]['webqueriespath']; ?>" SIZE=45><br>
</td>
</tr>
<tr>
<td>
Chunk Results Path: 
</td>
<td>
<INPUT TYPE="TEXT" NAME="webResultsPath" VALUE="<?php echo $configInfoArray[0]['webresultspath']; ?>" SIZE=45><br>
</td>
</tr>
<tr>
<td>
Delete Path: 
</td>
<td>
<INPUT TYPE="TEXT" NAME="webDeletePath" VALUE="<?php echo $configInfoArray[0]['webdeletepath']; ?>" SIZE=45><br>
</td>
</tr>
<tr>
<td>
CSV Chunk Results Path: 
</td>
<td>
<INPUT TYPE="TEXT" NAME="webCsvResultsPath" VALUE="<?php echo $configInfoArray[0]['webcsvresultspath']; ?>" SIZE=45><br>
</td>
</tr>
<tr>
<td>
Finalized Results Path:
</td>
<td>
<INPUT TYPE="TEXT" NAME="resultsDestinationPath" VALUE="<?php echo $configInfoArray[0]['resultsdestinationpath']; ?>"  SIZE=45><br>
</td>
</tr>
<tr>
<th colspan=2>
<b>User Alert Messages</b>
</th>
</tr>
<tr>
<td>
From E-Mail: 
</td>
<td>
<INPUT TYPE="TEXT" NAME="fromMail"  VALUE="<?php echo $configInfoArray[0]['fromemail']; ?>" SIZE=45><br>
</td>
</tr>
<tr>
<td>
Job Complete Subject: 
</td>
<td>
<INPUT TYPE="TEXT" NAME="jobCompleteSubj"  VALUE="<?php echo $configInfoArray[0]['jobcompletesubj']; ?>" SIZE=45><br>
</td>
</tr>
<tr>
<td>
Job Complete Message: 
</td>
<td>
<TEXTAREA NAME="jobCompleteMsg" cols="52" rows="10"><?php echo $configInfoArray[0]['jobcompletemsg']; ?></TEXTAREA><br>
</td>
</tr>
<tr>
<td>
Transfer Complete Subject:
</td>
<td>
<INPUT TYPE="TEXT" NAME="transferCompleteSubj" VALUE="<?php echo $configInfoArray[0]['transfercompletesubj']; ?>" SIZE=45><br>
</td>
</tr>
<tr>
<td>
Transfer Complete Message: 
</td>
<td>
<TEXTAREA NAME="transferCompleteMsg" cols="52" rows="10"><?php echo $configInfoArray[0]['transfercompletemsg']; ?></TEXTAREA><br>
</td>
</tr>
<tr>
<th colspan=2>
<b>Website Info</b>
</th>
</tr>
<tr>
<td>
URL Path: 
<td>
<INPUT TYPE="TEXT" NAME="url" VALUE="<?php echo $configInfoArray[0]['url']; ?>" SIZE=45><br>
</td>
</tr>
<tr>
<th colspan=2>
<b>Job Submission Settings</b>
</th>
</tr>
<tr>
<td>
Chunk Size:
</td>
<td>
<INPUT TYPE="TEXT" NAME="chunkSize" VALUE="<?php echo $configInfoArray[0]['chunksize']; ?>" SIZE=45><br>
</td>
</tr>
<tr>
<th colspan=2>
<b>Job Deletion</b>
</th>
</tr>
<tr>
<td>
Job Expiration Days:
</td>
<td>
<INPUT TYPE="TEXT" NAME="maxJobAge" VALUE="<?php echo $configInfoArray[0]['maxjobage']; ?>" SIZE=45><br>
</td>
</tr>
<tr>
<th colspan=2>
<b>Databases Settings:</b>
</th>
</tr>
<tr>
<td>
User Databases Path
</td>
<td>
<INPUT TYPE="TEXT" NAME="userDbsPath" VALUE="<?php echo $configInfoArray[0]['userdbspath']; ?>" SIZE=45><br>
</td>
</tr>
<tr>
<td>
Formatdb Volume Size
</td>
<td>
<INPUT TYPE="TEXT" NAME="dbVolumeSize" VALUE="<?php echo $configInfoArray[0]['dbvolumesize']; ?>" SIZE=45><br>
</td>
</tr>
<tr>
<td>
Taxonomy Path
</td>
<td>
<INPUT TYPE="TEXT" NAME="taxonomyPath" VALUE="<?php echo $configInfoArray[0]['taxonomypath']; ?>" SIZE=45><br>
</td>
</tr>
<tr>
<td>
</td>
<td>
<INPUT TYPE="submit" value="Update" name="submitConfig"><INPUT TYPE="submit" value="Restore Previous Config" name="submitUndoConfig"><br>
</td>
</tr>
</table>
</FORM>

<b>E-mail text syntax:</b><br><br>
<b>[csvurl]</b> - Will display a link to downlad the csv file from the web for the job<br>
<b>[resulturl]</b> - Will display a link to download the raw file from the web for the job<br>
<b>[job]</b> - Will display the job number<br>
<b>[url]</b> - Will display the URL path set in Website Info<br>


