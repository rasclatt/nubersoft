<?php
	function get_page_options()
		{
			$opts	=	(!empty(NubeData::$settings->page_prefs->page_options))? NubeData::$settings->page_prefs->page_options : false;
			
			if(empty($opts))
				return false;
				
			$vals	=	@json_decode($opts,true);

			return (!empty($vals))? $vals : $opts;
		}