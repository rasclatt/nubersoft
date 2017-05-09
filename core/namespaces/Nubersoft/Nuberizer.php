<?php
namespace Nubersoft;

class Nuberizer extends \Nubersoft\nFunctions
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
				$this->nApp			=	$this->nAutomator->getApp();
				$order[]	=	'blockflow/header';
				$order[]	=	'workflow/default';
				# Apply site-live
				if(!$this->nApp->isAdmin()) {
					if(!$this->nApp->siteLive() && !$this->nApp->isAdminPage()) {
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
				if(nApp::call()->isAdmin())
					return;
				$dbValid	=	$this->nApp->getConStatus();
				$iFile		=	NBR_ROOT_DIR.DS.'installer'.DS.'index.php';
				$iLink		=	$this->nApp->siteUrl(str_replace('//','/','installer/index.php'));
				# If there is a valid database connection
				if($dbValid) {
					# See if there are users in the database
					$users	=	$this->nApp->nQuery()
									->query("select COUNT(*) as count from users")
									->getResults(true);
					# If there are no users AND there is an admin page in the menus
					if($users['count'] == 0 && $this->nApp->getAdminPage()) {
						# Create a default admin user
						$this->nApp->getHelper('UserEngine')
								->loginUser(array('username'=>'guest','usergroup'=>NBR_ADMIN));
						# Set the redirect
						$adminPage	=	$this->nApp->siteUrl($this->nApp->getAdminPage('full_path'));
						# Redirect
						$this->nApp->getHelper('nRouter')->addRedirect($adminPage);
					}
				}
				else
					die('You need to install/reinstall database.');
			}
	}