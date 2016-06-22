<?php

	class nGet
		{
			private	static	$singleton;
			
			public	function __construct()
				{
					if(empty(self::$singleton))
						self::$singleton	=	$this;
					
					return self::$singleton;
				}
			// This will retrieve core elements from the NubeData
			public	function getCoreElement($type = false)
				{
					if(empty($type)) {
						return (!empty(NubeData::$settings))? NubeData::$settings : false;
					}

					switch($type) {
							case ('pref'):
								if(isset(NubeData::$settings->preferences))
									return NubeData::$settings->preferences;
							case ('menu'):
								if(isset(NubeData::$settings->menu_data)) {
									$menu['menu_data']		=	(isset(NubeData::$settings->menu_data))? NubeData::$settings->menu_data : false;
									$menu['menu_struc']		=	(isset(NubeData::$settings->menu_struc))? NubeData::$settings->menu_struc : false;
									$menu['menu_dir']		=	(isset(NubeData::$settings->menu_dir))? NubeData::$settings->menu_dir : false;
									$menu['menu_hiearchy']	=	(isset(NubeData::$settings->menu_hiearchy))? NubeData::$settings->menu_hiearchy : false;
									$menu['menu_current']	=	(isset(NubeData::$settings->menu_current))? NubeData::$settings->menu_current : false;

									return Safe::to_object($menu);
								}
							case ('page'):
								if(isset(NubeData::$settings->page_prefs))
									return NubeData::$settings->page_prefs;
							case ('site'):
								if(isset(NubeData::$settings->site))
									return NubeData::$settings->site;
							case ('user'):
								if(isset(NubeData::$settings->user))
									return NubeData::$settings->user;
							case ('connection'):
								if(isset(NubeData::$settings->connection))
									return NubeData::$settings->connection;
							case ('head'):
								if(isset(NubeData::$settings->preferences))
									return NubeData::$settings->preferences;
							case ('engine'):
								if(isset(NubeData::$settings->engine))
									return NubeData::$settings->engine;
							case ('plugin'):
								if(isset(NubeData::$settings->plugin))
									return NubeData::$settings->plugin;
							case ('prefs'):
								if(isset(NubeData::$settings->preferences))
									return NubeData::$settings->preferences;
							case ('bypass'):
								if(isset(NubeData::$settings->bypass))
									return NubeData::$settings->bypass;
						}

					return false;
				}
			// This will get the site prefs from NubeData
			public	function getSitePrefs($refresh = false)
				{
					$prefs	=	$this->getCoreElement('pref');
					
					if(!empty($prefs->site) && !$refresh)
						return $prefs->site;
					else {
						$make	=	$this->getSetSitePrefs(true);
						$prefs	=	$this->getCoreElement('pref');
						return (!empty($prefs->site))? $prefs->site : false;
					}
					
				}
			//  This will fetch the company logo from the NubeData
			public	function getSiteLogo()
				{
					$prefs	=	$this->getSitePrefs();
					if(!empty($prefs) && !empty($prefs->content->companylogo))
						return $prefs->content->companylogo;
						
					return false;
				}
			
			public	function getMenu($type = 'menu_current')
				{
					if(!empty($this->getCoreElement('menu')->{$type}))
						return $this->getCoreElement('menu')->{$type};
				}
			
			public	function getIncidentals()
				{
					if(!empty(NubeData::$incidentals))
						return NubeData::$incidentals;
				}
				
			public	function getErrors()
				{
					if(!empty(NubeData::$errors))
						return NubeData::$errors;
				}
			
			public	function getPage($var = false)
				{
					if(empty(CoreMySQL::$CoreAttributes))
						CoreMySQL::Initialize(true);
					
					if(CoreMySQL::$CoreAttributes) {
						$array	=	Safe::to_object(CoreMySQL::$CoreAttributes);
						if(!empty($array)) {
							if(!empty($var))
								return (isset($array->{$var}))? $array->{$var} : false;
							else
								return $array;
						}
					}
					
					return false;
				}
				
			public	function getSite($var = false)
				{
					$val	=	$this->getCoreElement('site');
					if(!empty($val)) {
						return ($var)? $val->{$var} : $val;
					}
				}
			
			public	function getSetSitePrefs($refresh = false)
				{
					if(!$refresh) {
						if(!empty($this->getCoreElement('head'))) {
							$prefs	=	Safe::to_array($this->getCoreElement('head'));
							if($return != false)
								return (isset($prefs[$return]))? Safe::to_object($prefs[$return]) : false;
							else
								return (is_array($prefs))? Safe::to_object($prefs) : false;
						}
					}
					 
					AutoloadFunction('nQuery,organize,create_default_prefs');
					$nubquery	=	nQuery();
					if(!$nubquery) {
						RegistryEngine::saveSetting('critical',array('nubquery_engine'=>false));
						RegistryEngine::saveError('database',array('nubquery_engine'=>false));	
						return false;
					}

					$exists		=	$nubquery	->select("COUNT(*) as count")
												->from("system_settings")
												->where(array("name"=>"settings"))
												->fetch();
		
					// Create the prefs
					if($exists[0]['count'] == 0 || !isset($exists[0]['count'])) {
						create_default_prefs();
					}
						
					$vals		=	organize($nubquery	->select()
														->from("system_settings")
														->where(array("name"=>"settings"))
														->fetch(),
														'page_element');
					
					if(empty($vals)) {
						RegistryEngine::saveError("invalid_table","system_settings");
						return false;
					}
					
					foreach($vals as $name => $settings) {
						$vals[$name]['content']	=	json_decode($vals[$name]['content']);
					}
						
					$prefs['site']		=	(isset($vals['settings_site']))? $vals['settings_site']:false;
					$prefs['header']	=	(isset($vals['settings_head']))? $vals['settings_head']:false;
					$prefs['footer']	=	(isset($vals['settings_foot']))? $vals['settings_foot']:false;
					
					if(in_array(false,$prefs)) {
						$key	=	array_keys($prefs,false);
						
						if(!empty($key)) {
							foreach($key as $try) {
								create_default_prefs($try);
							}
						}
					}
					
					if($return) {
						if(isset($prefs[$return]))
							return Safe::to_object($prefs[$return]);
					}
					
					RegistryEngine::saveSetting('preferences',$prefs);	
					
					return Safe::to_object($prefs);
				}
		}