<?php
if(!function_exists("ajax_load_favicons")) {
	function ajax_load_favicons()
		{
			AutoloadFunction("get_directory_list,version_from_file");
			$imgs	=	get_directory_list(array("dir"=>NBR_ROOT_DIR,"type"=>array("ico","png"),"recursive"=>false));
			
			if(empty($imgs['root']))
				return "NO FAVICONS SET";
				
			foreach($imgs['root'] as $icns) {
				if(preg_match('/favicon\./',$icns))
					$fImg[]	=	'<img src="'.site_url().$icns.version_from_file(NBR_ROOT_DIR.$icns).'" style="margin: 5px; height: 40px; width: 40px; border: 1px solid #FFF; box-shadow: 1px 1px 8px #000;" />';
			}
			
			return (!empty($fImg))? implode("",$fImg) : '';
		}
}
header("Cache-control: max-age=0, must-revalidate");	
echo ajax_load_favicons();