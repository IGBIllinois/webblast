<?php


error_reporting(E_ALL);
ini_set("display_errors", 1);
//$target_path = "uploads/".$_SESSION['username']."_".Date("YmdHis").".fasta";


$userid=$_SESSION['userid'];
$queryChunkSize="SELECT chunksize FROM config ORDER BY id DESC";
if(isset($_POST['chunkSize']))
{
	$chunkSize = $_POST['chunkSize'];
}
else
{
	$chunkSize=$sqlDataBase->singleQuery($queryChunkSize);
}

$errorsFound=0;

if(isset($_POST['saveProfile']))
{
	$profile = new Profile($sqlDataBase);

	$paramsEnabled = @implode(' ', $_POST['params']);

	$inputIU = "F";
	$inputg = "F";
	$inputJU = "F";
	$inputTU = "F";
	$inputUU = "F";
	$inputn = "F";

	if(isset($_POST['inputIU']))
	{
		$inputIU = "T";
	}
	else
	{
		$inputIU = "F";
	}

	if(isset($_POST['inputg']))
	{
		$inputg = "T";
	}
	else
	{
		$inputg= "F";
	}

	if(isset($_POST['inputJU']))
	{
		$inputJU = "T";
	}
	else
	{
		$inputJU = "F";
	}

	if(isset($_POST['inputTU']))
	{
		$inputTU = "T";
	}
	else
	{
		$inputTU="F";
	}

	if(isset($_POST['inputUU']))
	{
		$inputUU = "T";
	}
	else
	{
		$inputUU= "F";
	}

	if(isset($_POST['inputn']))
	{
		$inputn = "T";
	}
	else
	{
		$inputn = "F";
	}
	$profile->CreateProfile($_POST['saveProfileName'],$userid,addslashes($_POST['inputp']),addslashes($_POST['inputd']),addslashes($_POST['inpute']),addslashes($_POST['inputm']),addslashes($_POST['inputFU']),addslashes($_POST['inputGU']),addslashes($_POST['inputEU']),addslashes($_POST['inputXU']),addslashes($inputIU),addslashes($_POST['inputq']),addslashes($_POST['inputr']),addslashes($_POST['inputv']),addslashes($_POST['inputb']),addslashes($_POST['inputf']),addslashes($inputg),addslashes($_POST['inputQU']),addslashes($_POST['inputDU']),addslashes($_POST['inputa']),addslashes($inputJU),addslashes($_POST['inputMU']),addslashes($_POST['inputWU']),addslashes($_POST['inputz']),addslashes($_POST['inputKU']),addslashes($_POST['inputYU']),addslashes($_POST['inputSU']),addslashes($inputTU),addslashes($_POST['inputl']),addslashes($inputUU),addslashes($_POST['inputy']),addslashes($_POST['inputZU']),addslashes($_POST['inputRU']),addslashes($inputn),addslashes($_POST['inputLU']),addslashes($_POST['inputAU']),addslashes($_POST['inputw']),addslashes($_POST['inputt']),addslashes($_POST['inputBU']),addslashes($_POST['inputCU']),$paramsEnabled,$chunkSize);

}
//fasta query submitted start parsing to mysql
if(isset($_POST['submitfasta']))
{
	$job = new Job($sqlDataBase,$config_array);
	echo $_FILES['inputi']['tmp_name'];
	if($job->GetQueriesFromURL($_POST['inputiURL']) || $job->GetQueriesFromFile($_FILES['inputi']['tmp_name']))
	{
		if($_POST['inputp']>0)
		{
			$paramsEnabled = @implode(' ', $_POST['params']);

			$inputIU = "F";
			$inputg = "F";
			$inputJU = "F";
			$inputTU = "F";
			$inputUU = "F";
			$inputn = "F";

			if(isset($_POST['inputIU']))
			{
				$inputIU = "T";
			}
			else
			{
				$inputIU = "F";
			}
			if(isset($_POST['inputg']))
			{
				$inputg = "T";
			}
			else
			{
				$inputg= "F";
			}

			if(isset($_POST['inputJU']))
			{
				$inputJU = "T";
			}
			else
			{
				$inputJU = "F";
			}

			if(isset($_POST['inputTU']))
			{
				$inputTU = "T";
			}
			else
			{
				$inputTU="F";
			}
			if(isset($_POST['inputUU']))
			{
				$inputUU = "T";
			}
			else
			{
				$inputUU= "F";
			}

			if(isset($_POST['inputn']))
			{
				$inputn = "T";
			}
			else
			{
				$inputn = "F";
			}
			$job->CreateJob($_POST['jobname'],$userid,addslashes($_POST['inputp']),addslashes($_POST['inputd']),addslashes($_POST['inpute']),addslashes($_POST['inputm']),addslashes($_POST['inputFU']),addslashes($_POST['inputGU']),addslashes($_POST['inputEU']),addslashes($_POST['inputXU']),addslashes($inputIU),addslashes($_POST['inputq']),addslashes($_POST['inputr']),addslashes($_POST['inputv']),addslashes($_POST['inputb']),addslashes($_POST['inputf']),addslashes($inputg),addslashes($_POST['inputQU']),addslashes($_POST['inputDU']),addslashes($_POST['inputa']),addslashes($inputJU),addslashes($_POST['inputMU']),addslashes($_POST['inputWU']),addslashes($_POST['inputz']),addslashes($_POST['inputKU']),addslashes($_POST['inputYU']),addslashes($_POST['inputSU']),addslashes($inputTU),addslashes($_POST['inputl']),addslashes($inputUU),addslashes($_POST['inputy']),addslashes($_POST['inputZU']),addslashes($_POST['inputRU']),addslashes($inputn),addslashes($_POST['inputLU']),addslashes($_POST['inputAU']),addslashes($_POST['inputw']),addslashes($_POST['inputt']),addslashes($_POST['inputBU']),addslashes($_POST['inputCU']),$paramsEnabled,$chunkSize);

				$ps = $job->AddQueries();
				echo "<br><br><table border=1><tr><td><center><FONT COLOR=\"green\">Fasta Queries Submitted.<br> Please Check Submitted Jobs page to verify.<br>You will receive an e-mail when the job is complete.<br><br>>>DO NOT HIT REFRESH OR YOUR JOB WILL RESUBMIT<<</FONT></center></td></tr></table><br><br>";
			}
			else
			{
				echo "<br><FONT COLOR=\"red\">Please choose a program</FONT>";
			}
		}
		else
		{
			echo "Error: ".$_FILES['inputi']['error']."</br>";
			echo "<br><FONT COLOR=\"red\">File Upload Failed ".$_FILES['inputi']['tmp_name']." ".$_POST['inputiURL']."</FONT>";
			print_r($_FILES['inputi']);
		}

}
?>
