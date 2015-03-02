<?php

class Database
{

	private $sqlDatabase;
	private $databaseId;
	private $userDatabasePath;
	private $databaseName;
	private $description;
	private $status;
	private $userId;
	private $lastUpdate;
	private $type;
	private $configFilePath;
	private $configPerlPath;
	private $wgetURL;

	//Formatdb settings
	private $dbInSeqId;
	private $dbASNFormat;
	private $dbASNMode;
	private $dbInSeqEntry;
	private $dbCreateIndexes;
	private $dbTaxonomicInfo;
	
	
	public function __construct(SQLDataBase $sqlDatabase,$config_array)
	{
		$this->sqlDatabase = $sqlDatabase;
		$this->GetConfigurationValues($config_array);
		$this->wgetURL = 0;
	}

	public function __destruct()
	{

	}
	
	public function LoadDatabase($databaseId)
	{
		$queryDatabase = "SELECT * FROM dbs WHERE id=".$databaseId;
		$database = $this->sqlDatabase->query($queryDatabase);
		if($database)
		{
			$this->databaseId = $databaseId;
			$this->description = $database[0]['description'];
			$this->status = $database[0]['active'];
			$this->userId = $database[0]['userid'];
			$this->lastUpdate = $database[0]['last_update'];
			$this->type = $database[0]['type'];
			$this->databaseName = $database[0]['dbname'];
		}
	}

	public function CreateDatabase($description,$status,$type,$userid)
	{
		$typeDescription = ($type=="F")?"n":"p";
		$databaseName = $userid."_".Date("YmdHis");
		$queryInsertDatabase = "INSERT INTO dbs (dbname,description,active,userid,type,last_update)
					VALUES(\"".$databaseName."\",\"".$description."\",".$status.",".$userid.",\"".$typeDescription."\",NOW())";
		$insertid = $this->sqlDatabase->insertQuery($queryInsertDatabase);
		if($insertid)
		{
			$this->databaseId = $insertid;
			$this->databaseName = $databaseName;
			$this->description = $description;
			$this->status = $status;
			$this->userid = $userid;
			$this->lastUpdate = date('Y-m-d H:m:s');
			$this->type= $type;
			return true;
		}
		else
		{
			return false;
		}
	}

	public function UpdateDatabase()
	{
		$queryUpdateDatabase = "UPDATE dbs SET dbname=\"".$this->databaseName."\",
						description=\"".$this->description."\",
						active=\"".$this->status."\",
						userid=\"".$this->userid."\",
						type=\"".$this->type."\",
					WHERE id=".$this->databaseId;
		$this->sqlDatabase->nonSelectQuery($queryUpdateDatabase);	
	}	

	public function DeleteDatabase()
	{
		if($this->databaseName!="")
		{
			$dbTypePath = "";
			if($this->userId > 0)
			{
				$dbTypePath = "USER/";
			}
			else
			{
				$dbTypePath= "NCBI/";
			}
			$queryDeleteDatabase = "DELETE FROM dbs WHERE id=".$this->databaseId;
			echo $queryDeleteDatabase;
			$this->sqlDatabase->nonSelectQuery($queryDeleteDatabase);
			echo "rm -f ".$this->userDatabasePath.$dbTypePath.$this->databaseName."* &";
			Exec::run_in_background("rm -f ".$this->userDatabasePath.$dbTypePath.$this->databaseName."* &");
		}
		
	}
	
	public function GetConfigurationValues($config_array)
	{
		$this->userDatabasePath = $config_array['head_paths']['databases_path']."USER/";
		$this->configFilePath = $config_array['config_path']['config_file_path'];
		$this->configPerlPath = $config_array['perl_bin']['perl_bin_path'];
	}

	public function GetDatabaseFromURL($url)
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

	public function GetDatabaseFromFile($tempFilePost)
	{
		if(is_uploaded_file($tempFilePost))
		{	
			if(move_uploaded_file($tempFilePost,$this->userDatabasePath.$this->databaseName))
			{
				return true;	
			}
		}
		
		return false;
	}

	public function CompileDatabase($dbSeqId,$dbASNFormat,$dbASNMode,$dbInSeqEntry,$dbCreateIndexes,$dbTaxonomicInfo)
	{
		echo $this->configPerlPath." scripts/addDatabase.pl ".$this->databaseName." \"".$this->description."\" ".$this->type." ".$this->userDatabasePath.$this->databaseName." ".$dbSeqId." ".$dbASNFormat." ".$dbASNMode." ".$dbInSeqEntry." ".$dbCreateIndexes." ".$dbTaxonomicInfo." ".$this->userid." ".$this->databaseId." ".$this->configFilePath." ".$this->wgetURL;
		Exec::run_in_background($this->configPerlPath." scripts/addDatabase.pl ".$this->databaseName." \"".$this->description."\" ".$this->type." ".$this->userDatabasePath.$this->databaseName." ".$dbSeqId." ".$dbASNFormat." ".$dbASNMode." ".$dbInSeqEntry." ".$dbCreateIndexes." ".$dbTaxonomicInfo." ".$this->userid." ".$this->databaseId." ".$this->configFilePath." ".$this->wgetURL);
	}

	//Getters setters
	public function getDatabaseId() { return $this->databaseId; } 
	public function getDatabasePath() { return $this->userDatabasePath; } 
	public function getDatabaseName() { return $this->databaseName; }
	public function getDescription() { return $this->description; } 
	public function getStatus() { return $this->status; } 
	public function getUserId() { return $this->userId; } 
	public function getLastUpdate() { return $this->lastUpdate; } 
	public function getType() { return $this->type; } 
	public function setDescription($x) { $this->description = $x; } 
	public function setStatus($x) { $this->status = $x; } 
	public function setUserId($x) { $this->userId = $x; } 
	public function setType($x) { $this->type = $x; } 
}

?>
