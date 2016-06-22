<?php
/*Title: get_bypass()*/
/*Description: This function just get the bypass elements for the site.*/

	function get_bypass($type = false)
		{
			register_use(__FUNCTION__);
			
			if(!isset(NubeData::$settings->bypass)) {
					$register		=	new RegisterSetting();
					AutoloadFunction('get_site_prefs');
					$array			=	nApp::getSitePrefs('site');
					$data			=	(isset($array->content))? $array->content:false;
					$new['login']	=	(!isset($data->login))? false:$data->login;
					$new['head']	=	(!isset($data->head))? false:$data->head;
					$new['menu']	=	(!isset($data->menu))? false:$data->menu;
					$new['foot']	=	(!isset($data->foot))? false:$data->foot;
					$register->UseData('bypass',$new)->SaveTo('settings');
					
					return (isset($new[$type]))? (object) $new[$type] : (object) $new;
				}
				
			$prefs	=	(array) NubeData::$settings->bypass;
			
			return (isset($prefs[$type]))? $prefs[$type]: $prefs;
		}
?>