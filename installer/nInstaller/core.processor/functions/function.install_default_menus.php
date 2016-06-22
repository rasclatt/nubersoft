<?php
	function install_default_menus()
		{
			register_use(__FUNCTION__);
			AutoloadFunction('site_valid');
			if(!nApp::siteValid())
				return;
				
			AutoloadFunction('nQuery,FetchUniqueId');
			$query	=	nQuery();
			$query	->insert("main_menus")
					->columnsValues(array("unique_id","link","full_path","menu_name","usergroup","is_admin","page_live","session_status"), array("unique_id"=>FetchUniqueId(),"link"=>"admintools","full_path"=>"/admintools/","menu_name"=>"AdminTools","usergroup"=>1,"is_admin"=>1,"page_live"=>"on","session_status"=>'on'))
					->write();
			
			$query	->insert("main_menus")
					->columnsValues(array("unique_id","link","full_path","menu_name","page_live"),array("unique_id"=>FetchUniqueId(),"link"=>"","full_path"=>"/","menu_name"=>"Home","page_live"=>"on"))
					->write();
		}
?>