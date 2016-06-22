<?php
	
	class	ComponentEditor
		{
			public		$ref_page;
			public		$page_id;

			protected	$nubsql;
			protected	$nubquery;
			protected	$nuber;
			protected	$command;
			protected	$data;
			protected	$allowAdd;
			protected	$tempid;
			protected	$curr;
			protected	$table;
			
			public	function __construct($page_id = false, $ref_page = false)
				{
					register_use(__METHOD__);
					AutoloadFunction('nQuery');
					$this->page_id	=	$page_id;
					$this->ref_page	=	$ref_page;
					$this->tempid	=	"comp".uniqid(rand(1000, 10000));
				}
			
			public	function AddNewComponent($CompSet = false)
				{
					register_use(__METHOD__);
					include(NBR_RENDER_LIB.'/class.html/ComponentEditor/AddNewComponent.php');
				}
			
			public	function Display($data = array(), $table = 'components', $allowAdd = true, $command = false)
				{
					register_use(__METHOD__);
					$this->table	=	$table;
					$this->data		=	$data;
					$this->command	=	$command;
					$this->allowAdd	=	$allowAdd;
					
					// Check if this component contains an active data set
					$CompSet		=	(isset($this->data['unique_id']));
					// 
					include(NBR_RENDER_LIB.'/class.html/ComponentEditor/Display.php');
				}
			
			public	function DeleteComponent($CompSet = false)
				{
					register_use(__METHOD__);
					if(isset($this->data['ID']) && !empty($this->data['ID']))
						include(NBR_RENDER_LIB.'/class.html/ComponentEditor/DeleteComponent.php');
				}
				
			public	function TinyMCE($CompSet = false)
				{
					register_use(__METHOD__);
					if(isset($this->data['ID']) && !empty($this->data['ID'])) {
							include(NBR_ROOT_DIR.'/core.ajax/component/button.TinyMCE.php');
					 	}
				}
				
			public	function HelpDesk($CompSet = false)
				{
					register_use(__METHOD__);
					if(isset($this->data['ID']) && !empty($this->data['ID'])) {
							include(NBR_ROOT_DIR.'/core.ajax/component/button.helpdesk.php');
					 	}
				}
			
			public	function DuplicateComponent($CompSet = false)
				{
					register_use(__METHOD__);
					include(NBR_RENDER_LIB.'/class.html/ComponentEditor/DuplicateComponent.php');
				}
			
			protected	function DuplicateComponentsList($values = false)
				{
					register_use(__METHOD__);
					$icon_arr				=	array();
					$icon_arr['row']		=	'icn_div.png';
					$icon_arr['div']		=	'icn_div.png';
					$icon_arr['button']		=	'icn_button.png';
					$icon_arr['code']		=	'icn_code.png';
					$icon_arr['button']		=	'icn_button.png';
					$icon_arr['form_email']	=	'icn_form_email.png';
					$icon_arr['image']		=	'icn_image.png';
					$icon_arr['text']		=	'icn_text.png';
					$icon_arr['unknown']	=	'icn_alert.png';
					
					if(!empty($values) && is_array($values)) {
							include(NBR_RENDER_LIB.'/class.html/ComponentEditor/DuplicateComponentsList.php');
						}
				}
			
			public	function	ContainerDropDown()
				{
					$query		=	nQuery();
					$_checkCol	=	$query->addCustom("SHOW columns from `".$this->table."` where field='ref_page'",true)->fetch();
					
					if($_checkCol != 0) {
							
							$containers	=	$query	->select(array("unique_id","parent_id","content"))
													->from($this->table)
													->where(array('ref_page'=>$this->ref_page))
													->addCustom(" and (component_type = 'div' or component_type = 'row')")
													->fetch();
							
						//	$containers	=	$this->nubsql->FetchAssocArr("select unique_id,parent_id,content from " . $this->table . " where ref_page = '".$this->data['unique_id']."' and (component_type = 'div' or component_type = 'row')",'unique_id');
							if(is_array($containers)) {
								foreach($containers as $_objects) {
									if(!empty($_objects['parent_id']))
										$_parents[$_objects['parent_id']][]	=	$_objects['unique_id'];
								}
							}
							
							$_parent	=	(!empty($this->data['unique_id']) && isset($_parents[$this->data['unique_id']]))? true:false;
							$_child		=	(!empty($this->data['parent_id']) && isset($_parents[$this->data['parent_id']]))? true:true;
							$_isDiv		=	(!empty($this->data['component_type']) && in_array($this->data['component_type'],array('div','row')))? true:false; 
							
							include(NBR_RENDER_LIB.'/class.html/ComponentEditor/ContainerDropDown.php');
						}
				}
			
		// Main component function
		public function createFormElements()
				{
					register_use(__METHOD__);
					AutoloadFunction("component_assembler,form_field");
					
					$settings			=	component_assembler(array("table"=>Safe::URL64($this->table)));
					$layout				=	(!empty($settings['layout']))? $settings['layout']:array();
					$array				=	(!empty($settings['format']))? $settings['format']:array();
					$options			=	(!empty($settings['options']))? $settings['options']:array();
					$title				=	(!empty($settings['title']))? $settings['title']:"Create New";
					$values				=	$this->data;
					$table				=	(isset($settings['table']))? $settings['table']:"components";
					
					nApp::resetTableAttr($table);
					
					ob_start();
					
					foreach($layout as $compname => $compvals)
						include(NBR_RENDER_LIB.'/class.html/ComponentEditor/createFormElements.php');
			
					$data	=	ob_get_contents();
					ob_end_clean();
					return $data;
				}
				
			public	function	HelpDeskDisplay($column = false)
				{
					register_use(__METHOD__);
					$query	=	nQuery();
					AutoloadFunction('check_empty');
					if(isset($_SESSION) && check_empty($_SESSION,'helpdesk','on')) {
						AutoloadFunction('get_tables_in_db');
						$tables	=	get_tables_in_db();
						if(!$tables)
							$check_helper	=	$query->addCustom("show tables like 'help_desk'",true);
						else
							$check_helper	=	(in_array("help_desk",$tables))? 1:0;
					
						if($check_helper != 0) {
							$check_helper		=	$query	->select()
															->from("help_desk")
															->where(array("assoc_table"=>$this->table,"assoc_column"=>$column,"page_live"=>'on'))
															->fetch();
							if($check_helper !==  0) {
								$results	=	$check_helper[0];
								include(NBR_ROOT_DIR.'/core.ajax/component/component.helpdesk.php');
							}
						}
					}
				}
		}