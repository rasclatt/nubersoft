<?php
	function SuggestPublicPage($search = false)
		{
			register_use(__FUNCTION__);
			AutoloadFunction("site_url");
			$dReturn	=	array(array("menu_name"=>"HOME","full_path"=>site_url()));
			if(is_array($search) && !empty($search)) {
					foreach($search as $term) {
						$subterm	=	array_filter(explode("/",$term));
						if(!empty($subterm)) {
							foreach($subterm as $subval)
								$val['mSearch'][]	=	SuggestPublicPage(str_replace("/","",substr($subval,0,3)));
						}
						else
							$val['mSearch'][]	=	SuggestPublicPage(str_replace("/","",substr($term,0,3)));
					}
						
					$new	=	array();
					if(!empty($val['mSearch'])) {
						$sQcount	=	count($val['mSearch']);
						for($i = 0; $i < $sQcount; $i++) {
							$new	=	array_merge($new,$val['mSearch'][$i]);
						}
					}
					
					$new	=	organize($new,'menu_name',true);
					
					return (!empty($new))? $new:$dReturn;
				}
			
			// Get the db
			$nubquery	=	nQuery();
			$host		=	site_url();
			// Just return host
			if(!$nubquery)
				return $host;
			
			$nubquery	->select(array("menu_name","full_path"))
						->from("main_menus");
			
			$inc_adm	=	(is_admin())? "":" and session_status != 'on'";
					
			// Select criteria chosen by user
			if(!empty($search)) {
				$nubquery	->like(array('like'=>$search,'columns'=>array("link","menu_name")))
							->addCustom("and (page_live='on'{$inc_adm})");
			}
			// If logged in, check for a page that has credentials less than loggedin
			elseif(!empty(NubeData::$settings->user->loggedin))
				$nubquery->addCustom("where usergroup >= '".Safe::encode(NubeData::$settings->user->usergroup)."' and page_live='on'{$inc_adm}");
			// All else fails, look for any page that is live and does not need a to be logged in
			else
				$nubquery->addCustom("where session_status != 'on' and page_live='on'{$inc_adm}");
			
			$results	=	$nubquery->fetch();
			
			return ($results == 0)? $dReturn : $results;
		}