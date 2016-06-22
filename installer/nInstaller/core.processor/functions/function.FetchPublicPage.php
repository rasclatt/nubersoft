<?php
	function FetchPublicPage($default = false)
		{
			register_use(__FUNCTION__);
			// Get the db
			$nubquery	=	nQuery();
			$host		=	"http//".$_SERVER['HTTP_HOST'];
			// Just return host
			if(!$nubquery)
				return $host;
			
			$nubquery	->select(array("menu_name","full_path"))
						->from("main_menus");
			// Select criteria chosen by user
			if(!empty($default))
				$nubquery->where($default);
			// If logged in, check for a page that has credentials less than loggedin
			elseif(!empty(NubeData::$settings->user->loggedin))
				$nubquery->addCustom("where usergroup >= '".Safe::encode(NubeData::$settings->user->usergroup)."' and page_live='on'");
			// All else fails, look for any page that is live and does not need a to be logged in
			else
				$nubquery->addCustom("where session_status != 'on' and page_live='on'");
			
			$results	=	$nubquery->fetch();

			return ($results == 0)? array("name"=>"HOME","url"=>"http://".$_SERVER['HTTP_HOST']): array("name"=>$results[0]['menu_name'],"url"=>$results[0]['full_path']);
		}
?>