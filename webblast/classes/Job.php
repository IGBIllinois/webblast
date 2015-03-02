<?php
class Job
{
	private $jobid;
	private $jobname;
	private $userid;
	private $token;
	private $sqlDataBase;
	private $description;
	private $queryPath;
	private $resultsPath;
	private $finalizePath;
	private $submitpid;
	private $deletePath;
	private $csvPath;
	private $concatPid;
	private $transferPid;
	private $csvConcatPid;
	private $deletePid;
	private $chunkSize;
	private $priority;
	private $queriesAdded;
	private $blastId;
	private $perlPath;
	private $perlModule;
	private $configFilePath;
	private $numchunks;
	private $uploadPath;
	private $wgetURL;	
	private $tempFilePost;
	private $fastaFile;
	
	public function __construct(SQLDataBase $sqlDataBase,$config_array)
	{	
		$this->sqlDataBase=$sqlDataBase;
		$this->GetConfigValues($config_array);
		$jobid="";
		$jobname="";
		$userid="";
		$description="";
		$queryPath="";
		$countQueries=0;
		$this->wgetURL=0;
		$this->tempFilePost=0;
	}

	public function __destruct()
	{
	}

    /**
     * Delete the currently loaded job
     * verify job is not currently running prior to deleting
     * also verify that that job is not being processed at the moment.
     */
	public function DeleteJob()
	{
		if($this->CheckNoRunningQueries())
		{		
			if(Exec::is_running($this->submitpid))
			{
				Exec::kill($this->submitpid);
			}
		
                        if(Exec::is_running($this->transferPid))
                        {
                                Exec::kill($this->transferPid);
                        }

                        if(Exec::is_running($this->csvConcatPid))
                        {
                                Exec::kill($this->csvConcatPid);
                        }

                        if(Exec::is_running($this->concatPid))
                        {
                                Exec::kill($this->concatPid);
                        }

                        if(Exec::is_running($this->submitpid))
                        {
                                Exec::kill($this->submitpid);
                        }		

			if(!Exec::is_running($this->deletePid))
			{
				 $statusDeleting=10;
				 $ps=Exec::run_in_background($this->perlPath." scripts/deleteJob.pl ".$this->jobid." ".$this->configFilePath);
				 $queryUpdateDeletePid="UPDATE blast_jobs SET deletepid=".$ps.", status=".$statusDeleting." WHERE id=".$this->jobid;
				 $this->sqlDataBase->nonSelectQuery($queryUpdateDeletePid);
			}
			$queryUserNetid="SELECT netid FROM users WHERE id=".$this->userid;
                        $userNetid=$this->sqlDataBase->SingleQuery($queryUserNetid);

			$this->delTree($this->queryPath.$this->jobid,0,"query");
			$this->delTree($this->resultsPath.$this->jobid,0,"results");
			$this->delTree($this->csvPath.$this->jobid,0,"csv");
			$this->delFile($this->finalizePath,$userNetid."_".$this->jobid.".result");
			$this->delFile($this->finalizePath,$userNetid."_".$this->jobid.".csv");
		}else{
			echo "<FONT COLOR=\"red\">Can't delete job while queries are running. <br>Please Cancel job first and wait for running queries to finish</FONT>";
		}
	}

    /**
     * Cancel a job, marks all queries as canceled so that worker nodes do not try to run them
     */
	public function CancelJob()
	{
		$statusCanceled = 4;
                $statusRunning=2;
                $statusCompleted=3;
                $statusNew=1;
                $queryCancelAllNonRunnigQueries="UPDATE blast_queries SET statusid=".$statusCanceled." WHERE statusid=".$statusNew." AND jobid=".$this->jobid;
                $this->sqlDataBase->nonSelectQuery($queryCancelAllNonRunnigQueries);
	}

    /**
     * Resume a job, set all queries which are not complete or failed to new
     * this way the worker nodes will start to pick them up
     */
	public function ResumeJob()
	{
		$statusCanceled = 4;
                $statusRunning=2;
                $statusCompleted=3;
                $statusNew=1;
                $queryCancelAllNonRunnigQueries="UPDATE blast_queries SET statusid=".$statusNew." WHERE statusid=".$statusCanceled." AND jobid=".$this->jobid;
                $this->sqlDataBase->nonSelectQuery($queryCancelAllNonRunnigQueries);
	}

    /**Create a new job with all of the blastall parameters
     * I should have created an array instead of having this many inputs to the function argh dumb dumb dumb...
     * @param $newJobName
     * @param $loggedUserid
     * @param $inputp
     * @param $inputd
     * @param $inpute
     * @param $inputm
     * @param $inputFU
     * @param $inputGU
     * @param $inputEU
     * @param $inputXU
     * @param $inputIU
     * @param $inputq
     * @param $inputr
     * @param $inputv
     * @param $inputb
     * @param $inputf
     * @param $inputg
     * @param $inputQU
     * @param $inputDU
     * @param $inputa
     * @param $inputJU
     * @param $inputMU
     * @param $inputWU
     * @param $inputz
     * @param $inputKU
     * @param $inputYU
     * @param $inputSU
     * @param $inputTU
     * @param $inputl
     * @param $inputUU
     * @param $inputy
     * @param $inputZU
     * @param $inputRU
     * @param $inputn
     * @param $inputLU
     * @param $inputAU
     * @param $inputw
     * @param $inputt
     * @param $inputBU
     * @param $inputCU
     * @param $paramsEnabled
     * @param $chunkSize
     */
	public function CreateJob($newJobName,$loggedUserid,$inputp,$inputd,$inpute,$inputm,$inputFU,$inputGU,$inputEU,$inputXU,$inputIU,$inputq,$inputr,$inputv,$inputb,$inputf,$inputg,$inputQU,$inputDU,$inputa,$inputJU,$inputMU,$inputWU,$inputz,$inputKU,$inputYU,$inputSU,$inputTU,$inputl,$inputUU,$inputy,$inputZU,$inputRU,$inputn,$inputLU,$inputAU,$inputw,$inputt,$inputBU,$inputCU,$paramsEnabled,$chunkSize)
	{
		$statusNew=1;
		$randomDownloadToken=md5(uniqid(mt_rand(), true));
		$this->jobname = $newJobName;
		$this->userid = $loggedUserid;
		$this->chunkSize=$chunkSize;
		$this->token=$randomDownloadToken;

		$sql = "INSERT INTO blast_jobs (name,description,submitDate,userid,blastid,dbid,e,m,FU,GU,EU,XU,IU,q,r,v,b,f,g,QU,DU,a,JU,MU,WU,z,KU,YU,SU,TU,l,UU,y,ZU,RU,n,LU,AU,w,t,BU,CU,paramsenabled,status,chunksize,priority,token,numchunks) VALUES (\"".$this->jobname."\",\"".$this->description."\",NOW(),".$this->userid.",".$inputp.",".$inputd.",".$inpute.",".$inputm.",\"".$inputFU."\",".$inputGU.",".$inputEU.",".$inputXU.",\"".$inputIU."\",".$inputq.",".$inputr.",".$inputv.",".$inputb.",".$inputf.",\"".$inputg."\",".$inputQU.",".$inputDU.",".$inputa.",\"".$inputJU."\",\"".$inputMU."\",\"".$inputWU."\",".$inputz.",".$inputKU.",".$inputYU.",".$inputSU.",".$inputTU.",\"".$inputl."\",\"".$inputUU."\",".$inputy.",".$inputZU.",\"".$inputRU."\",\"".$inputn."\",\"".$inputLU."\",".$inputAU.",".$inputw.",".$inputt.",".$inputBU.",\"".$inputCU."\",\"".$paramsEnabled."\",".$statusNew.",".$chunkSize.",0,\"".$randomDownloadToken."\",1)";
		$this->jobid=$this->sqlDataBase->insertQuery($sql);
	
		$oldUmask=umask(0);
		@$this->fastaFile = $this->userid."_".Date("YmdHis").".fasta";
		mkdir($this->queryPath.$this->jobid,0777);
		mkdir($this->resultsPath.$this->jobid,0777);
		mkdir($this->csvPath.$this->jobid,0777);
		umask($oldUmask);	
	}

    /**Set job priority so workers will pick up them up first
     * @param $priority
     */
	public function SetPriority($priority)
	{
		$querySetJobPriority = "UPDATE blast_jobs SET priority=".$priority." WHERE id=".$this->jobid;
		$this->sqlDataBase->nonSelectQuery($querySetJobPriority);
		$querySetQueryPriority = "UPDATE blast_queries SET priority=".$priority." WHERE jobid=".$this->jobid;
                $this->sqlDataBase->nonSelectQuery($querySetQueryPriority);
		$this->priority=$priority;
			
	}

    /**Get the job priority
     * @return mixed
     */
	public function GetPriority()
	{
		return $this->priority;
	}

    /** Reset job by reseting all queries to new forcing worker nodes to rerun all queries for this job
     * @return bool
     */
	public function ResetJob()
	{
                if($this->CheckNoRunningQueries())
                {
			$statusNew=1;
                	$queryResetJobProgress = "UPDATE blast_jobs SET queriescompleted=0, status=".$statusNew." WHERE id=".$this->jobid;
                        $queryResetQueriesStatus = "UPDATE blast_queries SET reservenode=0, reservepid=0, statusid=1, starttime=NOW(), endtime=NOW() WHERE jobid=".$this->jobid;
			$queryUserNetid="SELECT netid FROM users WHERE id=".$this->userid;
			$userNetid=$this->sqlDataBase->SingleQuery($queryUserNetid);

			$this->delTree($this->resultsPath.$this->jobid,1,"results");
			$this->delTree($this->csvPath.$this->jobid,1,"csv");
			$this->delFile($this->finalizePath,$userNetid."_".$this->jobid.".result");
			$this->delFile($this->finalizePath,$userNetid."_".$this->jobid.".csv");
	
                        $this->sqlDataBase->nonSelectQuery($queryResetJobProgress);
                        $this->sqlDataBase->nonSelectQuery($queryResetQueriesStatus);
			$oldUmask=umask(0);
			@mkdir($this->resultsPath.$this->jobid,0770);
			@mkdir($this->csvPath.$this->jobid,0770);
			umask($oldUmask);
			
			return true;
                }
                else {
                        print "<FONT COLOR=\"red\">Can't delete job while queries are running. <br>Please cancel first and wait for running queries to finish</FONT>";
                }
	}

    /**Reset a single query chunk in order to froce nodes to rerun it.
     * Useful if a node crashes and the query is marked as failed.
     * @param $queryid
     */
	public function ResetQuery($queryid)
	{
		$statusNew=1;
		$statusRunning=2;
		$statusCompleted=3;
                $queryRunningQueries="UPDATE blast_queries SET statusid=".$statusNew.", reservenode=0, reservepid=0 WHERE jobid=".$this->jobid." AND id=".$queryid;
		$queryUpdateQueriesCompleted="UPDATE blast_jobs SET queriescompleted=queriescompleted-1 WHERE id=".$this->jobid;
		$this->sqlDataBase->nonSelectQuery($queryUpdateQueriesCompleted);
		$this->sqlDataBase->nonSelectQuery($queryRunningQueries);
		$this->delFile($this->resultsPath.$this->jobid."/",$queryid.".result");
		$this->delFile($this->csvPath.$this->jobid."/",$queryid.".csv");

	}

    /**Load job details from database into this object
     * @param $id
     */
	public function LoadJob($id)
	{
		$sql = "SELECT * FROM blast_jobs WHERE id=".$id;
		$jobInfoArray = $this->sqlDataBase->query($sql);
		$this->jobid = $jobInfoArray[0]["id"];
		$this->jobname = $jobInfoArray[0]["name"];
		$this->userid = $jobInfoArray[0]["userid"];
		$this->submitpid= $jobInfoArray[0]["submitpid"];	
		$this->concatPid = $jobInfoArray[0]["concatpidresult"];
		$this->transferPid = $jobInfoArray[0]["transferpid"];
		$this->deletePid = $jobInfoArray[0]["deletepid"];
		$this->csvConcatPid=$jobInfoArray[0]["concatpidcsv"];
		$this->chunkSize=$jobInfoArray[0]["chunksize"];
		$this->priority=$jobInfoArray[0]["priority"];
		$this->queriesAdded=$jobInfoArray[0]["queriesadded"];
		$this->blastId=$jobInfoArray[0]["blastid"];
		$this->token=$jobInfoArray[0]["token"];
		$this->numchunks=$jobInfoArray[0]["numchunks"];
	}

    /**
     * Transfer results to user's dropbox
     */
	public function TransferToDropBox()
	{

		$statusTransfering=9;
		$queryUserInfo="SELECT u.netid,u.dropboxpath, u.email FROM users u, blast_jobs j WHERE u.id=j.userid AND j.id=".$this->jobid;
		$userInfo = $this->sqlDataBase->query($queryUserInfo);
		$userNetid=$userInfo[0]["netid"];
		$userDropBoxPath=$userInfo[0]["dropboxpath"];
		$userEmail = $userInfo[0]["email"];

		if(file_exists($userDropBoxPath) && is_dir($userDropBoxPath))
		{
			if(Date('H') >= 17 || Date('H') <= 5 || Date('N') >= 6)
			{
				$ps=Exec::run_in_background($this->perlPath." scripts/transfer.pl ".$this->finalizePath." ".$userDropBoxPath." ".$this->jobid." ".$userNetid." ".$userEmail." ".$this->configFilePath);
				$queryUpdateTransferPid="UPDATE blast_jobs SET transferpid=".$ps.", status=".$statusTransfering."  WHERE id=".$this->jobid;
				$this->sqlDataBase->nonSelectQuery($queryUpdateTransferPid);
				echo "<FONT COLOR=\"green\">Transfering Results...<br>You will recieve an e-mail when the transfer is complete.</FONT>";
			}
			else{
				echo "<FONT COLOR=\"red\">Transfers to dropbox are only available during non-business hours due to file-server load.<br>If you would like to transfer the files to another server please send a request to help@igb.uiuc.edu</FONT>";
			}
		}else{
			echo "<FONT COLOR=\"red\">The drop box path does not exist (".$userDropBoxPath.")<br>Please contact your system admin for assistance.</FONT>";
		}
	}

    /**User scp to transfer job to another computer
     * @param $host
     * @param $path
     * @param $username
     * @param $password
     */
	public function SCPJob($host,$path,$username,$password)
	{
		$statusTransfering=9;
                $queryUserInfo="SELECT u.netid,u.dropboxpath, u.email FROM users u, blast_jobs j WHERE u.id=j.userid AND j.id=".$this->jobid;
                $userInfo = $this->sqlDataBase->query($queryUserInfo);
                $userNetid=$userInfo[0]["netid"];
		$userEmail = $userInfo[0]["email"];
		
		$ps=Exec::run_in_background($this->perlPath." scripts/scp.pl ".$this->finalizePath." ".$host." ".$username." ".$password." ".$path." ".$this->jobid." ".$userNetid." ".$userEmail." ".$this->configFilePath);
               	$queryUpdateTransferPid="UPDATE blast_jobs SET transferpid=".$ps.", status=".$statusTransfering."  WHERE id=".$this->jobid;
                $this->sqlDataBase->nonSelectQuery($queryUpdateTransferPid);
                echo "<br><FONT COLOR=\"green\">Transfering Results...<br>You will recieve an e-mail when the transfer is complete or has failed.</FONT>";

	}

    /**if the fasta file is bigger than 2GB then allow the user to give a URL from where webblast can download it.
     * @param $url
     * @return bool
     */
	public function GetQueriesFromURL($url)
        {
                //Check if the file exists at the url before trying to fetch it
                $file = @fopen($url,r);
                if($file)
                {
                        fclose($file);
                        $this->wgetURL=$url;
                        return true;
                }
                else
                {
                        $this->wgetURL=0;
                }

                return false;

        }


        public function GetQueriesFromFile($tempFilePost)
        {
		echo "Uploaded file";
                if(is_uploaded_file($tempFilePost))
                {
			$this->tempFilePost=$tempFilePost;
                      	return true;
                }
		else
		{
			$this->$tempFilePost=0;
		}

                return false;
        }

    /**
     * Use the fasta parser script to add queries to database for this job from an uploaded file
     */
	public function AddQueries()
	{
		if($this->tempFilePost)
		{
			move_uploaded_file($this->tempFilePost,$this->uploadPath.$this->fastaFile);
		}

		echo $this->perlPath." scripts/fastaParser.pl ".$this->uploadPath.$this->fastaFile." ".$this->jobid." ".$this->chunkSize." ".$this->configFilePath." 0 ".$this->userid;	
		$ps=Exec::run_in_background($this->perlPath." scripts/fastaParser.pl ".$this->uploadPath.$this->fastaFile." ".$this->jobid." ".$this->chunkSize." ".$this->configFilePath." ".$this->wgetURL." ".$this->userid);
		
		$queryUpdateJobPID="UPDATE blast_jobs SET submitpid=".$ps." WHERE id=".$this->jobid;
		$this->sqlDataBase->nonSelectQuery($queryUpdateJobPID);
	}

    /**
     * Delete all queries for this job from database
     */
	function ClearAllQueries()
	{
		$sql = "DELETE FROM blast_queries WHERE jobid=".$this->jobid;
		$this->sqlDataBase->nonSelectQuery($sql);
	}

    /**Concatinate CSV chunk results together
     * @param $netid
     * @param $destinationPath
     */
	function ConcatCSV($netid,$destinationPath)
	{
		$ps=Exec::run_in_background($this->perlPath." scripts/concat.pl ".$this->jobid." ".$netid." ".$this->csvPath." ".$destinationPath." csv");
		$this->sqlDatabase("UPDATE blast_jobs SET concatpidcsv=".$ps." WHERE id=".$this->jobid);	
	}

        function ConcatRaw($netid,$destinationPath)
        {
                $ps=Exec::run_in_background($this->perlPath." scripts/concat.pl ".$this->jobid." ".$netid." ".$this->resultsPath." ".$destinationPath." result");
		$this->sqlDatabase("UPDATE blast_jobs SET concatpidraw=".$ps." WHERE id=".$this->jobid);
        }

        function ConcatFasta($netid,$destinationPath)
        {
	
                $ps=Exec::run_in_background($this->perlPath." scripts/concat.pl ".$this->jobid." ".$netid." ".$this->queryPath." ".$destinationPath." fasta");
        }

    /**Delete all job files
     * @param $dir
     * @param int $mkdir
     * @param $type
     */
	private function delTree($dir,$mkdir=0,$type) 
	{
		$ps=Exec::run_in_background($this->perlPath." scripts/deleteFolder.pl ".$dir." ".$this->deletePath." ".$this->jobid. " ".$mkdir." ".$type);
	}

    /**Delete a file
     * @param $dir
     * @param $filename
     */
	private function delFile($dir,$filename)
	{
		$ps=Exec::run_in_background("rm -f ".$dir.$filename);
	}

    /**get all configuration values for job from centralized configuration file
     * @param $config_array
     */
	private function GetConfigValues($config_array)
	{
                $this->queryPath=$config_array['head_paths']['query_chunks_path'];
                $this->resultsPath=$config_array['head_paths']['result_chunks_path'];
                $this->deletePath=$config_array['head_paths']['delete_path'];
                $this->csvPath=$config_array['head_paths']['csv_chunks_path'];
		$this->finalizePath=$config_array['head_paths']['finalized_results_path'];
		$this->perlModule=$config_array['perl_bin']['perl_module'];
		$this->perlPath=$config_array['perl_bin']['perl_bin_path'];
		$this->configFilePath=$config_array['config_path']['config_file_path'];
		$this->uploadPath=$config_array['head_paths']['php_upload_dir'];

	}

    /**Check if there are any running queries for this job
     * @return bool
     */
	private function CheckNoRunningQueries()
	{
		$statusRunning=2;
                $queryRunningQueries="SELECT id FROM blast_queries WHERE statusid=".$statusRunning." AND jobid=".$this->jobid;
                if($this->sqlDataBase->countQuery($queryRunningQueries) == 0)
                {
			return true;
		}else{
			return false;
		}
	
	}

    /**Get a query string from job
     * @param $queryId
     * @return array
     */
	public function GetQueryString($queryId)
	{
		$queryContents=file($this->queryPath.$this->jobid."/".$queryId.".fasta");
		return $queryContents;
	}

    /**Get a result string for this job
     * @param $resultsId
     * @return array
     */
	public function GetResultsString($resultsId)
	{
		$resultsContents=file($this->resultsPath.$this->jobid."/".$resultsId.".result");
                return $resultsContents;
	}

    /**Get the results file path for this job
     * @return string
     */
	public function GetResultsFilePath()
	{
		$queryUserName = "SELECT netid FROM users WHERE id=".$this->userid;
		$userName = $this->sqlDataBase->singleQuery($queryUserName);
		if($userName)
		{
			$resultsFilePath = $this->finalizePath.$userName."_".$this->jobid.".result";
			return $resultsFilePath;
		}
	}

    /**Get a CSV results file path
     * @return string
     */
	public function GetCSVResultsFilePath()
	{
		$queryUserName = "SELECT netid FROM users WHERE id=".$this->userid;
                $userName = $this->sqlDataBase->singleQuery($queryUserName);
                if($userName)
                {
                        $resultsFilePath = $this->finalizePath.$userName."_".$this->jobid.".csv";
                        return $resultsFilePath;
                }
	}

    //Getters and Setters
	public function GetQueriesAdded()
	{
		return $this->queriesAdded;
	}

	public function GetBlastId()
	{
		return $this->blastId;
	}

	public function GetJobId()
	{
		return $this->jobid;
	}
		
	public function GetUserId()
	{
		return $this->userid;
	}
	
	public function GetToken()
	{
		return $this->token;
	}
}


?>
