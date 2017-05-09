<?php
	class	PasswordVerify implements PasswordProtect
		{
			public	$valid;
			
			private	$username;
			private	$password;
			private	$pHash;
			
			public	function __construct($rounds = false)
				{
				}
			
			public	function encrypt_password($password = false)
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
			
			public	function verify_password($password = false, $hash = false)
				{
					$this->verify($password, $hash);
					return $this;
				}
			
			public	function get_hash()
				{
					return (!empty($this->pHash))? $this->pHash : false;
				}
			
			public	function verify($password = false, $hash = false)
				{
					if(!empty($password))
						$this->password	=	$password;
					
					if(!empty($hash))
						$this->pHash	=	$hash;
					else {
							$user	=	nQuery()	->select(array("password"))
													->from("users")
													->where(array("username"=>$this->username))
													->fetch();

							if($user == 0)
								return false;
							
							$this->pHash	=	$user[0]['password'];
						}
					
					$this->valid	=	(password_verify($this->password,$this->pHash));
					
					return $this;
				}
				
			public	function set_user($username = false)
				{
					$this->username	=	$username;
					return $this;
				}
				
			public	function write()
				{
					if(!empty($this->pHash) && !empty($this->username)) {
							nQuery()	->update("users")
										->set(array("password"=>$this->pHash))
										->where(array("username"=>$this->username))
										->write();
										
							$user	=	nQuery()	->select(array("password"))
													->from("users")
													->where(array("username"=>$this->username))
													->fetch();
													
							if($user[0]['password'] == $this->pHash)
								return true;
						}
				
					return false;
				}
		}