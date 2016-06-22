<?php
	class AdminToolsMaster
		{
			protected	$nuber;
			protected	$nubquery;
			protected	$nubsql;
			
			public	function __construct()
				{
					AutoloadFunction('is_admin,nQuery,create_query_string,fetch_token');
					$this->nubquery	=	nQuery();
					register_use(__METHOD__);
				}
				
			public	function MenuBar($db_dir = '/core.processor/includes/')
				{
					register_use(__METHOD__);
					if(is_admin()) {
							$token_reinstall	=	(!isset($_SESSION['token']['reinstall']))? fetch_token('reinstall'):$_SESSION['token']['reinstall'];
							include(RENDER_LIB.'/class.html/AdminToolsMaster/MenuBar.php');
						}
				}
		
		// Add the plugins row
		public	function Plugins($plugins_array = array())
			{
				register_use(__METHOD__);
				if($this->nubquery == false)
					return;
				AutoloadFunction('get_tables_in_db');
				include(RENDER_LIB.'/class.html/AdminToolsMaster/Plugins.php');
			}
			
			
		public	function MastHead()
			{
				register_use(__METHOD__);
				include(RENDER_LIB.'/class.html/AdminToolsMaster/MastHead.php');
			}
			
		public	function CSS()
			{
				register_use(__METHOD__);
				include(RENDER_LIB.'/class.html/AdminToolsMaster/CSS.php');
			}
	}