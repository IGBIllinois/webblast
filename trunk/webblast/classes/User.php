<?php

class User
{
	private $sqlDataBase;
	private $userId;
	private $netid;
	private $first;
	private $last;
	private $email;
	private $authToken;
		
	public function __construct(SQLDataBase $sqlDataBase)
	{
		$this->sqlDataBase = $sqlDataBase;
	}

	public function __destruct()
	{

	}

	public function CreateUser($netid,$first,$last,$email)
	{
		$queryInsertNewUser = "INSERT INTO users (netid,first,last,email,auth_token)VALUES(\"".$netid."\",\"".$first."\",\"".$last."\",\"".$email."\",MD5(RAND()))";
		$userId = $this->sqlDataBase->insertQuery($queryInsertNewUser);
		if($userId)
		{
			$this->userId = $userId;
			$this->netid = $netid;
			$this->first = $first;
			$this->last = $last;
			$this->email = $email;
			return $userId;
		}
		
		return 0;

	}

	public function LoadUser($userId)
	{
		$queryUserInfo = "SELECT netid,first,last,email,auth_token FROM users WHERE id=".$userId;
		$userInfo = $this->sqlDataBase->query($queryUserInfo);
		if($userInfo)
		{
			$this->userId = $userId;
			$this->netid = $userInfo[0]['netid'];
			$this->first = $userInfo[0]['first'];
			$this->last = $userInfo[0]['last'];
			$this->email = $userInfo[0]['email'];
			$this->authToken = $userInfo[0]['auth_token'];

			return true;
		}
		else
		{
			return false;
		}		
	}

	public function UpdateAuthToken()
	{
		$queryUpdateAuthToken = "UPDATE users SET auth_token=MD5(RAND()) WHERE id=".$this->userId;
		$this->sqlDataBase->nonSelectQuery($queryUpdateAuthToken);
		$this->LoadUser($this->userId);
	}

	public function UpdateUserInfo()
	{
		$queryUpdateUserInfo = "UPDATE users SET first=\"".$this->first."\",last=\"".$this->last."\",email=\"".$this->email."\" WHERE id=".$this->userId;
		$this->sqlDataBase->nonSelectQuery($queryUpdateUserInfo);
	}
	
	public function GetUserIdFromNetid($netid)
	{
		$queryUserId = "SELECT id FROM users WHERE netid=\"".$netid."\"";
		return $this->sqlDataBase->singleQuery($queryUserId);
	}

	//Getters setters
	
	public function getUserId() { return $this->userId; } 
	public function getNetid() { return $this->netid; } 
	public function getFirst() { return $this->first; } 
	public function getLast() { return $this->last; } 
	public function getEmail() { return $this->email; }
	public function getAuthToken() { return $this->authToken; } 
	public function setFirst($x) { $this->first = $x; } 
	public function setLast($x) { $this->last = $x; } 
	public function setEmail($x) { $this->email = $x; } 
	public function setAuthToken($x) { $this->authToken = $x; }
}


?>
