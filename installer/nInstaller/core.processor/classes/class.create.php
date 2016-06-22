<?php

//***********************************************//
// THIS CLASS IS A MAIN COMPONENT CREATION CLASS //
//***********************************************//

	class	create
		{	
			// Parent data
			public		$inputArray;
			// Apply to table
			public		$table;
			// Set a default command
			public		$command;
			// Searched and returned child
			public		$returnChild;
			public		$iterator;
			// Used for help desk
			public		$column;
			// Container and settings for random number
			public		$rand;
			public		$start_num;
			public		$end_num;
			// All nuber elements
			public		$nubsql;
			public		$nubquery;
			public		$nuber;
			// jQuery Creator
			public		$jQuery_type;
			public		$jQuery_payload;
			public		$jQuery_random;
			
			protected	$con;
			protected	$unique_id;
			protected	$dropdowns;
			
			// Drive Down Array Variables
			private		$keys;
			private		$values;
			private		$curr;
			
			public	function __construct($unique_id = false)
				{
					register_use(__METHOD__);
					
					AutoloadFunction('nQuery,Input');
					$this->unique_id	=	$unique_id;
					$this->nubquery		=	nQuery();
				}
				
			
			public	function	SetDropDowns($dropdowns = array())
				{
					register_use(__METHOD__);
					
					$this->dropdowns =	(is_array($dropdowns) && !empty($dropdowns))? $dropdowns:false;
				}
			
			// Create a component
			public	function	component($inputArray, $table, $command, $displayCol = false)
				{
					register_use(__METHOD__);
					
					// Initiate engines
					$this->engines();
					
					// Data array
					$this->inputArray	=	$inputArray;
					
					$this->table		=	$table;
					
					$this->command		=	$command;
					
					// Determine if the component is new or old
					$function			=	(isset($this->inputArray[0]['unique_id']) && !empty($this->inputArray[0]['unique_id']))? 'update': 'add';
					
					// Determine if it's been admin locked
					if(isset($this->inputArray[0]['admin_lock']) && !empty($this->inputArray[0]['admin_lock']))
						$echoField	=	(!is_admin())?  false: true;
					else
						$echoField	=	true;
						
					// Include html display
					include(RENDER_LIB.'/class.html/create/component.php');
						
					return $this;
				}

//=======> FUNCTION: Help desk function
			public	function	helpdesk($table, $column)
				{
					register_use(__METHOD__);
					
					$this->table		=	$table;
					$this->column		=	$column;
					
					if(isset($_SESSION['helpdesk']) && $_SESSION['helpdesk'] == 'on') {
							$check_helper		=	$this->nubquery->addCustom("show tables like 'help_desk'",true);
						
							if($check_helper !== 0) {
									$check_helper		=	$this->nubquery	->select()
																			->from("help_desk")
																			->where(array("assoc_table"=>$this->table,"assoc_column"=>$this->column,"page_live"=>'on'))
																			->fetch();
								
									if($check_helper !==  0) {
											$results	=	$check_helper[0]; 
											include(RENDER_LIB.'/class.html/create/helpdesk.php');
										}
								}
						}
				}
			
			protected	function FetchName($_unique_id = false)
				{
					register_use(__METHOD__);
					
					$id		=	(!$_unique_id && !empty($_unique_id))? $_unique_id:$this->inputArray[0]['unique_id'];
					$fetch	=	$this->nubquery	->select("content")
												->from("components")
												->where(array("unique_id"=>$id))
												->fetch();
												
					return ($fetch != 0)? $fetch[0]['content']:array();
				}
			
//=======> FUNCTION: Drop menus for containers
			public	function	dropMenu()
				{
					register_use(__METHOD__);
					
					$_checkCol	=	$this->nubquery->addCustom("SHOW columns from `".$this->table."` where field='ref_page'",true);
					
					if($_checkCol !== 0) {
							
							$containers	=	$this->nubquery	->select(array("unique_id","parent_id","content"))
															->from($this->table)
															->where(array('ref_page'=>$this->unique_id))
															->addCustom(" and (component_type = 'div' or component_type = 'row')",true)
															->fetch();
															
							if(is_array($containers)) {
									foreach($containers as $_objects) {
											if(!empty($_objects['parent_id']))
												$_parents[$_objects['parent_id']][]	=	$_objects['unique_id'];
										}
								}
							
							$_parent	=	(isset($_parents[$this->inputArray[0]['unique_id']]))? true:false;
							$_child		=	(isset($_parents[$this->inputArray[0]['parent_id']]))? true:true;
							$_isDiv		=	(in_array($this->inputArray[0]['component_type'],array('div','row')))? true:false;
							
							include(RENDER_LIB.'/class.html/create/dropMenu.php');
						}
				}
				
			public	function formAdd()
				{
					register_use(__METHOD__);
					$unique_id	=	NubeData::$settings->page_prefs->unique_id;
					include(RENDER_LIB.'/class.html/create/formAdd.php');
				}
				
			public	function dup_component()
				{
					register_use(__METHOD__);
					
					AutoloadFunction('FetchUniqueId');
					if(!empty($this->inputArray[0]['ID'])) 
						include(RENDER_LIB.'/class.html/create/dup_component.php');
				}
				
			public	function driveDownArr($curr)
				{
					register_use(__METHOD__);
					$unique_id				=	NubeData::$settings->page_prefs->unique_id;
					// Current Value
					$this->curr				=	$curr;
					
					$icon_arr				=	array();
					$icon_arr['row']		=	'icn_div.png';
					$icon_arr['div']		=	'icn_div.png';
					$icon_arr['button']		=	'icn_button.png';
					$icon_arr['code']		=	'icn_code.png';
					$icon_arr['button']		=	'icn_button.png';
					$icon_arr['form_email']	=	'icn_form_email.png';
					$icon_arr['image']		=	'icn_image.png';
					$icon_arr['text']		=	'icn_text.png';
					
					// Auto-increment
					$i	=	(!isset($i))? 0: ++$i;
					
					if(is_array($this->curr)) {
							// Loop through values. If array, drive down into the array until single values are available
							// build the form if single values.
							foreach($this->curr as $key => $value)
								include(RENDER_LIB.'/class.html/create/driveDownArr.php');
						}
				}

			public	function formDelete()
				{
					register_use(__METHOD__);
					
					if(!empty($this->inputArray[0]['ID'])) {
							AutoloadFunction('FetchUniqueId');
							$rand	=	FetchUniqueId();
							include(RENDER_LIB.'/class.html/create/formDelete.php');
						}
				}

			public	function formTinyMCE()
				{
					register_use(__METHOD__);
					
					$checkOn	=	$this->nubquery	->select(array("unique_id", "page_live", "hidden_task", "hidden_task_trigger"))
													->from("system_settings")
													->where(array("name"=>'tinyMCE',"page_live"=>'on'))
													->fetch();
					
					if($checkOn == 0)
						return;
						
					$checkBox		=	$checkOn[0];
					
					if($checkBox['page_live'] == 'on') {					
							if($checkBox['hidden_task'] == 'session')
								$callType	=	(isset($_SESSION['tinyMCE']))? true:false;
							else
								$callType	=	true;
								
							include(RENDER_LIB.'/class.html/create/formTinyMCE.php');	
						}
				}
				
				
//=======> FUNCTION: ADD HELP

			public	function formHelpDesk()
				{
					register_use(__METHOD__);
					
					include(RENDER_LIB.'/class.html/create/formHelpDesk.php');
				}

			public function createFormElements()
				{
					register_use(__METHOD__);
					AutoloadFunction("organize");
					formInputs::Initialize();
					// Store array to save includes/excludes
					$includeArray		=	array();
					// Set preliminary array for the storing of form input values
					$form_input_array	=	array();
					// Get preset activ columns
					$columnCheck	=	$this->nubquery	->select(array("component_value","variable_type"))
														->from("component_builder")
														->where(array("assoc_table"=>$this->table,"page_live"=>'on'))
														->orderBy(array("variable_type"=>"DESC", "page_order"=>"ASC"))
														->fetch();
					
					if($columnCheck != 0) {
						$i	=	0;
						foreach($columnCheck as $result) {
							$includeArray[$i]	=	$result['component_value'];
							$sectionBreak[$i]	=	$result['variable_type'];
							++$i;
						}
					}
						
					// Set the form options
					//$table_query	=	$this->nubquery->addCustom("describe components",true)->fetch();
					$_cols			=	organize($this->nubquery	->select(array("column_name","column_type","size"))
																	->from("form_builder")
																	->where(array("page_live"=>'on'))
																	->fetch(), 'column_name',true);
					
					include(RENDER_LIB.'/class.html/create/createFormElements.php');
				}
		}