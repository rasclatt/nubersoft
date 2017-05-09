<?php 
	class ToolInspector
		{
			protected	$doc_root;
			protected	$dropdowns;
			protected	$menu_set;
			protected	$payload;
			
			private		$_table_prefs;
			private		$_return_array;
			private		$_table;
			
			public	function __construct($doc_root)
				{
					register_use(__METHOD__);
					AutoLoadFunction('is_admin,InputFields,site_valid,nQuery');
					// This will set a long or short path for the link/mkdir
					// true = full directory from root
					// false = just relative links
					$this->doc_root	=	$doc_root;
				}
			public function	execute()
				{
					register_use(__METHOD__);
					
					if(!nApp::siteValid())
						return;
					if(is_admin()) {
							$nubquery	=	nQuery();
							// Fetch dropdowns
							$this->dropdowns	=	InputFields("main_menus");
							include(NBR_RENDER_LIB.DS.'class.html'.DS.'ToolInspector'.DS.'execute.php');	
						} 
				}
			
			protected	function FolderStucture($array,$key = false)
				{
					register_use(__METHOD__);
					
					foreach($array as $col => $value) { ?>
					
					<div class="inspector-mini-allwrap">
						<div class="inspector-mini-wrap">
								<?php $this->AllMenus($col); ?>
						</div>
						<?php if(!is_array($value)) { ?>
					</div>
					<?php	}
							
							if(is_array($value)) {
								$this->FolderStucture($value,$col); ?>
					</div>
						<?php	}
						}
				}
			
			protected	function MenuTable($_table_prefs,$_return_array)
				{
					register_use(__METHOD__);
					
					$this->payload	=	Safe::to_array(NubeData::$settings->menu_data);
					$structure		=	Safe::to_array(NubeData::$settings->menu_struc);
					
				//	echo printpre($structure);
				//	echo printpre($this->payload);
					
					$this->FolderStucture($structure);
				}
				
			public	function AllMenus($unique_id = false)
				{
					register_use(__METHOD__);
					
					if(!$unique_id)
						return;
					
					if(empty($this->payload[$unique_id]))
						return;
					
					$id			=	$this->payload[$unique_id]['ID'];
					$nubquery	=	nQuery();
					$query		=	$nubquery	->select()
												->from("main_menus")
												->where(array("ID"=>$id))
												->fetch();
												
					$data		=	(isset($query[0]))? $query[0]:false;
					$count_dirs	=	count(array_filter(explode("/",$data['full_path'])));
					
					if(!$data)
						return;
					
					AutoloadFunction('display_toggle_icon');
					include(NBR_RENDER_LIB.DS.'class.html'.DS.'ToolInspector'.DS.'AllMenus.php');	
				}
				
			protected	static	function UpdateCurrentMenu()
				{
					return new createCustMenuExt();
				}
			
			protected	static	function CreateNewMenu()
				{
					return new createCustMenu();
				}
		}