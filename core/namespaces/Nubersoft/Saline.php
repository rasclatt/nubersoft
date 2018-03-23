<?php
namespace Nubersoft;

class	Saline
{
	protected	static	$BcryptEngine,
						$PasswordHash,
						$PasswordEngine;

	private		static	$username,
						$password;
	/**
	*	@description	Main method to verify and login user
	*	@param	$username	[string]	Self Explanitory (SE)
	*	@param	$password	[string]	SE
	*	@param	$return		[boolean]	If the intention is merely to return validation, set TRUE
	*/
	public	static function verify($username = false, $password = false, $return =  false)
	{
		self::$username	=	trim($username);
		self::$password	=	trim($password);
		# Set default log array
		$log			=	[
			'success'=>false,
			'error'=>'Username/Password Invalid'
		];
		# Core helper
		$nApp	=	nApp::call();
		# Check if either value is empty
		if(!empty(self::$username) && !empty(self::$password)) {
			# Password hashing / Bcrypt takes a bit of time to execute
			set_time_limit(15);
			# Get the User class
			$User	=	$nApp->getHelper('User');
			# Fetch username from database
			$result	=	$User->getUser(self::$username,false);
			# If no such user, return false
			if(empty($result))
				return false;
			# Set default engine
			$engine	=	PasswordGenerator::USE_DEFAULT;
			# If specifying engine
			if(defined('PASSWORD_ENGINE'))
				$engine	=	(strtolower(PASSWORD_ENGINE) == 'bcrypt')? PasswordGenerator::BCRYPT : PasswordGenerator::USE_DEFAULT;
			# Create self-instance
			$Saline		=	self::App();
			# Check password in default
			$validate	=	$Saline->passwordVerified($result['password'],$engine);
			# Run temporary password setup
			if(!$validate) {
				if(!empty($result['reset_password'])) {
					# Check temporary password
					$validate	=	$Saline->passwordVerified($result['reset_password'],$engine);
					# If temporary password is used, reset back to empty
					if($validate) {
						$User->resetTempPassword(self::$username);
					}
				}
			}
			# If users found
			if($validate) {
				# If return data is set
				if($return)
					return $result;
				# Regenerate the session id
				$nApp->getHelper('nSessioner')->newId();
				# Add set user values to session
				$User->loginUser($result,true);
				//nApp::call()->removeDataNode('_SESSION');
				//nApp::call()->getHelper('Submits')->setSessionGlobal();
				# Stop and return successful
				return true;
			}
			# Add kind of error
			$log['type']	=	"mismatch";
		}
		else
			# Add kind of error
			$log['type']	=	"empty";
		# Store result for messaging
		$nApp->saveIncidental('login',$log);
		return false;
	}
	/**
	*	@description	Main engine to check password against database hash
	*	@param	$password	[string]	SE
	*	@param	$engine		[constant]	Tells the password verify what kind of password method to use
	*	@returns bool	If user / password matches in database
	*/
	public	function passwordVerified($password,$engine=false)
	{
		$engine					=	(empty($engine))? PasswordGenerator::USE_DEFAULT : $engine;
		self::$PasswordEngine	=	PasswordGenerator::Engine($engine);
		return self::$PasswordEngine
			->setUser(self::$username)
			->verifyPassword(self::$password,$password)
			->isValid();
	}
	/**
	*	@description	Returns self for non-static use
	*/
	protected	static	function App()
	{
		return new Saline();
	}
}