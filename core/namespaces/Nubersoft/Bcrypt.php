<?php
// StackOverflow BLOW_FISH pre PHPv5.3.7
// Used like so:

/*
$bcrypt	=	new Bcrypt();
$hash	=	$bcrypt->hash('password');
$isGood	=	$bcrypt->verify('password', $hash);
*/
namespace Nubersoft;

class Bcrypt implements PasswordProtect
	{
		
		protected	$valid;
		
		private		$pHash,
					$username,
					$rounds,
					$randomState;
		
		public function __construct($rounds = false)
			{
				if(CRYPT_BLOWFISH != 1)
					throw new Exception("Bcrypt not supported in this installation. See http://php.net/crypt");

				$this->rounds = (empty($rounds))? 12 : $rounds;
			}
			
		public	function hashPassword($password = false)
			{
				$this->hash($password);
				return $this;
			}
		
		public function hash($input)
			{
				
				$this->pHash	=	crypt($input, $this->getSalt());
		
				if(strlen($this->pHash) > 13)
					return $this->pHash;
		
				return false;
			}
			
		public	function verifyPassword($password = false, $hash = false)
			{
				$this->verify($password, $hash);
				return $this;
			}
		
		public	function isValid()
			{
				return $this->valid;
			}
		
		public function verify($input, $existingHash)
			{
				
				$this->pHash	=	crypt($input, $existingHash);
				$this->valid	=	($this->pHash === $existingHash);
				
				return $this->valid;
			}
		
		private function getSalt()
			{
				$this->rounds	=	(empty($this->rounds))? 12 : $this->rounds;
				$salt			=	sprintf('$2a$%02d$', $this->rounds);
				$bytes			=	$this->getRandomBytes(16);
				$salt			.=	$this->encodeBytes($bytes);
		
				return $salt;
			}
		
		private function getRandomBytes($count)
			{
				$bytes	=	'';
		
				// OpenSSL is slow on Windows
				if(function_exists('openssl_random_pseudo_bytes') && (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN'))
					$bytes	=	openssl_random_pseudo_bytes($count);
				
				if($bytes === '' && is_readable('/dev/urandom') && ($hRand = @fopen('/dev/urandom', 'rb')) !== FALSE) {
					$bytes = fread($hRand, $count);
					fclose($hRand);
				}
				
				if(strlen($bytes) < $count) {
					$bytes = '';
				
					if($this->randomState === null) {
						$this->randomState = microtime();
						if(function_exists('getmypid'))
							$this->randomState .= getmypid();
					}
			
					for($i = 0; $i < $count; $i += 16) {
						$this->randomState = md5(microtime() . $this->randomState);
			
						if (PHP_VERSION >= '5')
							$bytes .= md5($this->randomState, true);
						else
							$bytes .= pack('H*', md5($this->randomState));
					}
			
					$bytes = substr($bytes, 0, $count);
				}
				
				return $bytes;
			}
		
		private function encodeBytes($input)
			{
				// The following is code from the PHP Password Hashing Framework
				$itoa64 = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		
				$output	= '';
				$i = 0;
				do {
					$c1		=	ord($input[$i++]);
					$output	.=	$itoa64[$c1 >> 2];
					$c1		=	($c1 & 0x03) << 4;
					
					if ($i >= 16) {
							$output	.=	$itoa64[$c1];
							break;
						}
	
					$c2		=	ord($input[$i++]);
					$c1		|=	$c2 >> 4;
					$output	.=	$itoa64[$c1];
					$c1		=	($c2 & 0x0f) << 2;
			
					$c2		=	ord($input[$i++]);
					$c1		|=	$c2 >> 6;
					$output	.= $itoa64[$c1];
					$output	.= $itoa64[$c2 & 0x3f];
				}
				while (1);
		
				return $output;
			}
	
		
		public	function get_hash()
			{
				return (!empty($this->pHash))? $this->pHash : false;
			}
				
		public	function setUser($username = false)
			{
				$this->username	=	$username;
				return $this;
			}
				
		public	function write()
			{
				if(!empty($this->pHash) && !empty($this->username)) {
					nquery()	->update("users")
								->set(array("password"=>$this->pHash))
								->where(array("username"=>$this->username))
								->write();
								
					$user	=	nquery()	->select(array("password"))
											->from("users")
											->where(array("username"=>$this->username))
											->getResults();
											
					if($user[0]['password'] == $this->pHash)
						return true;
				}
			
				return false;
			}
			
		public	function getHash()
			{
				return (!empty($this->pHash))? $this->pHash : false;
			}
	}