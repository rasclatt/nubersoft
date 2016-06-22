<?php
/*Title: get_site_prefs()*/
/*Description: Retrieves the sites working preferences. Specify the `$return` to return just a specific set of prefs.*/
	function get_site_prefs($return = false)
		{
			if(!empty(NubeData::$settings->preferences)) {
				$prefs	=	(array) NubeData::$settings->preferences;
				if($return != false)
					return (isset($prefs[$return]))? (object) $prefs[$return]:false;
				else
					return (object) $prefs;
					
				return false;
			}
			 
			AutoloadFunction('nQuery,organize,create_default_prefs');
			$nubquery	=	nQuery();
			$register	=	new RegisterSetting();
			
			if(!$nubquery) {
				$register->UseData('critical',array('nubquery_engine'=>false))->SaveTo('settings');	
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
					return (object) $prefs[$return];
			}
			
			$register->UseData('preferences',$prefs)->SaveTo('settings');	
			
			return (object) $prefs;
		}