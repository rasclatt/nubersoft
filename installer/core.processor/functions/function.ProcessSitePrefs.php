<?php

	function ProcessSitePrefs($payload = false)
		{
			register_use(__FUNCTION__);
			AutoloadFunction('nQuery,FetchUniqueId,ProcessRequestsCustom');
			$payload			=	(is_array($payload))? $payload:$_POST;
	
			if(!isset($payload['set_type']))
				return;
	
			$nubquery			=	nQuery();
			$elemtype			=	$payload['set_type'];
			$count				=	$nubquery->select("COUNT(*) as count")->from("system_settings")->where(array("page_element"=>$elemtype))->fetch();
			$serial				=	json_encode($payload['setting'][$elemtype]);
			
			$prefs['content']	=	$serial;
			
			if($count[0]['count'] == 0) {
					$prefs['unique_id']	=	FetchUniqueId($_SESSION['ID']);
					$prefs['add']		=	true;
				}
			else {
					$prefs['unique_id']	=	$payload['unique_id'];
					$prefs['ID']		=	$payload['ID'];
					$prefs['update']	=	true;
				}
			
			$prefs['requestTable']	=	'system_settings';
			$prefs['page_element']	=	$elemtype;
			$prefs['name']			=	"settings";

			return $prefs;
		}
?>