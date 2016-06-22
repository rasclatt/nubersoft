<?php
	class	MenuEngine
		{
			public		$data;
			public		$current;
			public		$current_menu;
			public		$menu_dir;
			
			protected	$register;
			protected	static	$singleton;
			
			public	function __construct()
				{
					register_use(__METHOD__);
					
					$this->register	=	new RegisterSetting();
					
					if(!self::$singleton) {
						// Create singleton
						self::$singleton	=	$this;
						return self::$singleton;
					}
    				else
						return self::$singleton;
				}
			
			public	function FetchMenuData($menu = false)
				{
					register_use(__METHOD__);
					
					AutoloadFunction("menu_get_all");
					$this->data		=	menu_get_all($menu);
					$this->register->UseData('menu_data',organize($this->data,'unique_id'))->SaveTo('settings');
					$this->OrganizeById();
					
					return $this;
				}
				
			public	function UseLayout($layout = false)
				{
					register_use(__METHOD__);
					
					if(is_file($layout)) {
						include($layout);
					}
					
					return $this;
				}
				
			protected	function OrganizeById()
				{
					register_use(__METHOD__);
					
					if(!empty($this->data)) {
						AutoloadFunction('menu_organize_id');
						$register		=	new RegisterSetting();
						$menu_layout	=	menu_organize_id($this->data);
						$this->register->UseData('menu_struc',$menu_layout)->SaveTo('settings');
						$this->GetBaseMenu();
						return $this;
					}
					else
						$this->register->UseData('menu_struc',array())->SaveTo('settings');
				}
			
			protected	function GetBaseMenu()
				{	
					$id	=	nApp::getPage('unique_id');
					
					if(empty(\nApp::getDataNode('menu_struc')) || !$id)
						return $this;
					
					
					$iterated	=	new ArrayObject(\nApp::nFunc()->toArray(\nApp::getDataNode('menu_struc')));
					
					foreach($iterated as $keys => $values) {
						$struct[$keys]	=	$values;
					}
					
					$new				=	new RecursiveIteratorIterator(
												new RecursiveArrayIterator($struct),
												RecursiveIteratorIterator::SELF_FIRST
											);
					$this->current_menu	=	false;
					$this->menu_dir		=	array();
					
					// Cycle through saved objects and assign temp for processing
					$menu_struc	=	\nApp::nFunc()->toArray(\nApp::getDataNode('menu_struc'));
					$menu_data	=	\nApp::nFunc()->toArray(\nApp::getDataNode('menu_data'));
					
					foreach($new as $key => $value) {
						if(isset($menu_struc[$key])) {
							$current			=	$key;
							$set[$current][]	=	$key;
							$dirs[$current][]	=	(isset($menu_data[$key]['link']))? $menu_data[$key]['link'] : false;
						}
						else {
							$set[$current][]	=	$key;
							$dirs[$current][]	=	(isset($menu_data[$key]['link']))? $menu_data[$key]['link'] : false;
						}
						
						if(!$this->current_menu) {
							if($key == $id) {
								$this->current_menu	=	(isset($menu_data[$current]))? $menu_data[$current] : false;
							}
						}
							
						$this->menu_dir[$current]	=	$dirs[$current];
					}

					$this->BuildDirectoryStructure();
	
					if(isset($set))
						$this->register->UseData('menu_hiearchy',$set)->SaveTo('settings');
						
					$this->register->UseData('menu_current',$this->current_menu)->SaveTo('settings');
					
					return $this;
				}
			
			protected	function BuildDirectoryStructure()
				{
					register_use(__METHOD__);
					
					$alt	=	array();
					foreach($this->menu_dir as $key => $array) {
						$str	=	"/";
						$aCount	=	count($array);
						for($i = 0; $i < $aCount; $i++) {
							$str			.=	$array[$i]."/";
							$new[$key][]	=	str_replace(_DS_._DS_,_DS_,$str);
						}
							
						$alt	=	array_merge($alt,$new[$key]);
					}
						
					$this->register->UseData('menu_dir',$alt)->SaveTo('settings');
				}
		}