<?php
namespace Nubersoft;

class	nAdminTools	extends nConfigEngine
	{
		protected	$valid,
					$configs;
		
		public	function useConfigs($dir = false)
			{
				if(!$dir)
					$dir	=	NBR_CLIENT_DIR;
				
				$this->cEngine	=	new configFunctions(new \Nubersoft\nAutomator());
				$this->configs	=	\nApp::getConfigs();
				
				
				//$this->configs	=	$this->cEngine	->addLocation(NBR_CLIENT_DIR)
				//									->addLocation(NBR_TEMPLATE_DIR.'/default/')
				//									->getConfigs();
				
				return $this;
			}
		
		public	function isValid()
			{
				return $this->valid;
			}

		public	function getAdmintoolsBody()
			{		
				return $this->cEngine->getSettings(array('ui','admintools','body'));
			}
		/*
		**	@description	Checks if there is an "ontable" action. This means, if a specific table is in the request,
		**					and there is an ontable action, then it will run that action
		**	@param $table	[string]	This is the current table name
		**	@param $type	[string]	This parameter will modify the search array
		*/
		public	function getAdminToolsOnTable($table,$type = 'body')
			{
				$this->renderKey	=	'name';
				// Search configs for ontable layouts
				$this->layout		=	$this->cEngine->getSettings(array('ui','admintools','body','ontable'));
				// Current table to match
				$this->table		=	$table;
				// Search through the matched array and see if a key is matched
				$returned			=	$this	->useArray($this->layout)
												->hasKey($this->renderKey);
				// See if the table name is in the list
				$this->valid		=	(is_array($returned[$this->renderKey]) && in_array($table,$returned[$this->renderKey]));
				
				return $this;
			}
		/*
		**	@description	This method will return admintool/body arrays
		*/
		public	function getAdminToolsLayout()
			{
				$this->renderKey	=	'body';	
				$this->layout		=	$this->cEngine->getSettings(array('ui','admintools'));
				$this->valid		=	$this->hasKey($this->renderKey);
				return $this;
			}
		/*
		**	@description	This method will loop through returned config and find ontable matches that have 
		**					valid naming. If so, it will recurse the array and convert any markdown then include
		*/
		public	function includeByTableName()
			{
				if(!$this->valid)
					return;
				
				foreach($this->layout['ontable'] as $includer) {
					if(isset($includer[0])) {
						foreach($includer as $row)
							$this->nameLoop($row, new \Nubersoft\nFunctions());
					}
					else
						$this->nameLoop($includer, new \Nubersoft\nFunctions());
				}
			}
		
		private	function nameLoop($includer, \Nubersoft\nFunctions $nFunctions)
			{
				if(!empty($includer['name'])) {
					if($includer['name'] != $this->table)
						return false;
					
					$type	=	$this->determineInc($includer);
					if($type) {
						$inc = \nApp::nAutomator()->matchFunction($includer[$type]);
						try {
							if(is_file($inc)) {
								$this->determineInc($includer,$inc);
							}
							else
								throw new \Exception('File not found:'.$inc);
						}
						catch(\Exception $e) {
							echo nErrors::directory($inc,$e);
						}
					}
				}
			}
	}