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
				$args		=	func_get_args();
				$errorCode	=	(!empty($args[0]))? $args[0] : 404;
				$errorMsg	=	(!empty($args[1]))? $args[1] : 'http/1.1 '.$errorCode.' Not Found';
					
				$page['is_admin']	=	false;
				# Reset the notification that path is bad
				NubeData::$settings->{"error".$errorCode}	=	true;
				# Create a redirect notice
				$this->saveSetting('pageURI_redirect',false,true);
				# Update the page settings so there is no error 404
				if(!isset(NubeData::$settings->site->page))
					$this->saveSetting('site',array('page'=>false));
				else
					NubeData::$settings->site->page	=	false;
				$this->saveSetting("getPageURI",array(),true);
				$this->saveSetting('pageURI',array(),true);
				$this->getHelper('GetSitePrefs')->setPageRequestSettings();
				NubeData::$settings->site->page_valid	=	false;
				http_response_code($errorCode);
				header($errorMsg);
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
						
					if(!$this->isAjaxRequest()) {
						$this->addRedirect($this->siteUrl($path));
					}
				}
				
				if($this->getPageURI('is_admin') > 2)
					$this->getHelper('nSessioner')->destroy('LOCALE');
				
			}
			
		public	function createAbbrevAppend()
			{
				return preg_replace('/[^a-zA-Z0-9\-\_]/','',strtolower(trim(str_replace(array(' ',),array('_'),$this->getRequest('country')))));
			}
		/*
		**	@description	Creates an Open SSL-encoded path for use when redirecting
		*/
		public	function createJumpPage($url)
			{
				return $this->safe()->encOpenSSL($url);
			}
		/*
		**	@description	Decrypts the createJumpPage() path. May require the urldecode for proper decoding
		*/
		public	function decodeJumpPage($url,$urldec = true)
			{
				return $this->safe()->decOpenSSL($url,array("urlencode"=>$urldec));
			}
		/*
		**	@description	Creates a redirect via ajax/javascript
		*/
		public	function doAjaxRedirect($path,$instructions = false)
			{
				# If not an ajax request, stop
				if(!$this->isAjaxRequest())
					return;
				# Create default script
				$instr	=	array(
					'html'=>array(
						'<script>window.location="'.$path.'";</script>'
					),
					'sendto'=>array(
						'body'
					)
				);
				# Include anymore html elements
				if(!empty($instructions['html'])) {
					$instr['html']	=	array_merge($instructions['html'],$instr['html']);
					unset($instructions['html']);
				}
				# Include sendto elements
				if(!empty($instructions['sendto'])) {
					$instr['sendto']	=	array_merge($instructions['sendto'],$instr['sendto']);
					unset($instructions['sendto']);
				}
				# If there are anymore elements, combine them with default
				if(is_array($instructions) && count($instructions) > 0)
					$instr	=	array_merge($instr,$instructions);
				
				$this->ajaxResponse($instr);
			}
	}