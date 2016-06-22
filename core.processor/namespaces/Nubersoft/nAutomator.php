<?php
namespace	Nubersoft;

class nAutomator
	{
		private	$actionArr,
				$listenForKey,
				$organizeBy,
				$cachedArr;
		/*
		**	@description	!!! The automator requires the addition of two method-set attributes, `listenFor()` and `organizeBy()`
		**					!!!	The `$array` should come from the parsed reg file: $this->parseRegFile($dir)
		**
		**	@Example:
		**----------------------------------------------------------------------
		$dir		=	__DIR__.'/myplugin/';
		$nFunctions	=	new Nubersoft\nFunctions();
		$configs	=	$nFunctions	->listenFor('action')
							->organizeBy('name')
							->automate($nFunctions->parseRegFile($dir));
		**----------------------------------------------------------------------
		**	@param	[array]  $array				Accepts any array
		**	@param	[string] $extractArr		Used to search above array for a matching key, then extracts array
		**	@param	[string] $organizeByName	Used to organize array by a key from inside the above extracted array
		**	@param	[string] $baseDir			Used to find included file if one available
		*/
		public	function automate($array,$baseDir = false)
			{
				$actionArr		=	(!empty($this->actionArr))? trim($this->actionArr) : false;
				
				if(empty($actionArr))
					return false;
					
				// Check for an action
				$action	=	(!empty($this->listenForKey))? $this->listenForKey : false;
				$orgKey	=	(!empty($this->organizeBy))? $this->organizeBy : false;
				$array	=	(is_array($array))? $array : false;
				
				if(empty($action) || empty($orgKey) || empty($array))
					return false;
					
				// Get the array and organize it
				$runActions		=	\nApp::nFunc()->extractArray($array,$action,$orgKey);
				$actionsAvail	=	(is_array($runActions) && !empty($runActions))? array_keys($runActions) : array();
				// Check if there is a plugin action associated with this request action
				if(in_array($actionArr,$actionsAvail)) {
					if(isset($runActions[$actionArr]['function']['name'])) {
						$thisAct				=	$runActions[$actionArr]['function'];
						$thisAct['foundin']		=	(!empty($thisAct['foundin']))? $thisAct['foundin'] : false;
						$thisAct['root_dir']	=	$baseDir;
						
						$thisOpts	=	array(	'name'=>$thisAct['name'],
												'root_dir'=>$thisAct['root_dir'],
												'foundin'=>$thisAct['foundin']);
						$type	=	'function';
					}
					elseif(isset($runActions[$actionArr]['class']['name'])) {
						$thisAct				=	$runActions[$actionArr]['class'];
						$thisAct['root_dir']	=	$baseDir;
						$thisAct['foundin']		=	(!empty($thisAct['foundin']))? $thisAct['foundin'] : false;
						
						$thisOpts	=	array(	'class'=>$thisAct['name'],
												'method'=>(!empty($thisAct['method']))? $thisAct['method'] : false,
												'root_dir'=>$thisAct['root_dir'],
												'foundin'=>$thisAct['foundin']);
						$type	=	'class';
					}
					elseif(isset($runActions[$actionArr]['include']) || isset($runActions[$actionArr]['include_once'])) {
						
						if(isset($runActions[$actionArr]['include'])) {
							$includes	=	$runActions[$actionArr]['include'];
							$incFunc	=	true;
						}
						else {
							$includes	=	$runActions[$actionArr]['include_once'];
							$incFunc	=	false;
						}
						
						if(is_array($includes)) {
							foreach($includes as $include) {
								$include	=	$this->matchFunction($include);
								if(is_file($include))
									($incFunc)? include($include) : include_once($include);
							}
						}
						else {
							$include	=	$this->matchFunction($includes);
							if(is_file($include))
								($incFunc)? include($include) : include_once($include);
						}
						
						return false;
					}
					
					if(!empty($type)) {
						$this->coreAutomate($thisAct,$thisOpts,$type);
					}
				}
			}
		
		private	function automateByClass($typeOpts,$inject = false)
			{
				$func	=	function($class,$typeOpts = false,$inject = false)
					{
						if(!class_exists($class)) {
							if(empty($typeOpts['foundin']))
								return false;
							else {
								$foundIn	=	$this->matchFunction($typeOpts['foundin']);
								if(is_file($foundIn))
									require_once($foundIn);
							}	
						}
						
						$initClass	=	new $class();
						
						if(!empty($initClass) && !empty($typeOpts['method'])) {
							$method	=	$typeOpts['method'];
							if(!empty($inject))
								$initClass->{$method}($inject);
							else
								$initClass->{$method}();
						}	
					};
				
				$class	=	(!empty($typeOpts['name']))? $typeOpts['name'] : false;
				
				if(empty($class))
					return false;
				
				$func($class,$typeOpts,$inject);
			}
		
		private	function automateByFunction($typeOpts,$inject = false)
			{
				$func	=	(!empty($typeOpts['name']))? $typeOpts['name'] : false;
				
				if(empty($func))
					return false;
				
				if(!function_exists($func)) {
					$foundIn	=	$this->matchFunction($typeOpts['foundin']);
					if(!is_file($foundIn))
						return false;
					else
						require_once($foundIn);
				}
				
				if(!empty($inject))
					$func($inject);
				else
					$func();
			}
		
		private	function coreAutomate($thisAct,$typeOpts,$useType = 'function')
			{
				$inject	=	false;
				
				if(!empty($thisAct['inject'])) {
					if(!empty($thisAct['inject']) && is_array($thisAct['inject'])) {
						foreach($thisAct['inject'] as $useMethod => $methodVal) {
							
							switch($useMethod) {
								case('post'):
									$REQUEST	=	\nApp::getPost();
									break;
								case('get'):
									$REQUEST	=	\nApp::getGet();
									break;
								case('request'):
									$REQUEST	=	\nApp::getRequest();
									break;
							}
							if(empty($REQUEST))
								continue;
							
							if(is_array($methodVal)) {
								foreach($methodVal as $postKey) {
									$inject[$postKey]	=	(!empty($REQUEST->{$postKey}))? $REQUEST->{$postKey} : false;
								}
							}
							else {
								$postKey			=	$methodVal;
								$inject[$postKey]	=	(!empty($REQUEST->{$postKey}))? $REQUEST->{$postKey} : false;
							}
						}
					}
				}
				
				if($useType == 'function')
					$this->automateByFunction($typeOpts,$inject);
				elseif($useType == 'class')
					$this->automateByClass($typeOpts,$inject);
			}
			
		public	function matchFunction($str)
			{
				if(!is_string($str))
					return $str;
					
				if(strpos($str,'~') !== false) {
					return preg_replace_callback('/\~([\w]{1,})\~/',function($v){
							if(!empty($v[1])) {
								$func	=	$v[1];
								if(function_exists($func))
									return $func();
								elseif(defined($func))
									return constant($func);
								else
									return $func;
							}
						},$str);
				}
				
				return $str;
			}
			
		public	function listenFor($action = false,$method = 'post')
			{
				if(empty($action))
					return $this;
				
				$this->listenForKey	=	$action;
				
				switch($method) {
					case('get'):
						$this->actionArr	=	\nApp::getGet($action);
						break;
					case('request'):
						$this->actionArr	=	\nApp::getRequest($action);
						break;
					default:
						$this->actionArr	=	\nApp::getPost($action);
				}
				
				return $this;
			}
		
		public	function organizeBy($key = false)
			{
				$this->organizeBy	=	$key;
				return $this;
			}
		
		public	function observer($settings = false)
			{
				$action		=	(!empty($settings['action_trigger']))? $settings['action_trigger'] : 'action';
				$request	=	(!empty($settings['request_type']))? $settings['request_type'] : 'request';
				$name		=	(!empty($settings['organize_by']))? $settings['organize_by'] : 'name';
				$configs	=	\nApp::getConfigs();
				
				$this	->listenFor($action,$request)
						->organizeBy($name)
						->automate($configs);
			}
		/*
		**	@description	This method runs through a one dimensional array with settings per sub array
		**					An example workflow array would be
		Array(
			[object] => Array(
				[0] => Array(
					[class] => Array(
						[name] => \Nubersoft\nReWriter
						[method] => validate
					)
				)
				[1] => Array(
					[function] => Array(
						[name] => printpre
						[echo] => true
						[inject] => validate
					)
				)
			)
		)
		**	
		*/
		public	function doWorkFlow($workflow = false)
			{
				if(empty($workflow))
					return false;
					
				if(is_array($workflow)) {
					foreach($workflow as $doAction) {
						$method	=	false;
						$func	=	false;
						if(isset($doAction['function'])) {
							try {
								$funcArr	=	$doAction['function'];
								$func		=	$funcArr['name'];
								if(!function_exists($func)) {
									if(isset($funcArr['include'])) {
										$inc	=	$this->matchFunction($funcArr['include']);
										require_once(NBR_ROOT_DIR.$inc);
									}
									if(!function_exists($func))
										throw new Exception("Function ({$func}) does not exist. Use an include line: <include>/path/to/function.php</include>");
								}
								
								if(isset($funcArr['inject'])) {
									$inject		=	$funcArr['inject'];
									$echoFunc	=	$func(((is_string($inject)? $inject : $this->doWorkflow($inject))));
								}
								else
									$echoFunc	=	$func();
								
								if(isset($funcArr['echo']) && $funcArr['echo'] == 'true')
									echo $echoFunc;
								elseif(isset($funcArr['return']) && $funcArr['return'] == 'true')
									return $echoFunc;
							}
							catch(\Exception $e) {
								if(is_admin())
									die($e->getMessage());
							}
						}
						elseif(isset($doAction['class'])) {
							$obj	=	$doAction['class'];
							$func	=	$obj['name'];
							
							if($func == 'this') {
								if(!empty($obj['method'])) {
									$method	=	$obj['method'];

									if(isset($obj['inject']))
										$this->{$method}($obj['inject']);
									else
										$this->{$method}();
										
									if(!method_exists($this,$method))
										throw new \Exception("Method ({$method}) does not exist.");
								}
							}
							else {
								try {
									if(!class_exists($func)) {
										if(isset($obj['include'])) {
											if(!include_once(NBR_ROOT_DIR.$obj['include'])) {
												throw new \Exception('Support file not found for :'.$func);
											}
										}
									}
										
									if(isset($obj['method'])) {
										$method	=	$obj['method'];
									}
									
									if($method)
										call_user_func(array($func,$method));
									else
										call_user_func($func);
								}
								catch (\Exception $e) {
									die($e->getMessage());
								}
							}
						}
						elseif(isset($doAction['include'])) {
						}
						elseif(isset($doAction['include_once'])) {
						}
						elseif(isset($doAction['require'])) {
						}
						elseif(isset($doAction['require_once'])) {
						}
					}
				}
			}
		/*
		**	@description	This method listens for actions and executes scripts based on instructions
		**					provided by various config xml files strewn about
		**	@param	$settings	[array]	This array tells the method where xml files are.
		**								This also indicates what key word to listen for via input fields
		**								and lastly, will organize the data by the name of the found fields
		*/
		public	function makeListener($settings)
			{
				$dir		=	(!empty($settings['dir']))? $settings['dir'] : NBR_CLIENT_DIR;
				$listener	=	(!empty($settings['listen_for']))? $settings['listen_for'] : 'action';
				$organize	=	(!empty($settings['organize_by']))? $settings['organize_by'] : 'name';
				// Get all the config files
				$configs	=	\nApp::nRegister()->parseRegFile($dir);
				// Convert them to an array
				$configs	=	\nApp::nFunc()->toArray($configs);
				// Run the automator
				$this->listenFor($listener)
					->organizeBy($organize)
					->automate($configs);
			}
		/*
		**	@description	This method is a listener for file uploads
		**	@param	$settings	[array]	Contains options for the nUploader to work
		*/
		public	function makeFileListener($settings = false)
			{
				// Name of the input (default is "file")
				$inputName	=	(!empty($settings['name']))? $settings['name'] : 'file';
				// Set the file types (derived from `file_types` table if left empty)
				$files		=	(!empty($settings['types']) && is_array($settings['types']))? $settings['types'] : \nApp::getFileTypes();
				// Get move options which include "overwrite" (unlinks file if exists), "unique" (uses the unique name of the file instead of real name)
				$move		=	(!empty($settings['move_opts']) && is_array($settings['move_opts']))? $settings['move_opts'] : array('overwrite'=>true);
				// Add htaccess to folder
				$protect	=	(!empty($settings['protect']));
				// Save folder
				$dir		=	(!empty($settings['dir']))? $settings['dir'] : NBR_CLIENT_DIR._DS_.'images'._DS_.'default'._DS_;
				// Add a callback
				$callback	=	(!empty($settings['callback']))? $settings['callback'] : false;
				// Start uploader
				$nFiles		=	new nUploader($inputName);
				try{
					// Set the destination folder
					$nFiles	->setDestination($dir,array('protect'=>$protect))
							// Adds allowable ext
							->allowFileTypes($files)
							->setMoveAttr($move)
							->setObserver();
							// See if there are anymore pre-processing
							if(!empty($callback))
								$nFiles->setCallback($callback);
							// Upload the files remaining
							$nFiles->upload();
					// Record file transaction
					$nFiles->recordTransaction();
					// Send success
					if(!empty($nFiles->getData()))
						\nApp::saveError('file_listener',array('success'=>true));
					// Send back the array
					return	$nFiles->getData();
				}
				catch(Exception $e) {
					if(is_admin()) {
						// Just die and display error
						die($e->getMessage());
					}
					// Save to error array
					\nApp::saveError('file_upload',array('success'=>false,'msg'=>'upload failed'));
					// Save to log file
					\nApp::saveToLogFile('error_uploads.txt',$e->getMessage());
					// Set 
					return false;
				}
			}
		/*
		**	@description	This method will fetch cached preferences. The array will contain all the elements
		**					saved from parsed files as the page loads for the first time.
		**					Just delete cache to reset the prefs
		*/
		public	function getCachedConfigs()
			{
				$this->cachedArr	=	false;
				// Get the cache directory
				$dir	=	\nApp::getDataNode('site')->cache_folder;
				// If the cache config file is available, parse and return
				if(is_file($prefs = $dir.'configs.json'))
					$this->cachedArr	=	array(
												'configs'=>json_decode(file_get_contents($prefs)),
												'xml_add_list'=>json_decode(file_get_contents($dir.'xml_add_list.json'))
											);
				return $this;
			}
		/*
		**	@description	This is the companion function to getCachedConfigs(). When loaded at the bottom of the
		**					workflow, will capture all the loaded configs and save the configs to a json file
		**					for recall at the beginning of the page load
		*/
		public	function saveCachedConfigs()
			{
				$dir		=	_DS_.trim(\nApp::getDataNode('site')->cache_folder,_DS_)._DS_;
				$filename	=	$dir.'configs.json';
				$xmlList	=	$dir.'xml_add_list.json';
				if(is_file($filename))
					return;
				
				$configs		=	\nApp::getConfigs();
				$xml_add_list	=	\nApp::getDataNode('xml_add_list');
				if(!empty($configs)) {
					if(\nApp::nFunc()->isDir($dir,true,0755)) {
						\nApp::nFunc()->autoload('CreateHTACCESS',NBR_FUNCTIONS);
						CreateHTACCESS(array('dir'=>$dir));
						file_put_contents($filename,json_encode($configs));
						file_put_contents($xmlList,json_encode($xml_add_list));
					}
				}
			}
		
		public	function toPrefs()
			{
				$configs	=	$this->cachedArr;
				if(!empty($configs['configs'])) {
					\nApp::saveSetting('config',$configs['configs']);
					\nApp::saveSetting('xml_add_list',$configs['xml_add_list']);
				}
			}
	}