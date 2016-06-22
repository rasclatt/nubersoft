<?php
namespace Nubersoft;

class nRegister
	{
		/*
		** @description - Load registry file into xml object (if possible)
		*/
		private	function getXML($get = false,$name = false)
			{
				if(defined('NBR_ROOT_DIR') && !empty($name))
					$name	=	str_replace(NBR_ROOT_DIR,'',$name);
				\nApp::nFunc()->autoload('printpre',NBR_FUNCTIONS);
				$addName	=	$this->regParsedLocation($name);//echo '<br />'.printpre($addName);
				
				if($addName->parsed) {
					$pList	=	\nApp::nFunc()->toArray(\nApp::getDataNode('xml_add_list'));
					$key	=	array_search($addName->file_path,$pList);
					$parsed	=	\nApp::nFunc()->toArray(\nApp::getDataNode('configs'));
					if(!empty($parsed[$key]))
						return $parsed[$key];
				}
				
				$lName		=	$addName->file_path;
				$name		=	(empty($name))? '' : " (found: `{$name}`)";
				$get		=	trim($get);
				
				if(!$xml = @simplexml_load_string($get)) {
					throw new \Exception("There was an error processing xml contents{$name}. Check your xml carefully!");
				}
				// Save to list
				\nApp::saveSetting('xml_add_list',array($lName));
				// Save config to setting
				\nApp::saveSetting('configs',array($xml));
				// return xml
				return (!empty($xml))? $xml : false;
			}
		
		public	function regParsedLocation($filename)
			{
				$parsed		=	false;
				// Get the root version of the reg file
				$added		=	strtolower(str_replace(array(NBR_ROOT_DIR,DIRECTORY_SEPARATOR),'',$filename));
				$added		=	trim($added,'.xml');
				// Get the list of already added regs
				$addList	=	\nApp::nFunc()->toArray(\nApp::getDataNode('xml_add_list'));
				if(!empty($addList)) {
					if(in_array($added,$addList))
						$parsed	=	true;
				}
				
				return (object) array('parsed'=>$parsed,'file_path'=>$added);;
			}
		
		public	function getRegFile($filename = false)
			{
				$filename	=	(!empty($filename) && is_file($filename))? $filename : false;
				
				if(empty($filename))
					return false;
				
				// Get the root version of the reg file
				$added		=	$this->regParsedLocation($filename);

				try {
					// Try and get contents of file
					$get		=	@file_get_contents($filename); 
					// Try to process it with xml processor
					$data		=	$this->getXML($get,$filename);
				} catch (\Exception $e) {
					\nApp::nFunc()->autoload('is_admin',NBR_FUNCTIONS);
					if(is_admin())
						\nApp::saveError('xml_processor',array('success'=>false,'message'=>$e->getMessage()));
				}
				// If data is not empty, convert to an array
				return (!empty($data))? \nApp::nFunc()->toArray($data) : false;
			}
		
		public	function parseRegFile($dir = false,$check = false)
			{
				if(!is_dir($dir))
					return false;
				
				// Fetch options
				$configs	=	\nApp::nFunc()->getDirList(array('dir'=>$dir,'type'=>array('xml')));
					
				if(!empty($configs['list'])) {
					
					if($check)
						return true;
						
					foreach($configs['list'] as $includes) {
						// Check if there is already registered value associated with the parse
						$xmlFile	=	$this->regParsedLocation($includes);
						// If it's already been parsed
						if($xmlFile->parsed) {
							// Get key of the parsed array
							$sKey	=	array_search($xmlFile->file_path, \nApp::nFunc()->toArray(\nApp::getDataNode('xml_add_list')));
							// If it exists
							if($sKey !== false) {
								// Get the key
								$getConfigs	=	\nApp::getDataNode('configs');
								// Set the key, then continue on
								if(isset($getConfigs->{$sKey}))
									$con[$sKey]	=	$getConfigs->{$sKey};
	
								continue;
							}
						}
						else {
							$parseReg	=	$this->getRegFile($includes);
							
							if(empty($parseReg))
								continue;
							
							$con[]	=	$parseReg;
						}
					}
					
					return (!empty($con))? $con : array();
				}
			}
	}