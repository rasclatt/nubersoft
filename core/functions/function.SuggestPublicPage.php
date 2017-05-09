<?php
	function SuggestPublicPage($search = false,$home = false)
		{
			
			AutoloadFunction("site_url");
			$host		=	site_url();
			$dReturn	=	($home)? array(array("menu_name"=>"HOME","full_path"=>$host)) : false;
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
							if(is_array($val['mSearch'][$i]))
								$new	=	array_merge($new,$val['mSearch'][$i]);
						}
					}
					
					$new	=	organize($new,'menu_name',true);
					
					return (!empty($new))? $new:$dReturn;
				}
			
			AutoloadFunction("is_loggedin");
			$sql				=	array();
			$params				=	false;
			$sql[]				=	"SELECT `".implode("`,`",array("menu_name","full_path","page_live"))."` from `main_menus`";
			$inc_adm			=	(is_admin())? "":" and `session_status` != 'on'";
			// Select criteria chosen by user
			if(!empty($search)) {
				$sql[]			=	"WHERE (`link` like :0 or `menu_name` like :1) and `page_live` = 'on'{$inc_adm}";
				$params[":0"]	=	"%".$search."%";
				$params[":1"]	=	"%".$search."%";
			}
			// If logged in, check for a page that has credentials less than loggedin
			elseif(!empty(is_loggedin())) {
				$sql[]			=	"WHERE `usergroup` >= :2 and `page_live` = 'on'{$inc_adm}";
				$params[":2"]	=	NubeData::$settings->user->usergroup;
			}
			// All else fails, look for any page that is live and does not need a to be logged in
			else {
				$sql[]			=	"WHERE `session_status` != 'on' and `page_live` = 'on'{$inc_adm}";
			}
			
			$results	=	nQuery()->query(implode(" ",$sql),$params)->getResults();
			
			return ($results == 0)? false : $results;
		}