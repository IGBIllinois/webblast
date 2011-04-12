<?php

$fileName = $_SESSION['usernameAdmin']."_".Date("YmdHis").".fas";
$queryUserDbsPath = "SELECT userdbspath FROM config ORDER BY id DESC LIMIT 1";
$userDbsPath = $sqlDataBase->singleQuery($queryUserDbsPath);

$target_path = $userDbsPath.$fileName;
if(isset($_POST['submitDB']))
{
	if(move_uploaded_file($_FILES['inputi']['tmp_name'], $target_path) || 1)
        {
		$dbName = $fileName;
		$dbTitle = addslashes($_POST['t']);
		$dbType = $_POST['dbType'];
		$dbFile = $target_path;
		$dbSeqId = "F";
		$dbASNFormat = "F";
		$dbASNMode = $_POST['b'];
		$dbInSeqEntry = "F";
		$dbCreateIndexes = "F";
		$dbTaxonomicInfo = $_POST['T'];
		$userId = $_POST['user'];

		if($_POST['t']=="")
		{
			$dbTitle = $dbName;
		}
		if(isset($_POST['o']))
		{
			$dbSeqId = "T";
		}

		if(isset($_POST['a']))
		{
			$dbASNFormat = "T";
		}

		if(isset($_POST['b']))
		{
			$dbASNMode = "T";
		}	

		if(isset($_POST['s']))
		{
			$dbCreateIndexes = "T";
		}
		echo "<br>";
		Exec::run_in_background("scripts/addDatabase.pl ".$dbName." \"".$dbTitle."\" ".$dbType." ".$dbFile." ".$dbSeqId." ".$dbASNFormat." ".$dbASNMode." ".$dbInSeqEntry." ".$dbCreateIndexes." ".$dbTaxonomicInfo." ".$sqlDataBase->GetSqlUsername()." ".$sqlDataBase->GetSqlPassword()." ".$sqlDataBase->GetDatabase()." ".$userId > addDatabase.log);	
		echo "<br><table border=1><tr><td><center><FONT COLOR=\"green\">Database submitted!<br>You will recieve an e-mail when the database is ready for use on the cluster or incase of a failure.<br><br>>>DO NOT HIT REFRESH OR YOUR DATABASE WILL RESUBMIT<<</FONT></center></td></tr></table>";

        }
	else
	{
		echo "<br><FONT COLOR=\"red\">File Upload Failed</FONT>";
	}
	
}
?>
<b>Database Upload:</b>

<br><br>
<FORM ACTION="admin.php?view=uploaddatabase" NAME="DataBaseSubmissionForm" METHOD="POST" enctype="multipart/form-data">
<table border=1>
<tr><th colspan=3><b>Formatdb Database</b></th></tr>
<tr><td>User Name:</td><td>
<SELECT name="user">
<?php
$queryUsers = "SELECT netid, id FROM users ORDER BY netid";
$users = $sqlDataBase->query($queryUsers);
foreach($users as $id=>$userInfo)
{
	echo "<option value=".$userInfo['id'].">".$userInfo['netid']."</option>";
}


?>
</SELECT>
</td><td></td></tr>
<tr><td>Database Name: </td><td><input type="text" name="t"></td><td>Title for database file.</td></tr>
<tr><td>Database Type:</td><td> <SELECT name="dbType">
<option value="T">Protein</option>
<option value="F">Nucleotide</option>
</SELECT></td><td></td></tr>
<tr><td>Parse deflines and indexes seqIDs:</td><td><input type="checkbox" name="o" value="T"></td><td>If Parse SeqId and create indexes is checked (and the input database is in FASTA format), then the database identifiers in the FASTA definition line must follow the convention described in the appendices of <a href="ftp:/ncbi.nlm.nih.gov/blast/dB/README">ftp:/ncbi.nlm.nih.gov/blast/dB/README.</a></td></tr>
<tr><td>Database File:</td><td><INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="10000000000">
<INPUT TYPE="file" NAME="inputi">
</td></tr>
<tr><td>ASN.1 Format:</td><td><input type="checkbox" name="a" value="T"></td><td>Input file is database in ASN.1 format (otherwise FASTA is expected)</td></tr>
<tr><td>ASN.1 Mode:</td><td><SELECT name="b"><option value="T">Binary</option><option value="F" SELECTED>Text</option></select></td><td>An input ASN.1 database may be represented in two formats - ascii text and binary. The option is ignored in case of FASTA input database.</td></tr>
<tr><td>Input is Seq-entry:</td><td><input type="checkbox" name="e" value="T"></td><td> An input ASN.1 database (either text ASCII or binary) may contains Bioseq-set or just one Bioseq. In the latter case the "-e" switch should be set to TRUE.</td></tr>
<tr><td>Create indexes limited only to accessions</td><td><input type="checkbox" name="s" value="T"></td><td>This option limits the indices for the string identifiers (used by formatdb) to accessions (i.e., no locus names). This is especially useful for sequences sets like the EST's where the accession and locus names are identical. Formatdb runs faster and produces smaller temporary files if this option is used. It is strongly recommended for EST's, STS's, GSS's, and HTGS's.</td></tr>


<tr><td>Read taxonomic information and write group bit to the ASN.1 defline</td><td><SELECT name="T">
<option value="0" SELECTED>None</option>
<?php
$queryTaxonomyDmp = "SELECT file,description FROM taxonomy";
$taxonomyDmp = $sqlDataBase->query($queryTaxonomyDmp);
foreach($taxonomyDmp as $id =>$dmpFileInfo)
{
	echo "<option value=\"".$dmpFileInfo['file']."\">".$dmpFileInfo['description']."</option>";
}
?>
</select></td><td>This parameter allows formatdb to read in a file with gi/taxid information and write the taxid information to the ASN.1 defline.</td></tr>
<tr><td></td><td><input type="submit" name="submitDB" value="Submit Database"></td><td></td></tr>

</table>
</FORM>


