<?php

class Auth
{
	private $host;
	private $peopleDN;
	private $groupDN;
	private $ssl;
	private $port;
	private $adminGroup;
	private $error="No Error Detected";
	private $sqlDataBase;
	private $authUser;
	
	
	public function __construct(SQLDatabase $sqlDataBase,$config_array)
	{
		$this->sqlDataBase=$sqlDataBase;
		$this->host=$config_array['ldap_config']['ldap_host'];
                $this->peopleDN=$config_array['ldap_config']['ldap_peopleDN'];
                $this->groupDN=$config_array['ldap_config']['ldap_groupDN'];
                $this->ssl=$config_array['ldap_config']['ldap_ssl'];
                $this->port=$config_array['ldap_config']['ldap_port'];
                $this->adminGroup=$config_array['ldap_config']['ldap_admin_group'];
	}
	
	public function __destruct()
	{
	
	}

	public function AuthSession()
	{
		if(isset($_SESSION['username']) && isset($_SESSION['password']))
		{
			if($this->AuthLogin($_SESSION['username'],$_SESSION['password']))
			{
				return 1;
			}
		}
		
		$this->UnsetSession();

		return 0;
	}

	public function AuthSessionAdmin()
        {
                if(isset($_SESSION['username']) && isset($_SESSION['password']))
                {
                        if($this->AuthLogin($_SESSION['username'],$_SESSION['password'],$this->adminGroup))
                        {
                                return 1;
                        }
                }

                $this->UnsetSession();

                return 0;
        }

	public function AuthLogin($username,$password,$group="")
	{	
		if($this->AuthLdap($username,$password,$group))
		{
			$this->CreateSession($username,$password);
			return 1;
		}	
		return 0;	
	}

	public function AuthAdminLogin($username,$password)
	{
		if($this->AuthLdap($username,$password,$this->adminGroup))
                {
                        $this->CreateSession($username,$password);
                        return 1;
                }
                return 0;
	}
		
	public function AuthLogout()
	{
		$this->UnsetSession();
	}

	public function AuthLdap($username,$password,$group="")
	{
		if ($this->ssl == 1) {
                	$connect = ldap_connect("ldaps://" . $this->host,$this->port);
               
        	}
        	elseif ($this->ssl == 0) {
               		$connect = ldap_connect("ldap://" . $this->host,$this->port);
               
        	}
                 
        	$bindDN = "uid=" . $username . "," . $this->peopleDN;
       
       	 	$success = @ldap_bind($connect, $bindDN, $password);
		
        	if ($success == 1 && $group!="") {
                	$search = ldap_search($connect,$this->groupDN,"(cn=" . $group . ")");
                	$data = ldap_get_entries($connect,$search);
                	ldap_unbind($connect);
              
                	foreach($data[0]['memberuid'] as $groupMember) {
                       
                        	if ($username == $groupMember) {
                                	$success = 1;
                                	return $success;
                        	}
                        	else {
                                	$success = 0;
                        	}
                	}
               
        	}
		if($success == 0)
		{
			$error=ldap_error($connect);
		}
        	return $success;
	}

	public function AuthToken($token,$jobid)
	{
		$queryTokenAuth = "SELECT id FROM blast_jobs WHERE token=\"".$token."\" AND id=".$jobid;
		$tokenAuth = $this->sqlDataBase->query($queryTokenAuth);
		if($tokenAuth)
		{
			return 1;
		}
		return 0;
	}
	
	public function CreateSession($username,$password,$group)
	{
		$_SESSION['username']=$username;
		$_SESSION['password']=$password;
		$_SESSION['group']=$group;
		$this->authUser = new User($this->sqlDataBase);
		$userId = $this->authUser->GetUserIdFromNetid($username);
		if($userId)
		{
			$this->authUser->LoadUser($userId);
			$_SESSION['userid']=$userId;	
		}
		else
		{
			echo "create user";
			$userId = $this->authUser->CreateUser($username,"","",$username."@igb.illinois.edu");
			$_SESSION['userid']=$userId;
				
		}	
	}
	
	public function UnsetSession()
	{
		unset($_SESSION['username']);
		unset($_SESSION['password']);
		unset($_SESSION['userid']);
		unset($_SESSION['group']);
	}

	public function GetAuthUser()
	{
		return $this->authUser;
	}
}


?>
