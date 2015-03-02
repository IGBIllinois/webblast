<?php

if(isset($_POST['submitDB']))
{
	$userDatabase = new Database($sqlDataBase,$config_array);
	$description = mysql_real_escape_string($_POST['t']);
	$status = 1;
	$type = mysql_real_escape_string($_POST['dbType']);
	$userid = $authen->GetAuthUser()->getUserId();
	$userDatabase->CreateDatabase($description,$status,$type,$userid);
	if($userDatabase->GetDatabaseFromFile($_FILES['inputi']['tmp_name']) || $userDatabase->GetDatabaseFromUrl($_POST['inputiURL']))
	{
		$dbSeqId = "F";
		$dbASNFormat = "F";
		$dbASNMode = $_POST['b'];
		$dbInSeqEntry = "F";
		$dbCreateIndexes = "F";
		$dbTaxonomicInfo = $_POST['T'];

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
		$userDatabase->CompileDatabase($dbSeqId,$dbASNFormat,$dbASNMode,$dbInSeqEntry,$dbCreateIndexes,$dbTaxonomicInfo);
		echo "<br>";
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
<FORM ACTION="index.php?view=createdatabase" NAME="DataBaseSubmissionForm" METHOD="POST" enctype="multipart/form-data">
<table border=1>
<tr><th colspan=3><b>Formatdb Database</b></th></tr>
<tr><td>Database Name: </td><td><input type="text" name="t"></td><td>Title for database file.</td></tr>
<tr><td>Database Type:</td><td> <SELECT name="dbType">
<option value="T">Protein</option>
<option value="F">Nucleotide</option>
</SELECT></td><td></td></tr>
<tr><td>Parse deflines and indexes seqIDs:</td><td><input type="checkbox" name="o" value="T"></td><td>If Parse SeqId and create indexes is checked (and the input database is in FASTA format), then the database identifiers in the FASTA definition line must follow the convention described in the appendices of <a href="ftp:/ncbi.nlm.nih.gov/blast/dB/README">ftp:/ncbi.nlm.nih.gov/blast/dB/README.</a></td></tr>
<tr>
	<td>
	<b>Database File:</b>

	</td>
	<td>
	<input type="hidden" name="MAX_FILE_SIZE" value="2147483647" /><INPUT TYPE="file" NAME="inputi">
	</td>
	<td>
	<b>File must be less than 2GB in size</b>, if you would like to upload a larger file please upload it to your file server user directory and E-mail <a href="mailto:help@igb.illinois.edu">help@igb.illinois.edu</a> with special parameter requests.
	</td>
</tr>
<tr>
	<td>
	<b>Database Url:</b>
	</td>
	<td>
	<TEXTAREA name="inputiURL"></TEXTAREA>
	</td>
	<td>
	<b>For files larger than 2GB</b> please use a web link to the file. You can either use http://uofi.box.net or upload your file to file-server.igb.illinois.edu and add the file to your public_html directory. To access the file on the fileserver simply enter http://file-server.igb.illinois.edu/~yourNetId/filename.fasta (where yourNetId is your university netid) for assistance please contact help@igb.illinois.edu.
	</td>
</tr>
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


