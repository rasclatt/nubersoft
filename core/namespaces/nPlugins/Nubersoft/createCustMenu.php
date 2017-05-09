<?php
namespace nPlugins\Nubersoft;
/*
** @description This extends the component class to make the components for<br>
** the toolpallet menu create/update slider
*/
class	createCustMenu	extends create
	{
		public		$table,
					$inputArray,
					$command,
					$displayCol;
		
		/*
		** @param	[array]		$inputArray	- Data for the entire component
		** @param	[string]	$table		- Name of the table to query from (for field input formatting)
		** @param	[string]	$command	- the command that will be executed on the component (ex: "page_builder")
		** @param	[string]	$displayCol	- not 100% sure what this is for...
		*/
		public	function	component($inputArray, $table, $command, $displayCol = false)
			{
				if(!\Nubersoft\DatabaseConfig::$con)
					return;
				# Data array
				$this->inputArray	=	$inputArray;
				$this->table		=	$table;
				$this->command		=	$command;
				$this->displayCol	=	$displayCol;
				# Retrieve the page global
				$unique_id			=	$this->nApp->getPage('unique_id');
				# Get dropdowns for this table
				$dMenus				=	$this->toArray($this->nApp->getDropDowns($this->table));
				# Set the dropdowns
				$this->setDropDowns(((!empty($dMenus))? $dMenus: array()));
				# Arraytize
				$this->inputArray[0]=	(isset($this->inputArray[0]))? $this->toArray($this->inputArray[0]): array();
				# Determine if the component is new or old
				$function			=	(!empty($this->inputArray[0]['unique_id']))? 'update': 'add';
				# Include layout
				include(__DIR__.DS.'createCustMenu'.DS.'component.php');
			}
		/*
		** @method	Includes the parent dropdown menu
		*/
		public	function	dropMenu()
			{
				# Check for admin access and if the toggle edit is on
				$checkRows		=	$this->nApp->nQuery()
										->select()
										->from($this->table)
										->orderBy(array("unique_id"=>"ASC"))
										->getResults();
				# Shorten array name
				$payload			=	(isset($this->inputArray[0]))? $this->inputArray[0]:array();
				# Set a start for the build
				$default_container	=	(!empty($payload['parent_id']))? $payload['parent_id']: '';
				$default_disp		=	(!empty($default_container))? $default_container: 'Select Parent Directory';
				# Include layout
				include(__DIR__.DS.'createCustMenu'.DS.'dropMenu.php');
			}
	}