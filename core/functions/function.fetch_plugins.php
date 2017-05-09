<?php
	function fetch_plugins()
		{
			if(!isset(NubeData::$settings->plugins)) {
				AutoloadFunction('get_directory_list');
				$register			=	new RegisterSetting();
				$auto				=	get_directory_list(array("dir"=>NBR_PLUGINS.'/'));
				$admin				=	get_directory_list(array("dir"=>NBR_CLIENT_DIR.'/settings/plugins/admintools/'));
				$general			=	get_directory_list(array("dir"=>NBR_CLIENT_DIR.'/settings/plugins/general/'));
				$auto['list']		=	(isset($auto['list']))? $auto['list']:array();
				$admin['list']		=	(isset($admin['list']))? $admin['list']:array();
				$general['list']	=	(isset($general['list']))? $general['list']:array();
				
				if(empty($auto['list']) && empty($admin['list']) && empty($general['list']))
					return false;
				
				$array				=	array();
				$admin_tasks		=	array_merge($auto['list'],$admin['list']);
				$user_tasks			=	$general['list'];
				
				if(!empty($admin_tasks)) {
					$aCount	=	count($admin_tasks);
					for($i = 0; $i < $aCount; $i++) {
						$array['admin_root'][]	=	$admin_tasks[$i];
						$array['admin_local'][]	=	str_replace(NBR_ROOT_DIR,"",$admin_tasks[$i]);
					}
				}
				
				if(!empty($user_tasks)) {
					$uTasksCnt	=	count($user_tasks);
					for($i = 0; $i < $uTasksCnt; $i++) {
						$array['user_root'][]	=	$user_tasks[$i];
						$array['user_local'][]	=	str_replace(NBR_ROOT_DIR,"",$user_tasks[$i]);
					}
				}
				
				$register->UseData('plugins',$array)->SaveTo('settings');
			}
			
			return (isset(NubeData::$settings->plugins))? (array) NubeData::$settings->plugins : false;
		}