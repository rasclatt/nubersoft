<?php
namespace Nubersoft;

class	nRequester
	{
		private	$autoloader;
		
		public	function __construct(\Nubersoft\nConfigEngine $nConfigEngine)
			{
				$this->autoloader	=	$nConfigEngine->getAllConfigs();
			}
		
		public	function execute()
			{
				// run autoloaded xml-based functions
				$this->autoLoad('header');
				// Run the header processes
				$this->runHeadProcessor(new \HeadProcessor());
				// Get the onload preference
				$onload	=	\nApp::getDataNode('onload');
				
				if(!empty($onload))
					$this->processOnLoad(\nApp::nFunc()->toArray(($onload)));
				
				if(\nApp::getPost('update') && \nApp::getPost('requestTable')) {
					if(\nApp::getPost()->requestTable !== 'system_settings')
						return;
		
					header("Location: ".$_SERVER['HTTP_REFERER']);
					exit;
				}
			}
		
		protected	function autoLoad($type = 'header')
			{
				$types[]	=	'post';
				$types[]	=	'get';
				$types[]	=	'request';
				
				if(isset($this->autoloader[$type])) {
					for($i = 0; $i < 3; $i++) {
						if(empty($this->autoloader[$type][$types[$i]]))
							continue;

						$array	=	$this->autoloader[$type][$types[$i]];
						$count	=	count($array);
						
						for($a = 0; $a < $count; $a++) {
							$func	=	false;
							$inc	=	false;
							if(empty($array[$a]['action']))
								$inc	=	true;
							else {
								switch($types[$i]) {
									case('post'):
										if(nApp::getPost('action') == $array[$a]['action']) {
											$inc	=	true;
											break;
										}
									case('request'):
										if(nApp::getRequest('action') == $array[$a]['action']) {
											$inc	=	true;
											break;
										}
									case('get'):
										if(nApp::getGet('action') == $array[$a]['action']) {
											$inc	=	true;
											break;
										}
								}
							}
							
							if($inc) {
								$func	=	preg_replace('/(function|class).(.*)/', "$2",pathinfo($array[$a]['include'],PATHINFO_FILENAME));
								if(!function_exists($func)) {
									if(is_file(NBR_ROOT_DIR.$array[$a]['include']))
										include(NBR_ROOT_DIR.$array[$a]['include']);
									else
										RegistryEngine::saveError('nXmLoader',"BAD PATH: ".$array[$a]['include']);
								}
									
								if(function_exists($func))
									$func();
								else
									RegistryEngine::saveError('nXmLoader',"FUNCTION INACCESSABLE: {$func}()");
							}
						}
					}
				}
			}
		
		protected	function processOnLoad($array = false)
			{
				// If not an array just skip
				if(!is_array($array))
					return false;
				// Make sure there is a header to load up
				if(!isset($array[0]['header']))
					return false;
				// Loop through each header action
				foreach($array as $cArr) {
					// Assign header
					$header		=	$cArr['header'];
					// If there is no including file, skip
					if(empty($header['use']))
						continue;
					// Make some settings
					$requester	=	(!empty($header['requester']))? $header['requester'] : 'post';
					$action		=	(is_file(NBR_ROOT_DIR.$header['action']))? NBR_ROOT_DIR.$header['action'] : false;
					$use		=	(is_file(NBR_ROOT_DIR.$header['use']))? NBR_ROOT_DIR.$header['use'] : false;
					// If the include file doesn't exist, skip
					if(!$use)
						return false;
					// If there is no matching that needst to happen,
					// just include the file
					if(!$action) {
						include($use);
					}
					// If there is a post,get,request that needs to match
					// assign it here
					else {
						switch($requester) {
							case('get'):
								$method	=	Safe::to_array(nApp::getGet());
								break;
							case('request'):
								$method	=	Safe::to_array(nApp::getRequest());
								break;
							default:
								$method	=	Safe::to_array(nApp::getPost());
						}
						// If there is a matching key, include the file
						if(isset($method[$action]))
							include($use);
					}
				}
			}
		
		private	function runHeadProcessor(\HeadProcessor $header)
			{
				// Process any actions
				$header->Process();
				// Process login action
				$header->Login();
				// Reset the engine
				$reset			=	(!empty($header->reset))? array("reset"=>$header->reset,"data"=>$header->data) : array("reset"=>false,"data"=>array());
				$array			=	\nApp::nFunc()->toArray(\nApp::getDataNode('engine'));
				$array['reset']	=	$reset;
				\nApp::saveSetting('engine',$array);
			}
	}