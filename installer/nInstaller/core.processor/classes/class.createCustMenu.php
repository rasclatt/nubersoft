<?php
//********************************************//
//*** THIS CLASS IS A CUSTOMIZED VIEW FOR THE MAIN MENUS TOOL PALETTE//
//********************************************//
	class	createCustMenu	extends create
		{
			public		$table;
			public		$inputArray;
			public		$command;
			public		$displayCol;
			
			// Create a component
			public	function	component($inputArray, $table, $command, $displayCol = false)
				{
					register_use(__METHOD__);
					if(!DatabaseConfig::$con)
						return;
					// Data array
					$this->inputArray	=	$inputArray;
					$this->table		=	$table;
					$this->command		=	$command;
					$this->displayCol	=	$displayCol;
					// Retrieve the page global
					$unique_id			=	NubeData::$settings->page_prefs->unique_id;
					// Arraytize
					$this->inputArray[0]=	(isset($this->inputArray[0]))? (array) $this->inputArray[0]:array();
					// Determine if the component is new or old
					$function			=	(!empty($this->inputArray[0]['unique_id']))? 'update': 'add';
					// Include layout
					include(RENDER_LIB.'/class.html/createCustMenu/component.php');
				}
				
//=======> FUNCTION: Drop menus for containers
			public	function	dropMenu()
				{
					register_use(__METHOD__);
					
					// Check for admin access and if the toggle edit is on
					$checkRows		=	$this->nubquery	->select()
														->from($this->table)
														->orderBy(array("unique_id"=>"ASC"))->fetch();
					// Shorten array name
					$payload			=	(isset($this->inputArray[0]))? $this->inputArray[0]:array();
					// Set a start for the build
					$default_container	=	(!empty($payload['parent_id']))? $payload['parent_id']: '';
					$default_disp		=	(!empty($default_container))? $default_container: 'Select Parent Directory';
					// Include layout
					include(RENDER_LIB.'/class.html/createCustMenu/dropMenu.php');
				}
		} ?>