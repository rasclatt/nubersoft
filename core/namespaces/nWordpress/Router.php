<?php
namespace nWordpress;

class Router extends \Nubersoft\nApp
{
	public	function redirectToError($family, $code = false,$path = false)
	{
		$query	=	'?error='.$family.'&code='.$code;
		
		if(empty($path)){
			wp_redirect('/'.$query);
			exit;
		}
		else {
			header('Location: '.$path.'?error='.$family.'&code='.$code);
			exit;
		}
	}
	
	public	function redirectToSuccess($family, $code = false,$path = false)
	{
		$query	=	'?success='.$family.'&code='.$code;
		
		if(empty($path)){
			wp_redirect('/'.$query);
			exit;
		}
		else {
			header('Location: '.$path.'?success='.$family.'&code='.$code);
			exit;
		}
	}
	
	public	function getThemeDirectory($val=false)
	{
		return str_replace(DS.DS,DS,ABSPATH.str_replace(get_site_url(),'',get_stylesheet_directory_uri())).$val;
	}
	
	public	static	function createRoute($page,$func=false,$args=false)
	{
		# Fetch the current path
		$curr		=	self::call()->getDataNode('_SERVER')->SCRIPT_URL;
		# Count how many dir in current page
		$currDirCnt	=	count(array_values(array_filter(explode('/',$curr))));
		# Count how many dir in destination page
		$pageDirCnt	=	count(array_values(array_filter(explode('/',$page))));
		# If the directory count doesn't match
		if($currDirCnt != $pageDirCnt) {
			# It's possible that the base dir can be forced
			if(empty($args['parent']))
				# If not set to force, stop
				return false;
		}
		# Check if there is a variable subdir
		if(strpos($page,'{') !== false) {
			# remove the empty and remainder brace
			$pgExp	=	array_map(function($v){
				return rtrim($v,'}');
			},array_filter(explode('{',$page)));
			# Re-assign base
			$currPg	=	$pgExp[0];
			# Get the regex for this path
			$regx	=	$pgExp[1];
			# Match the pattern
			preg_match('!'.$currPg.'(['.$regx.']+)!',$curr,$match);
			# Save the settings to persist
			self::call()->saveSetting('nbr_uri',['regex'=>$regx,'data'=>$match]);
		}
		# See if the base matches the destination
		if(isset($pgExp)) {
			if(preg_match('!^'.ltrim($currPg,'/').'!',ltrim($curr,'/'))) {
				$curr	=
				$page	=	$currPg;
				# Try and match page by getting title
				$path	=	get_page_by_path($currPg,ARRAY_A);
			}
		}
		else {
			# Try and match page by getting title
			$path	=	get_page_by_path($curr,ARRAY_A);
		}
		# Make base path
		$routePage	=	strtolower(trim($page,'/'));
		$onPage		=	strtolower(trim($curr,'/'));
		# If they don't match
		if($routePage != $onPage) {
			# Check if base should be allowed
			if(!empty($args['parent'])) {
				# See if the base matches
				if(!preg_match('!^'.$onPage.'/.*!',$routePage.'/'))
					return false;
			}
			else
				return false;
		}
		# This is used to force a page to use route even if exists internally
		$force	=	(!empty($args['force']));
		# If the title doesn't exist
		if(!empty($path['ID'])) {
			# If not forcing overwrite, stop
			if(!$force)
				return false;
		}
		# If there is a setting for making the title
		if(!empty($args['title'])) {
			# Modify the title with filter
			add_filter('pre_get_document_title','nbr_add_title', 10, 2);
			# Save to data for the nbr_add_title() function to use title
			self::call()->saveSetting('nbr_add_title',$args['title']);
		}
		
		$User		=	new User();
		$loggedIn	=	$User->isLoggedIn();
		
		switch($args['loggedin']){
			case('in'):
				if($loggedIn)
					break;
				else
					return false;
			case('out'):
				if(!$loggedIn)
					break;
				else
					return false;
		}
		
		# Run the functions
		if(is_string($func)) {
			if(function_exists($func)) {
				$func();
				exit;
			}
		}
		elseif(is_callable($func)) {
			$func();
			exit;
		}
	}
}