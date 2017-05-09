<?php

	class GetSitePrefs
		{
			public	static	$site;
			public	static	$footer;
			public	static	$header;

			public	static	function Fetch($fetch = false)
				{
					register_use(__METHOD__);
					
					AutoloadFunction('get_site_prefs');
					$prefs			=	nApp::getSitePrefs($fetch);
					
					self::$site		=	(!empty($prefs->site))? $prefs->site : false;
					self::$header	=	(!empty($prefs->header))? $prefs->header : false;
					self::$footer	=	(!empty($prefs->footer))? $prefs->footer : false;
				}
		}
?>