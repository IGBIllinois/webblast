<?php
class Profile
{
	private $profileid;
	private $profilename;
	private $userid;
	private $sqlDataBase;
	private $description;
	private $queryPath;
	private $resultsPath;
	private $concatPath;
	private $submitpid;
	private $deletePath;
	private $csvPath;
	private $concatPid;
	private $transferPid;
	private $csvConcatPid;
	private $deletePid;
	private $destPath;
	private $chunkSize;

	public function __construct(SQLDataBase $sqlDataBase)
	{	
		$this->sqlDataBase=$sqlDataBase;
		$profileid="";
		$profilename="";
		$userid="";
		$description="";
		$queryPath="";
		$countQueries=0;
	}

	public function __destruct()
	{
	}

    /**
     * Delete a user's job profile
     */
	public function DeleteProfile()
	{
		$queryDeleteProfile = "DELETE * FROM blast_profiles WHERE id=".$this->profileid;
		$this->sqlDataBase->nonSelectQuery($queryDeleteProfile);
	}

    /**Create a custom job profile, allows a user to create one profile and then run multiple jobs with the same blast settings
     * @param $newProfileName
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
	public function CreateProfile($newProfileName,$loggedUserid,$inputp,$inputd,$inpute,$inputm,$inputFU,$inputGU,$inputEU,$inputXU,$inputIU,$inputq,$inputr,$inputv,$inputb,$inputf,$inputg,$inputQU,$inputDU,$inputa,$inputJU,$inputMU,$inputWU,$inputz,$inputKU,$inputYU,$inputSU,$inputTU,$inputl,$inputUU,$inputy,$inputZU,$inputRU,$inputn,$inputLU,$inputAU,$inputw,$inputt,$inputBU,$inputCU,$paramsEnabled,$chunkSize)
	{
		$statusNew=1;
		$this->profilename = $newProfileName;
		$this->userid = $loggedUserid;
		$this->chunkSize=$chunkSize;

		$sql = "INSERT INTO blast_profiles (name,userid,blastid,dbid,e,m,FU,GU,EU,XU,IU,q,r,v,b,f,g,QU,DU,a,JU,MU,WU,z,KU,YU,SU,TU,l,UU,y,ZU,RU,n,LU,AU,w,t,BU,CU,paramsenabled,chunksize) VALUES (\"".$this->profilename."\",".$this->userid.",".$inputp.",".$inputd.",".$inpute.",".$inputm.",\"".$inputFU."\",".$inputGU.",".$inputEU.",".$inputXU.",\"".$inputIU."\",".$inputq.",".$inputr.",".$inputv.",".$inputb.",".$inputf.",\"".$inputg."\",".$inputQU.",".$inputDU.",".$inputa.",\"".$inputJU."\",\"".$inputMU."\",\"".$inputWU."\",".$inputz.",".$inputKU.",".$inputYU.",".$inputSU.",".$inputTU.",\"".$inputl."\",\"".$inputUU."\",".$inputy.",".$inputZU.",\"".$inputRU."\",\"".$inputn."\",\"".$inputLU."\",".$inputAU.",".$inputw.",".$inputt.",".$inputBU.",\"".$inputCU."\",\"".$paramsEnabled."\",".$chunkSize.")";

		$this->profileid=$this->sqlDataBase->insertQuery($sql);
		
	}

    /**Load profile from database
     * @param $id
     */
	public function LoadProfile($id)
	{
		$sql = "SELECT * FROM blast_profiles WHERE id=".$id;
		$profileInfoArray = $this->sqlDataBase->query($sql);
		$this->profileid = $profileInfoArray[0]["id"];
		$this->profilename = $profileInfoArray[0]["name"];
		$this->userid = $profileInfoArray[0]["userid"];
		$this->submitpid= $profileInfoArray[0]["submitpid"];	
		$this->concatPid = $profileInfoArray[0]["concatpidresult"];
		$this->transferPid = $profileInfoArray[0]["transferpid"];
		$this->deletePid = $profileInfoArray[0]["deletepid"];
		$this->csvConcatPid=$profileInfoArray[0]["concatpidcsv"];
		$this->chunkSize=$profileInfoArray[0]["chunksize"];
		$this->GetConfigValues();
	}

}

?>
