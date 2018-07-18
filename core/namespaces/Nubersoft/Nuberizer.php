<?php
namespace Nubersoft;

class Nuberizer extends \Nubersoft\nApp
{
	protected	$autoloader,
				$head,
				$mast,
				$menu,
				$login,
				$body,
				$foot,
				$page,
				$login_valid,
				$page_view,
				$timezone,
				$TimeZone,
				$layout,
				$error,
				$nAutomator,
				$nApp,
				$nRegister;

	public	function core(\Nubersoft\nAutomator $nAutomator)
	{
		$this->nAutomator	=	$nAutomator;
		$order[]	=	'blockflow/header';
		$order[]	=	'workflow/default';
		# Apply site-live
		if(!$this->isAdmin()) {
			if(!$this->siteLive() && !$this->isAdminPage()) {
				/*
				** This will overwrite the site live message with the not found message
				** When javascript tries to pull a page silently that doesn't exist, if a fetch_token()
				** is being called, it will reset that token by reloading the page.
				** In this case, we want to show the not-found page rather than the site offline page.
				*/
				$order[]	=	'workflow/offline';
			}
			else
				$order[]	=	'workflow/template';
		}
		else
			$order[]	=	'workflow/template';
		# Run through all the flows
		foreach($order as $flow) {
			# Add session observer
			$this->nAutomator
				->setListenerName('action')
				->getInstructions($flow);
		}
	}

	private	function runDefaultLoad()
	{
		# Caught in a loop if admin is not checked first
		if($this->isAdmin())
			return;
		# Connection is set
		$dbValid	=	$this->getConStatus();
		# If there is a valid database connection
		if($dbValid) {
			$User	=	$this->getHelper('User');
			# See if there are users in the database
			$users	=	$User->systemHasUsers();
			# If there are no users AND there is an admin page in the menus
			if($users['count'] == 0 && $this->getAdminPage()) {
				# Create a default admin user
				$User->loginUser(array('username'=>'guest','usergroup'=>NBR_ADMIN));
				# Set the redirect
				$adminPage	=	$this->siteUrl($this->getAdminPage('full_path'));
				# Redirect
				$$this->getHelper('nRouter')->addRedirect($adminPage);
			}
		}
		else
			die('You need to install/reinstall database.');
	}
}