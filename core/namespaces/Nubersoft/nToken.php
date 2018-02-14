<?php
namespace	Nubersoft;

class	nToken extends \Nubersoft\nApp
{
	public	function create($name)
	{
		$token	=	$this->safe()->encOpenSSL(mt_rand());
		$this->setSession('token_'.$name,$token,true);

		return $token;
	}

	public	function fetch($key,$clear = true)
	{
		$value	=	$this->getSession('token_'.$key,$clear);
		if(empty($value))
			return false;

		return $this->safe()->decOpenSSL($value);
	}

	public	function getSetToken($name = 'nProcessor', $salt = false, $multiToken = false)
	{
		$multiToken	=	(is_array($salt));
		// Get the token value/array
		$SESS	=	(isset($_SESSION['token'][$name]))? $_SESSION['token'][$name] : false;
		// If not set
		if(empty($SESS)) {
			return $this->setToken($name,$salt,$multiToken);
		}
		else {
			// If return is an array
			if(is_array($SESS)) {
				// If there is a salt and it's an array
				if($salt && is_array($salt)) {
					// Get the values
					$useKey	=	$salt[0];
				}
				$setKey	=	(isset($useKey))? $useKey : $salt;
				// If there is already a value, return it
				if(isset($SESS[$setKey]))
					return $SESS[$setKey];
				// If not, make value
				else
					return $this->setToken($name,$salt,$multiToken);
			}
			else {
				return $_SESSION['token'][$name];
			}
		}
	}
	/**
	*	@description			This function will set a token array
	*	@param	$name [string]	This is the name of the array
	*	@param	$salt [array]	Requires two strings in the array, the name of the token and a value to be md5ed
	*/
	public	function setMultiToken($name,$key)
	{
		$rand	=	mt_rand(10000,99999);
		$salt	=	array($key,$rand);
		return $this->getSetToken($name,$salt,true);
	}

public	function setToken($name = false, $salt = false, $multiToken = false)
	{
		if($salt && is_array($salt)) {
			$useKey	=	$salt[0];
			$salt	=	$salt[1];
		}
		else
			// Create salt for token
			$salt	=	($salt)? $salt : mt_rand(100,999);
		// Create a token
		$MD5	=	md5($salt);
		// If it's supposed to be a multi-token
		if($multiToken) {
			// Save to array
			if(isset($_SESSION['token']) && (isset($_SESSION['token'][$name]) && is_array($_SESSION['token'][$name]))) {
				if(!in_array($MD5,$_SESSION['token'][$name])) {
					if(isset($useKey))
						$_SESSION['token'][$name][$useKey]	=	$MD5;
					else
						$_SESSION['token'][$name][$salt]	=	$MD5;
				}
			}
			else {
				if(isset($useKey))
					$_SESSION['token'][$name][$useKey]	=	$MD5;
				else
					$_SESSION['token'][$name][$salt]	=	$MD5;
			}

			$_SESSION['token'][$name]	=	array_unique($_SESSION['token'][$name]);

			return $MD5;
		}
		else {
			// Save to string
			$_SESSION['token'][$name]	=	$MD5;
			return $MD5;
		}
	}

	public	function tokenExists($name = false,$multiToken = false)
	{
		$SESS	=	(isset($_SESSION['token'][$name]))? $_SESSION['token'][$name] : false;

		if(!$SESS)
			return false;

		if(!$multiToken && $SESS)
			return true;
		elseif($multiToken && isset($SESS[$multiToken]))
			return true;
		else
			return false;
	}

	public	function clearToken($key = false,$tokenKey = false)
	{
		// If the main token is set
		if(isset($_SESSION['token'][$key])) {
			// If the token is an array
			if($tokenKey && is_array($_SESSION['token'][$key])) {
				// See if there is a value the array
				if(isset($_SESSION['token'][$key][$tokenKey])) {
					// Unset it
					unset($_SESSION['token'][$key][$tokenKey]);
					// Set to null just in case
					if(isset($_SESSION['token'][$key][$tokenKey]))
						$_SESSION['token'][$key][$tokenKey]	=	NULL;
				}
				// Return
				return;
			}

			unset($_SESSION['token'][$key]);
			if(isset($_SESSION['token'][$key]))
				$_SESSION['token'][$key]	=	NULL;
		}
	}

	public	function tokenMatch($token_name = false,$req = false)
	{
		if(empty($token_name)) {
			$this->saveIncidental('token_match', array('error'=>'token empty'));
			return false;
		}

		$name	=	$token_name;
		$req	=	(empty($req))? $this->toArray($this->getPost()) : $req;
		$key	=	false;

		if(!isset($req['token'][$name])) {
			$this->saveIncidental('token_match', array('error'=>'token request not made'));
			return false;
		}
		else {
			// If there is an token, check against session
			if(!isset($_SESSION['token'][$name])) {
				$this->saveIncidental('token_match', array('error'=>'server-side token not set'));
				return false;
			}
			elseif(isset($_SESSION['token'][$name])) {
				if(is_array($_SESSION['token'][$name])) {
					$key	=	array_search($req['token'][$name],$_SESSION['token'][$name]);
					return ($key !== false)? $key : false;
				}
				else {
					if($_SESSION['token'][$name] != $req['token'][$name]) {
						$this->saveIncidental('token_match', array('error'=>'token mismatch'));
						return false;
					}
					else {
						$key	=	$name;
					}
				}
			}
		}

		$this->clearToken($name,(($key !== false)? $key : false));
		return $key;
	}

	public	function resetTokenOnMatch($REQUEST,$name = 'nProcessor',$default = 'page',$salt = false)
	{
		$salt	=	(!empty($salt))? $salt : mt_rand(1000,9999);
		// Process token
		if(!$this->tokenExists($name)) {
			// Save error
			$this->saveIncidental('token_error', array('error'=>'no token found'));
			// Reset the token for this page
			$this->saveSetting($name,$this->getSetToken($name,array($default,$salt),true));
			// Stop action
			return false;
		}
		else {
			$tokenMatch	=	$this->tokenMatch($name,$REQUEST);
			// If the token doesn't match, stop
			if(!$tokenMatch)
				return false;
			// If matches, continue and save a new token
			else
				$this->saveSetting($name,$this->getSetToken($name,array($tokenMatch,$salt),true));

			return true;
		}
	}

	public	function getPageToken($reset = false)
	{
		$token	=	false;
		if(!empty($this->getSession('token')->nProcessor->page))
			$token	=	$this->getSession('token')->nProcessor->page;

		if($token && $reset) {
			parent::$settings->_SESSION->token->nProcessor->page	=	
			$_SESSION['token']['nProcessor']['page']	=	$this->safe()->encOpenSSL(mt_rand());
		}

		return $token;
	}

	public	function getTokenFromPool($key,$pool = false)
	{
		if(!empty($this->getSession('token'))) {
			if(!empty($pool)) {
				return (!empty($this->getSession('token')->{$pool}->{$key}))? $this->getSession('token')->{$pool}->{$key} : false;
			}

			return (!empty($this->getSession('token')->{$key}))? $this->getSession('token')->{$key} : false;
		}

		return false;
	}

	public	function nOnceDecode($value, $urlenc = true, $json = false)
	{
		$decVal	=	$this->safe()->decOpenSSL($value,array('urlencode'=>$urlenc));
		$decode	=	($json)? json_decode($decVal,true) : $decVal;
		$value	=	($json)? $this->safe()->decOpenSSL($decode[$json]) : $decVal;
		return $this->nOnce($value);
	}

	public	function nOnce($name,$value=NULL)
	{
		$Methodizer	=	$this->getHelper('Methodize');
		$nQuery	=	$this->nQuery();
		$sqlGet	=	"SELECT
							`ID`,`category_id`,`content`
						FROM
							components
						WHERE
							`component_type` = 'nonce'
						AND
							`category_id` = :0";
		if($value == NULL) {
			$nonce	=	$nQuery->query($sqlGet,array($name))->getResults(true);
			if($nonce == 0)
				return $Methodizer;

			$Methodizer->saveAttr('token',$this->safe()->encOpenSSL($name));
			$Methodizer->saveAttr('value',$nonce['content']);

			return $Methodizer;
		}
		else {
			$sql	=	"SELECT
							COUNT(*) as count
						FROM
							components
						WHERE
							`component_type` = 'nonce'
						AND
							`category_id` = :0";

			$query	=	$nQuery->query($sql,array($name))->getResults(true);

			if($query['count'] == 0) {
				$sql	=	"INSERT INTO components 
								(`component_type`,`category_id`,`content`)
							VALUES
								('nonce', :0, :1)";

				$nQuery->query($sql,array($name,$value));
			}

			$query	=	$nQuery->query($sqlGet,array($name))->getResults(true);

			$Methodizer->saveAttr('token',$this->safe()->encOpenSSL($name));
			$Methodizer->saveAttr('value',$query['content']);

			return $Methodizer;
		}
	}

	public	function nOnceClear($key)
	{
		$sql	=	"DELETE 
					FROM
						components
					WHERE
						`component_type` = 'nonce'
					AND
						`category_id` = :0";

		$this->nQuery()->query($sql,array($key));
		return $this;
	}

	public	function matchTo($formToken)
	{
		$formToken	=	trim($formToken);
		if(empty($formToken))
			return false;

		$token	=	$this->getPageToken();

		if(empty($token))
			return false;

		return ($token == $formToken);
	}
}