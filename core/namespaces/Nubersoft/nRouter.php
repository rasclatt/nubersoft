<?php
namespace Nubersoft;

class nRouter extends \Nubersoft\nApp
	{
		protected	$data;
		
		public	function addHeaderCode($code,$message = false,$thow = false)
			{
				if(!empty($message)) {
					$this->saveIncidental('headers',array($code=>$message));
				}
				
				http_response_code($code);
				
				if(!empty($thow) && !empty($message)) {
					throw new \Exception($message);
				}
				
				return $this;
			}
		/*
		**	@description	Adds a header and records it to settings
		*/
		public	function addHeader()
			{
				# Check if anthing is added
				$args		=	func_get_args();
				# Assign if available
				if(!empty($args[0]))
					$headers	=	(is_array($args[0]))? $args[0] : array($args[0]);
				# Stop if no header is set
				if(empty($headers)) {
					trigger_error('You are attemptying to add an empty header.',E_USER_NOTICE);
					return false;
				}
				# Loop through headers and add
				foreach($headers as $insert) {
					header($insert);
				}
				# Save all the headers to node
				$this->saveSetting('site',array('headers'=>$headers));

				return $this;
			}
		
		public	function addRedirect($to)
			{
				//die(printpre($this->getSession('workflow_run')));
				header('Location: '.$to);
				exit;
			}
			
		public	function stripIndex($link)
			{
				return str_replace("index.php","",$link);
			}
		
		public	function resetPageRouting($page)
			{
				$page['is_admin']	=	(isset($page['is_admin']))? $this->getBoolVal($page['is_admin']) : false;
				# Reset the notification that path is bad
				NubeData::$settings->error404	=	false;
				# Create a redirect notice
				$this->saveSetting('pageURI_redirect',$page['include']);
				# Update the page settings so there is no error 404
				NubeData::$settings->site->page	=	$page;
				$this->saveSetting("getPageURI{$page['full_path']}",$page,true);
				$this->saveSetting('pageURI',$page,true);
				$this->getHelper('GetSitePrefs')->setPageRequestSettings();
				NubeData::$settings->site->page_valid	=	true;
				http_response_code(200);
				header('http/1.1 200 OK');
			}
		
		public	function toErrorPage()
			{
				$page['is_admin']	=	false;
				# Reset the notification that path is bad
				NubeData::$settings->error404	=	true;
				# Create a redirect notice
				$this->saveSetting('pageURI_redirect',false,true);
				# Update the page settings so there is no error 404
				NubeData::$settings->site->page	=	false;
				$this->saveSetting("getPageURI",array(),true);
				$this->saveSetting('pageURI',array(),true);
				$this->getHelper('GetSitePrefs')->setPageRequestSettings();
				NubeData::$settings->site->page_valid	=	false;
				http_response_code(404);
				header('http/1.1 404 Not Found');
			}
		
		private	function checkIsResetCo()
			{
				return ($this->getRequest('action') == 'nbr_change_country');
			}
		
		public	function appendAndRedirect()
			{
				$redirect	=	(!empty($this->getDataNode('_SERVER')->REDIRECT_URL))? $this->getDataNode('_SERVER')->REDIRECT_URL : false;
				$cou		=	'USA';
				
				if($this->checkIsResetCo()) {
					$this->setSession('LOCALE',((empty($this->getRequest('country')))? $cou : $this->getRequest('country')),true);
					$path	=	$this->getPageURI('full_path');
					if($path == '/home/')
						$path	=	'/';
						
					if(!$this->isAjaxRequest())
						$this->addRedirect($this->siteUrl($path));
				}
				
				if($this->getPageURI('is_admin') > 2)
					$this->getHelper('nSessioner')->destroy('LOCALE');
				/*
				if(empty($this->getPageURI('invalid_uri')) && !$this->checkIsResetCo()) {
					if(!$this->isAjaxRequest())
						$this->nSession()->destroy('LOCALE');
					return;
				}
				
				if(!$this->checkIsResetCo()) {
					if(!empty($redirect)) {
						$filter	=	$this->getPrefFile(
							'countries',
							array(
								'xml' => NBR_CLIENT_SETTINGS,
								'save' => true
							),
							false,
							function($path,$nApp) {
								return array_keys($nApp
									->organizeByKey($nApp
										->getHelper('nRegister')
										->parseXmlFile($path)['country'],'abbr'
									)
								);
						});
						
						$exp	=	array_values(array_filter(explode('/',$redirect)));
						$dir	=	array_shift($exp);
						
						if(!in_array(strtoupper(trim($dir,'/')),$filter))
							return;
						
						$path	=	(!empty($exp))? str_replace('//','/','/'.implode('/',$exp).'/') : '/';
						
						$this->getHelper('nGet')->getPageURI($path,$path);
						if(!empty($page['invalid_id'])) {
							if(strpos($redirect,$this->getSession('LOCALE').'/')!== false) {
								$path	=	preg_replace('!^'.$this->getSession('LOCALE').'!','',$redirect);
								$this->getHelper('nGet')->getPageURI($path,$path);
							}
						}
						else {
							if(empty($this->getSession('LOCALE'))) {
								$this->setSession('LOCALE','/'.$dir,true);
							}
						}
					}
				}
				else {
					$append	=	$this->createAbbrevAppend();
					if($append == 'usa') {
						if($this->getSession('LOCALE',true)) {
							$this->addRedirect($this->siteUrl());
						}
					}
					else {
						$this->setSession('LOCALE','/'.$append,true);
						$this->addRedirect($this->localeUrl());
					}
				}
				*/
			}
			
		public	function createAbbrevAppend()
			{
				return preg_replace('/[^a-zA-Z0-9\-\_]/','',strtolower(trim(str_replace(array(' ',),array('_'),$this->getRequest('country')))));
			}
	}