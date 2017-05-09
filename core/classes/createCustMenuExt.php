<?php

//***************************************************************************************************************************************************//
//****************************************************************************** THIS CLASS REMOVES THE ABILITY TO MOVE THE SELECTED MENU INTO ITSELF//
//***************************************************************************************************************************************************//

class	createCustMenuExt	extends createCustMenu
	{
		public	$table;
		public	$inputArray;
		public	$command;
		public	$displayCol;
		
		public	function	dropMenu()
			{
				register_use(__METHOD__);
				$unique_id		=	NubeData::$settings->page_prefs->unique_id;
				// Check for admin access and if the toggle edit is on
				$checkRows		=	$this->nubquery	->select()
													->from($this->table)
													->where("unique_id != '$unique_id'")
													->orderBy(array("unique_id"=>"ASC"))
													->fetch();

				$payload			=	(isset($this->inputArray[0]))? $this->inputArray[0]:array();
				// Set a start for the build
				$default_container	=	(!empty($payload['parent_id']))? $payload['parent_id']: '';
				$default_cont_name	=	(!empty($payload['parent_id']))? $payload['full_path']: '';
				$default_disp		=	(!empty($default_container))? $default_cont_name: 'Select Parent Directory';
				// Include the dropdown
				include(NBR_RENDER_LIB.DS.'class.html'.DS.'createCustMenu'.DS.'dropMenu.php');
			}
	}