<?php
	function app_blogger($unique_id = false)
		{
			register_use(__FUNCTION__);
			$unique_id	=	(isset($unique_id) && !empty($unique_id))? $unique_id:NubeData::$settings->page_prefs->unique_id;
			$blogger	=	new PostEngine();
			$blogger->FetchPostsByParent($unique_id)->prepare(array("subread"=>false));
			ob_start();
			echo $blogger->display;
			
			$blogger->ReplyForm();
			$data		=	ob_get_contents();
			ob_end_clean();
			
			return $data;
		}
?>