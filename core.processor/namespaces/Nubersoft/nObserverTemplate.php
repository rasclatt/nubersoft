<?php
namespace Nubersoft;

class nObserverTemplate implements nObserver
	{
		public	static	function listen()
			{
				\nApp::nFunc()->autoload('is_admin',NBR_FUNCTIONS);
				
				$page	=	\nApp::getPage();
				$site	=	\nApp::getSite();
				
				if(empty($page->unique_id)) {
					$admin		=	false;
					$template	=	false;
				}
				// Set admintools template
				elseif(is_admin() && !\nApp::siteValid()) {
					$template	=	false;
					$admin		=	true;
				}
				elseif(!isset($page->template)) {
					$template	=	false;
					$admin		=	\nApp::siteValid();
				}
				elseif(isset($page->is_admin) && $page->is_admin == 1) {
					$admin		=	true;
					$template	=	(!empty($page->template))? NBR_ROOT_DIR._DS_.$page->template : false;
				}
				else {
					$admin		=	false;
					$template	=	(isset($page->template))? NBR_ROOT_DIR._DS_.$page->template : false;
				}
				// If the page is admin, check if there is an associated whitelist
				if($admin){
					if(!\nApp::onWhiteList($_SERVER['REMOTE_ADDR'])) {
						\nApp::nFunc()->autoload('nbr_fetch_error',NBR_FUNCTIONS);
						// create a log file
						$nLogger	=	new \Nubersoft\nLogger();
						$nLogger->ErrorLogs_WhiteList(nbr_fetch_error('whitelist',__FILE__,__LINE__));
					}
				}
				// Send 404 error
				if(empty($page->unique_id))
					header('http/1.1 404 not found');
					
				\nApp::nFunc()->autoload('get_template',NBR_FUNCTIONS);
				// Return a template directory
				if(!empty($site->template)) {
					$site->template	=	get_template($template,$admin);
				}
				else
					\nApp::saveSetting(array("use"=>"site","data"=>array("template"=>get_template($template,$admin))));
			}
		/*
		**	@description	This method is used to output the final template to the browser
		**
		*/
		public	static	function toBrowser()
			{
				// Get the page id from the page prefs
				$unique_id	=	\nApp::getPage('unique_id');
				// Sets te unique_id back into the page_prefs array
				\Nubersoft\nSet::saveToPage('unique_id', ((!empty($unique_id))? $unique_id : false));
				// Grab the proper template
				$def		=	\nApp::getSite('template');
				// If the default is equal to the default template, just assign the default
				if($def == 'default'._DS_.'template')
					$def	=	'default'._DS_.'include.php';
				// Make the final path link to the template
				$template	=	NBR_ROOT_DIR._DS_.'core.processor'._DS_.'template'._DS_.$def;
				// First check that the path to a file is valid and is default
				if(is_file($def))
					$template	=	$def;
				// Include the appropriate template file
				include($template);
			}
	}