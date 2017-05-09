<?php
namespace Nubersoft;

class	PasswordVerify implements \Nubersoft\PasswordProtect
	{
		public	$valid;
		
		private	$username,
				$password,
				$pHash;
		
		private	$checkCol	=	'password';
		
		public	function __construct($rounds = false)
			{
			}
		
		public	function setCheckColumn($col = 'password')
			{
				$this->checkCol	=	$col;
				return $this;
			}
		
		public	function hashPassword($password = false)
			{
				$this->hash($password);
				return $this;
			}
		
		public	function hash($password = false)
			{	
				$this->password	=	$password;
				$this->pHash	=	password_hash($this->password, PASSWORD_DEFAULT);
				return $this;	
			}
		
		public	function verifyPassword($password = false, $hash = false)
			{
				$this->verify($password, $hash);
				return $this;
			}
		
		public	function getHash()
			{
				return (!empty($this->pHash))? $this->pHash : false;
			}
		
		public	function isValid()
			{
				return $this->valid;
			}
			
		public	function verify($password = false, $hash = false)
			{
				$nApp	=	nApp::call();
				
				if(!empty($password))
					$this->password	=	$password;
				
				if(!empty($hash))
					$this->pHash	=	$hash;
				else {
					$user	=	$nApp->nQuery()
									->select(array($this->checkCol))
									->from("users")
									->where(array("username"=>$this->username))
									->getResults();
							
					if($user == 0)
						return false;
					
					$this->pHash	=	$user[0][$this->checkCol];
				}
				
				$this->valid	=	(password_verify($this->password,$this->pHash));
				
				return $this;
			}
			
		public	function setUser($username = false)
			{
				$this->username	=	$username;
				return $this;
			}
			
		public	function write()
			{
				if(!empty($this->pHash) && !empty($this->username)) {
					$nApp->nQuery()
						->update("users")
						->set(array("password"=>$this->pHash))
						->where(array("username"=>$this->username))
						->write();
								
					$user	=	$nApp->nQuery()
									->select(array("password"))
									->from("users")
									->where(array("username"=>$this->username))
									->getResults();
											
					if($user[0]['password'] == $this->pHash)
						return true;
				}
			
				return false;
			}
	}