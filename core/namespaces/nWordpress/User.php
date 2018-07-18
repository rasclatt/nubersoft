<?php
namespace nWordpress;

class User extends \Nubersoft\nFunctions
{
	private	$user;
	/**
	*	@desctription	This is just a quick link to login user
	*/
	public	static	function isLoggedIn()
	{
		return is_user_logged_in();
	}
	
	public	function __construct()
	{
		if(empty($this->user))
			$this->user	=	wp_get_current_user();
	}
	
	public	function getUser()
	{
		return $this->user;
	}
	
	public	function getRoles()
	{
		$user	=	$this->getUser();
		return (!empty($user->roles))? $user->roles : [];
	}
	
	public	function isAdmin()
	{
		$arr	=	$this->getRoles();
		return in_array('administrator',$this->getRoles());
	}
	
	public	function get($userid,$key=false)
	{
		$key	=	(!empty($key))? $key : 'email';
		
		return (is_numeric($userid))? get_userdata($userid) : get_user_by($key,$userid);
	}
	
	public	function getUserId()
	{
		$User	=	$this->getUser();
		$id		=	(!empty($User))? $User->ID : false;
		
		return $id;
	}
	
	public	function getUserByMeta($meta,$value=false)
	{
		return (!empty($value))? get_users(['meta_key'=>$meta,'meta_value'=>$value,'number' => 1,'count_total' => false]) : get_users(['meta_key'=>$meta]);
	}
	
	public	function userValid($username, $password, $key = 'email')
	{
		$this->user		=	$this->get($username, $key);
		$user_id	=	(!empty($this->user->ID))? $this->user->ID : false;
		
		if(empty($user_id))
			return false;
		
		return wp_check_password($password, $this->user->user_pass, $user_id);
	}
}