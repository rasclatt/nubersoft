<?php
namespace Nubersoft;

class nAutomator extends \Nubersoft\Singleton
	{
		const	START_RECORDING	=	true;
		
		private	$nApp,
				$actionArr,
				$listenForKey,
				$organizeBy,
				$cachedArr,
				$action,
				$orderBy,
				$position;
		
		private	static	$configs	=	array();
		
		protected	$combinedArr;
		
		public	function __construct(\Nubersoft\nApp $nApp)
			{
				date_default_timezone_set('America/Los_Angeles');
				$this->nApp	=	$nApp;
				return parent::__construct();
			}
		
		public	function getApp()
			{
				return $this->nApp;
			}
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
					
				# Check for an action
				$action	=	(!empty($this->listenForKey))? $this->listenForKey : false;
				$orgKey	=	(!empty($this->organizeBy))? $this->organizeBy : false;
				$array	=	(is_array($array))? $array : false;
				if(empty($action) || empty($orgKey) || empty($array))
					return false;
				
				# Get the array and organize it
				$runActions		=	$this->extractArray($array,$action,$orgKey); 
				$actionsAvail	=	(is_array($runActions) && !empty($runActions))? array_keys($runActions) : array();
				
				# Check if there is a plugin action associated with this request action
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
		
		public	function getTimeCastLast()
			{
				$time	=	$this->nApp->toArray($this->nApp->getDataNode('workflow_runtime'));
				if(empty($time))
					return $this->getMicroTimeNow();
				
				$time	=	array_reverse($time);
				
				return $time[0]['now'];
			}
		
		public	function getTimeCastSum($time)
			{
				$totals	=	array_map(function($v){
					return $v['time'];
				},$time);
				
				return array_sum($totals);
			}
		
		public	function getTimeCast($then,$app)
			{
				$time	=	$this->nApp->toArray($this->nApp->getDataNode('workflow_runtime'));	
				if(empty($time))
					$time	=	array();
				
				$now	=	$this->getMicroTimeNow();
				
				$time[]	=	array(
								'time'=>number_format(($now-$then),6),
								'now'=>$now,
								'then'=>$then,
								'total'=>number_format($this->getTimeCastSum($time),6),
								'app'=>$app
							);
				
				$this->nApp->saveSetting('workflow_runtime',$time,true);
				$this->nApp->saveSetting('workflow_runtime_total',$this->getTimeCastSum($time),true);
			}
		
		public	function getMicroTimeNow()
			{
				list($usec, $sec) = explode(" ", microtime());
				return ((float)$usec + (float)$sec);
			}
		
		private	function isActiveRecording()
			{
				return	self::START_RECORDING;
			}
		
		private	function automateByClass($typeOpts,$inject = false)
			{
				if($this->isActiveRecording()) {
					$rec	=	array_filter(array('*********'.__FUNCTION__.'*********',$typeOpts,$inject));
					$this->getTimeCast($this->getTimeCastLast(),$rec);
					$this->nApp->saveSetting('workflow_run',$rec);
				}
				
				$func	=	function($class,$typeOpts = false,$inject = false)
					{
						if(!class_exists($class)) {
							if(empty($typeOpts['foundin']))
								return false;
							else {
								echo
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
				
				$class	=	(!empty($typeOpts['class']))? $typeOpts['class'] : false;
				
				if(empty($class))
					return false;
				
				$func($class,$typeOpts,$inject);
			}
		
		private	function automateByFunction($typeOpts,$inject = false)
			{
				if($this->isActiveRecording()) {
					$rec	=	array_filter(array('*********'.__FUNCTION__.'*********',$typeOpts,$inject));
					$this->getTimeCast($this->getTimeCastLast(),$rec);
					$this->nApp->saveSetting('workflow_run',$rec);
				}
				
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
									$REQUEST	=	$this->getPost();
									break;
								case('get'):
									$REQUEST	=	$this->getGet();
									break;
								case('request'):
									$REQUEST	=	$this->getRequest();
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
		/*
		**	@description			Takes a string and populates it
		**	@param $str	[string]	Takes a string like "~NBR_ROOT_DIR~/file/path/here.php" and turns it to:
		**							"/var/www/vhosts/website/httpdocs/file/path/here.php"
		*/
		public	function matchFunction($str)
			{
				
				if(is_string($str)) {
					$str	=	trim($str);
					
					if(strpos($str,'~') !== false) {
						return preg_replace_callback('/\~([^\~]{1,})\~/',function($v){
								if(!empty($v[1])) {
									$value	=	$v[1];
									if(strpos($value,'[') !== false) {
										$arr	=	array_filter(array_map(function($v){
												return trim($v,']');
											},explode('[',$value)));
											
										foreach($arr as $type) {
											$new['object']['@attributes']['event']	=	'runtime';
											if(strpos($type,'CLASS::') !== false)
												$new['object']['class']['name']	=	str_replace('CLASS::','',$type);
											elseif(strpos($type,'METHOD::') !== false)
												$new['object']['class']['method']	=	str_replace('METHOD::','',$type);
										}
										
										if(!empty($new)) {
											$new['object']['class']['return']	=	true;
											return $this->doWorkFlow($new);
										}
									}
									else {
										if(function_exists($value))
											return $value();
										elseif(defined($value))
											return constant($value);
										else
											# This should return with tags because it may get called later
											# and won't be recognized anymore if stripped of the atildes
											return '~'.$value.'~';//return trim($value,'~');
									}
								}
							},$str);
					}
				}
				
				return $str;
			}
			
		public	function listenFor($action = false,$method = 'post')
			{
				if(empty($action))
					return $this;
				
				if(is_string($action)) {
					
					$this->listenForKey	=	$action;
					
					switch($method) {
						case('get'):
							$this->actionArr	=	$this->nApp->getGet($action);
							break;
						case('request'):
							$this->actionArr	=	$this->nApp->getRequest($action);
							break;
						default:
							$this->actionArr	=	$this->nApp->getPost($action);
					}
				
					return $this;
				}
				else {
					foreach($action as $name) {
						if($method == 'get')
							$this->actionArr	=	$this->nApp->getGet($name);
						elseif($method == 'request')
							$this->actionArr	=	$this->nApp->getRequest($name);
						else
							$this->actionArr	=	$this->nApp->getPost($name);
					
						if(!empty($this->actionArr)) {
							$this->listenForKey	=	$name;
							return $this;
						}
					}
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
				$this	->listenFor($action,$request)
						->organizeBy($name)
						->automate($this->nApp->getConfigs());
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
						if(isset($doAction['object'])) {
							$this->doWorkFlow($doAction['object']);
							continue;
						}
						
						if(isset($doAction['function']) || isset($doAction['class'])) {
							$obj		=	(isset($doAction['function']))? $doAction['function'] : $doAction['class'];
							$dependency	=	(!empty($obj['dependency']))? $obj['dependency'] : false;
							$inject		=	(!empty($obj['inject']))? $obj['inject'] : false;
							$doObj		=	(isset($doAction['function']))? 'func' : 'class';
							# If there are multiple in one event, run in a loop
							if(isset($doAction[$doObj][0])) {
								# Return or echo is not allowed in this scenario
								$newObj	=	false;
								# Loop through each action
								foreach($doAction[$doObj] as $obj) {
									$new[$doObj]	=	$obj;
									$this->doWorkflow(array($new));
								}
							}
							else
								$newObj	=	$this->callDynamicWorkflow($doObj,$obj,$inject,$dependency);
						
							# Allows for object to be echoed or returned
							if(isset($obj['echo']) && $obj['echo'] == 'true')
								echo $newObj;
							elseif(isset($obj['return']) && $obj['return'] == 'true')
								return $newObj;
						}
						else {
							foreach(array('include','include_once','require','require_once') as $value) {
								if(isset($doAction[$value])) {
									$file	=	$this->matchFunction($doAction[$value]);
									
									if(is_file($file)) {
										$nRender	=	nApp::call('nRender');
										switch($value) {
											case('include'):
												if(isset($doAction['render']) && strtolower($doAction['render']) == 'false'){
													include($file);
												}
												else
													echo $nRender->render($file,'include');
												break;
											case('include_once'):
												if(isset($doAction['render']) && strtolower($doAction['render']) == 'false'){
													include_once($file);
												}
												else
													echo $nRender->render($file,'include_once');
												
												break;
											case('require'):
												if(isset($doAction['render']) && strtolower($doAction['render']) == 'false'){
													require($file);
												}
												else
													echo $nRender->render($file,'require');
												break;
											case('require_once'):
												if(isset($doAction['render']) && strtolower($doAction['render']) == 'false'){
													require_once($file);
												}
												else
													echo $nRender->render($file,'require_once');
										}
									}
								}
							}
						}
					}
				}
			}
		
		public	function callDynamicWorkflow($type,$obj,$inject,$dependency)
			{
				$name	=	($type == 'func')? "doFunctionWorkflow" : "doClassWorkflow";
				return $this->{$name}($obj,$inject,$dependency);
			}
		/*
		**	@description	Either includes the required class file, or adds the namespace if provided
		*/
		public	function addClassIncluded($obj,$func)
			{
				# If there is an include file, try and include it
				if(isset($obj['include'])) {
					$parsedPath	=	$this->matchFunction($obj['include']);
					$hardPath	=	NBR_ROOT_DIR.$obj['include'];
					
					if(is_file($parsedPath))
						$file	=	$parsedPath;
					elseif(is_file($hardPath))
						$file	=	$hardPath;
					else
						$file	=	false;
						
					if(empty($file) || !is_file($file))
						throw new \Exception('Support file not found for :'.$func);
					else
						include_once($file);
				}
				elseif(isset($obj['namespace'])) {
					$parsedPath	=	$this->matchFunction($obj['namespace']);
					
					if(is_dir($parsedPath))
						$this->nApp->addNamespace($parsedPath);
					else
						throw new \Exception('Support file not found for :'.$func);
				}
			}
		
		public	function doClassWorkflow($obj, $inject = false, $dependency = false)
			{
				if($this->isActiveRecording()) {
					$rec	=	array_filter(array('*********'.__FUNCTION__.'*********',$obj,$inject,$dependency));
					$this->getTimeCast($this->getTimeCastLast(),$rec);
					$this->nApp->saveSetting('workflow_run',$rec);
				}
				# Get the name of the class
				$func				=	(isset($obj['name']))? $obj['name'] : false;
				
				if(empty($func))
					return;
				
				# Check if there is a dependency in the constructor
				$constr_dependency	=	(!empty($obj['@attributes']['dependency']))? $obj['@attributes']['dependency'] : false;
				# See if this requires a chaining of the same object
				$chain				=	(!empty($obj['chain']));
				# Set default so as not to draw errors
				$dependency_arr		=	false;
				# Set method name
				$method				=	(!empty($obj['method']))? $obj['method'] : false;
				# See if this class is to be returned
				$return				=	(isset($obj['return']) && $obj['return'] == 'true');
				# See if this class is to echoed
				$echo				=	(isset($obj['echo']) && $obj['echo'] == 'true');
				# Create a multi-depenence map
				if(is_array($dependency)) {
					if(!is_array($dependency['inject'])) {
						$dependency['inject']	=	array($dependency['inject']);
						$dependency['into']		=	array($dependency['into']);
					}
					# Creates the actual dependency array
					$dependency_arr	=	array_combine($dependency['into'],$dependency['inject']);
				}
				# Uses the nAutomator as the class to use
				if($func == 'this') {
					if($method) {
						if(!empty($dependency))
							$this->{$method}(new $dependency());
						elseif(!empty($inject))
							$this->{$method}($inject);
						else
							$this->{$method}();
							
						if(!method_exists($this,$method))
							throw new \Exception("Method ({$method}) does not exist.");
					}
				}
				else {
					# If the class doesn't exist and an include is indicated, try to include it
					# If no include is set, then it will try to autoload
					$this->addClassIncluded($obj,$func);
					# If there is a method, process the class with methods in mind
					if($method) {
						if($inject) {
							$thisMethod	=	($chain)? 'doClassChaining' : 'doClassInject';
							$instance	=	$this->{$thisMethod}($obj,$func,$inject,$method,$dependency,$dependency_arr,$constr_dependency);
						}
						else {
							if($chain)
								$instance	=	$this->doClassChaining($obj,$func,$inject,$method,$dependency,$dependency_arr,$constr_dependency);
							else {
								if($dependency) {
									if($constr_dependency)
										$instance	=	$this->nApp->getPlugin($func,$this->nApp->getPlugin($constr_dependency))->{$method}($this->nApp->getPlugin($dependency));
									else
										$instance	=	$this->nApp->getPlugin($func)->{$method}($this->nApp->getPlugin($dependency));
								}
								else {
									if($constr_dependency)
										$instance	=	$this->nApp->getPlugin($func,$this->nApp->getPlugin($constr_dependency))->{$method}();
									else
										$instance	=	$this->nApp->getPlugin($func)->{$method}();
								}
							}
						}
					}
					else {
						if($inject)
							$instance	=	$this->nApp->getPlugin($func,$this->injector($inject));
						else {
							if($dependency)
								$instance	=	$this->nApp->getPlugin($func,$this->nApp->getPlugin($dependency));
							else
								$instance	=	$this->nApp->getPlugin($func);
						}
					}
					
					if($return)
						return $instance;
					elseif($echo)
						echo $instance;
				}
			}
			
		public	function doClassChaining($obj,$func,$inject,$method = false,$dependency = false,$dependency_arr = false,$constr_dependency = false)
			{
				$instance	=	($constr_dependency)? $this->nApp->getPlugin($func,$this->nApp->getPlugin($constr_dependency)) : $this->nApp->getPlugin($func);
				
				$this->chain($obj,$instance,$method,$inject,$dependency);	
			}
		
		public	function chain($obj,$instance,$method,$inject = false,$dependency=false,$dependency_arr=false)
			{
				if($this->isActiveRecording()) {
					$rec	=	array_filter(array('*********'.__FUNCTION__.'*********',$obj,$method,$inject,$dependency));
					$this->getTimeCast($this->getTimeCastLast(),$rec);
					$this->nApp->saveSetting('workflow_run',$rec);
				}
				
				if(!is_array($obj['chain']))
					$obj['chain']	=	array($method,$obj['chain']);
				else
					array_unshift($obj['chain'],$method);
				
				$i = 0;
				foreach($obj['chain'] as $method) {
					if($dependency) {
						if(is_array($dependency)) {
							if(is_array($dependency_arr) && isset($dependency_arr[$method])) {
								$useDep	=	$dependency_arr[$method];
								$instance->{$method}($this->nApp->getPlugin($useDep));
							}
							else {
								if($inject && $i == 0)
									$instance->{$method}($this->injector($inject));
								else
									$instance->{$method}();
							}
						}
						else
							$instance->{$method}(new $dependency());
					}
					else {
						if($inject && $i == 0)
							$instance->{$method}($this->injector($inject));
						else
							$instance->{$method}();
					}
					
					$i++;
				}
			}
		
		public	function injector($inject)
			{
				$isStr		=	(is_string($inject));
				$isArg		=	(isset($inject['arg']));
				$injectAsIs	=	(!empty($isStr) || !empty($isArg));
				if($isArg)
					$inject	=	$inject['arg'];
					
				return	(($injectAsIs)? $inject : $this->doWorkflow($inject));
			}
		
		public	function doClassInject($obj,$func,$inject,$method = false,$dependency = false,$dependency_arr = false,$constr_dependency = false)
			{
				if($this->isActiveRecording()) {
					$rec	=	array_filter(array('*********'.__FUNCTION__.'*********',$obj,$func,$method,$inject,$dependency,$constr_dependency));
					$this->getTimeCast($this->getTimeCastLast(),$rec);
					$this->nApp->saveSetting('workflow_run',$rec);
				}
				
				if($dependency)
					$instance	=	$this->nApp->getPlugin($func,$this->nApp->getPlugin($dependency))->{$method}($this->injector($inject));
				else {
					if($constr_dependency)
						$instance	=	$this->nApp->getPlugin($func,$this->nApp->getPlugin($constr_dependency))->{$method}($this->injector($inject));
					else
						$instance	=	$this->nApp->getPlugin($func)->{$method}($this->injector($inject));
				}
				
				return $instance;
			}
		
		public	function doFunctionWorkflow($obj,$inject = false,$dependency = false)
			{
				if($this->isActiveRecording()) {
					$rec	=	array_filter(array('*********'.__FUNCTION__.'*********',$obj,$inject,$dependency));
					$this->getTimeCast($this->getTimeCastLast(),$rec);
					$this->nApp->saveSetting('workflow_run',$rec);
				}
				
				$func	=	$obj['name'];
				if(!function_exists($func)) {
					if(isset($obj['include'])) {
						$inc	=	$this->matchFunction($obj['include']);
						if(!is_file($inc))
							throw new \Exception('File not found: '.$inc);
							
						require_once($inc);
					}
					else
						$this->nApp->autoload(array($func));
					
					if(!function_exists($func) && $func != 'die' && $func != 'exit')
						throw new \Exception($this->nApp->getHelper('Safe')->encodeSingle("Function ({$func}) does not exist. Use an include line: <include>/path/to/php</include>"));
				}
				
				if(!empty($inject)) {	
					if(!is_string($inject))
						$inject	=	(!isset($inject[0]))? array($inject) : $inject;
					
					if($func == 'die' || $func == 'exit')
						die(((is_string($inject)? $inject : $this->doWorkflow($inject))));
					else {
						$set	=	((is_string($inject))? $inject : $this->doWorkflow($inject));
						return	$func($set);
					}
				}
				elseif(!empty($dependency)) {
					return	$func(new $dependency());
				}
				else {
					if($func == 'die' || $func == 'exit')
						die();
					else
						return $func();
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
				# Get all the config files
				$configs	=	$this->nApp->getHelper('nRegister')->parseRegFile($dir);
				# Convert them to an array
				$configs	=	$this->toArray($configs);
				# Run the automator
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
				# Name of the input (default is "file")
				$inputName	=	(!empty($settings['name']))? $settings['name'] : 'file';
				# Set the file types (derived from `file_types` table if left empty)
				$files		=	(!empty($settings['types']) && is_array($settings['types']))? $settings['types'] : $this->getFileTypes();
				# Get move options which include "overwrite" (unlinks file if exists), "unique" (uses the unique name of the file instead of real name)
				$move		=	(!empty($settings['move_opts']) && is_array($settings['move_opts']))? $settings['move_opts'] : array('overwrite'=>true);
				# Add htaccess to folder
				$protect	=	(!empty($settings['protect']));
				# Save folder
				$dir		=	(!empty($settings['dir']))? $settings['dir'] : NBR_CLIENT_DIR.DS.'images'.DS.'default'.DS;
				# Add a callback
				$callback	=	(!empty($settings['callback']))? $settings['callback'] : false;
				# Start uploader
				$nFiles		=	new nUploader($inputName);
				try{
					# Set the destination folder
					$nFiles	->setDestination($dir,array('protect'=>$protect))
							# Adds allowable ext
							->allowFileTypes($files)
							->setMoveAttr($move)
							->setObserver();
							# See if there are anymore pre-processing
							if(!empty($callback))
								$nFiles->setCallback($callback);
							# Upload the files remaining
							$nFiles->upload();
					# Record file transaction
					$nFiles->recordTransaction();
					# Send success
					if(!empty($nFiles->getData()))
						$this->nApp->saveError('file_listener',array('success'=>true));
					# Send back the array
					return	$nFiles->getData();
				}
				catch(Exception $e) {
					if($this->isAdmin()) {
						# Just die and display error
						die($e->getMessage());
					}
					# Save to error array
					$this->nApp->saveError('file_upload',array('success'=>false,'msg'=>'upload failed'));
					# Save to log file
					$this->nApp->saveToLogFile('error_uploads.txt',$e->getMessage(),array('logging','exceptions'));
					# Set 
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
				# Get the cache directory
				$dir	=	(!empty($this->nApp->getDataNode('site')->cache_folder))? $this->nApp->getDataNode('site')->cache_folder : DS.'client'.DS.'settings'.DS;
				# If the cache config file is available, parse and return
				if(is_file($prefs = $dir.'configs.json'))
					$this->cachedArr	=	array(
												'configs'=>json_decode(file_get_contents($prefs)),
												'xml_add_list'=>json_decode(file_get_contents($dir.'xml_add_list.json')));
				
				return $this;
			}
		/*
		**	@description	This is the companion function to getCachedConfigs(). When loaded at the bottom of the
		**					workflow, will capture all the loaded configs and save the configs to a json file
		**					for recall at the beginning of the page load
		*/
		public	function saveCachedConfigs()
			{
				$dir		=	$this->nApp->toSingleDs(DS.trim(NBR_ROOT_DIR.DS.$this->nApp->getDataNode('site')->cache_folder,DS).DS);
				$filename	=	$dir.'configs.json';
				$xmlList	=	$dir.'xml_add_list.json';
				
				if(is_file($filename))
					return;

				$configs		=	$this->nApp->getConfigs();
				$xml_add_list	=	$this->nApp->getData()->getXmlAddList();
				
				if(!empty($configs)) {
					if($this->nApp->isDir($dir,true,0755)) {
						$this->nApp->savePrefFile('configs',$configs);
						$this->nApp->savePrefFile('xml_add_list',$xml_add_list);
					}
				}
			}
		
		public	function toPrefs()
			{
				$configs	=	$this->cachedArr;
				if(!empty($configs['configs'])) {
					$this->nApp->saveSetting('config',$configs['configs']);
					$this->nApp->saveSetting('xml_add_list',$configs['xml_add_list']);
				}
			}
		/*
		**	@description	This is the main application automator
		*/
		public	function getInstructions($type,$search = 'workflow',$append=false,$settings=false)
			{
				$Sortify	=	$this->nApp->getHelper('Sortify');
				# Get the action sort
				$configs	=	$Sortify
									->setActionName($this->listenForKey)
									->execute($type,$append,$settings);
				
				$keys		=	array_keys($configs);
				
				$this->doWorkflow($configs);
				
				return $this;
			}
		
		public	function getStoredConfigs()
			{
				return self::$configs;
			}
		
		/*
		**	@description	This will reorder the events based on the array. The keys/values must always be before/after
		*/
		protected	function reorderEvent($order,$before,$after)
			{
				$before	=	(empty($before))? array() : $before;
				$after	=	(empty($after))? array() : $after;
				$new	=	array();
				
				foreach($before as $anchor => $find) {
					$anchorRev	=	preg_replace('/[^a-zA-Z\_]/','',$anchor);
				
					$key		=	array_search($find,$order);
					if(isset($order[$key-1]) && $order[$key-1] == $anchor)
						continue;
					
					unset($order[$key]);
					$order		=	array_values($order);
					$placement	=	array_search($anchorRev,$order)+1;
					
					if($placement == 0) {
						$front	=	array_shift($order);
						array_unshift($order,$find);
						$order	=	array_merge(array($front),$order);
					}
					else
						array_splice($order,$placement,0,array($find));
				}
				
				foreach($after as $anchor => $find) {
					$anchor		=	preg_replace('/[^a-zA-Z\_]/','',$anchor);
					# Find the anchor
					$key		=	array_search($find,$order);
					# Find the cut
					$cut		=	array_search($anchor,$order);
					# Remove the after from the array
					unset($order[$cut]);
					$order		=	array_values($order);
					# Insert the after after the value in question
					array_splice($order,($key+1),0,array($anchor));
				}
				
				$this->setOrder($order);
				
				return $this;
			}
			
		public	function setOrder($array)
			{
				$this->position['order']	=	$array;
			}
		
		public	function orderArrayGen($array)
			{
				$placement	=
				$new		=	array();
				
				foreach($array as $key => $row) {
					$kName	=	(isset($row['name']))? $row['name'] : $key;
					if(isset($row['method']))
						$kName	.=	'\\'.$row['method'];
						
					$new[$kName]	=	$row;
					
					if(isset($row['after'])) {
						$placement[]	=	array($row['after']=>$kName);
					}
					
					if(isset($row['before'])) {
						$placement[]	=	array($kName=>$row['before']);
					}
					
					if(!isset($row['before']) && !isset($row['after'])) {
						$placement[]	=	array($kName=>false);
					}
				}
				
				$holding	=	array();
				//$order		=	array_keys($new);
				foreach($placement as $row) {
					foreach($row as $before => $after) {
						if(empty($after))
							continue;
						
						elseif(!in_array($before,$holding) && !in_array($after,$holding)) {
							$holding[]	=	$before;
							$holding[]	=	$after;
						}
						elseif(in_array($before,$holding) && !in_array($after,$holding)) {
							$placeKey	=	(array_search($before));
							$befPlace	=	($placeKey < 0)? 0 : $placeKey;
							$holding	=	$this->nApp->insertIntoArray($holding,$after,$befPlace);
						}
						elseif(!in_array($before,$holding) && in_array($after,$holding)) {
							$placeKey	=	(array_search($after,$holding));
							$holding	=	$this->nApp->insertIntoArray($holding,$before,$placeKey);
						}
						elseif(in_array($before,$holding) && in_array($after,$holding)) {
							$this->saveIncidental('sorting_order',array('msg'=>'Events have been ordered already. Further ordering will likely remove a previous ordered item.'));
							
							//$bKey		=	(array_search($before,$holding));
							//$aKey		=	(array_search($after,$holding));
							//$holding	=	$this->nApp->insertIntoArray($holding,$after,$befPlace);
						}
					}
				}
				
			
				if(!empty($holding)) {
					$final	=	array();
					if(count($holding) != count(array_keys($new))) {
						$holding	=	array_merge(array_diff(array_keys($new),$holding),$holding);
					}
					
					foreach($holding as $key) {
						$final[]	=	$new[$key];
					}
					
					return $final;
				}
				else {
					return array_values($new);
				}
			}
		
		private	function organizeByAttr($array,$type = 'core')
			{
				if(isset($array['@attributes']))
					$array	=	array($array);
					
				$addToObj	=	function($obj1,$obj2,$nApp)
					{
						
						foreach($obj1 as $attr => $values) {
							if(isset($obj2[$attr])) {
								if($attr == '@attributes')
									continue;
								
								if(!isset($obj1[$attr][0])) {
									$obj1[$attr]	=	array($obj1[$attr]);
								}
								
								foreach($obj2[$attr] as $key => $value) {
									$obj1[$attr][]	=	$value;
								}
								
								$obj1[$attr]	=	$nApp->orderArrayGen($obj1[$attr]);
							}
						}
						
						return $obj1;
					};
					
				$i = 0;
				foreach($array as $row) {
					if(!isset($row['@attributes']['event'])) {
						throw new \Exception('No event specified in workflow object. An "event" attribute must be specified or this object will not run.');
					}
					
					$name		=	$row['@attributes']['event'];
					
					if(isset($new[$name]))
						$new[$name]['object']	=	$addToObj($row['object'],$new[$name]['object'],$this);
					else
						$new[$name]				=	$row;
					
					$this->setOrderBy($row,$name,$i);					
					//unset($new[$name]['@attributes']);
					$i++;
				}
				
				return (!empty($new))? $new : $array;
			}
		
		public	function setOrderBy($row,$name,$i)
			{
				if(isset($row['@attributes']['before'])) {
					$this->position['position']['before'][$name.$i]	=	$row['@attributes']['before'];
				}
				if(isset($row['@attributes']['after'])) {
					$this->position['position']['after'][$name.$i]	=	$row['@attributes']['after'];
				}
				
				return $this;
			}
		
		public	function setListenerName($name = 'action')
			{
				$this->listenForKey	=	$name;
				
				return $this;
			}
		
		public	function getOrderBy($type = false)
			{
				if($type)
					return (!empty($this->position['position'][$type]))? $this->position['position'][$type] : false;
				
				return (!empty($this->position))? $this->position : false;
			}
	}