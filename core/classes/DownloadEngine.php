<?php
	class	DownloadEngine
		{
			private	static	$singleton;
			
			private	function __construct()
				{
				}
			
			public	static	function init()
				{
					if(!isset(self::$singleton))
						self::$singleton	=	new Downloader();
					
					return self::$singleton;
				}
		}