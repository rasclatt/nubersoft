<?php
	function render_element_css($dir = false,$recursive = true,$useLocalUrl = true)
		{
			AutoloadFunction("get_directory_list");
			$dir		=	(!empty($dir))? $dir : CLIENT_DIR.'/css/';
			$use		=	($recursive)? array("dir"=>$dir,"type"=>array("css")) : array("dir"=>$dir,"type"=>array("css"),"recursive"=>false);
			$css		=	get_directory_list($use);
			$localUrl	=	"";
			if($useLocalUrl) {
					AutoloadFunction("site_url");
					$localUrl	=	site_url();
				}
			if(empty($css['root']))
				return;
				
			ob_start();
			foreach($css['root'] as $key => $val) {
?>
<link type="text/css" rel="stylesheet" href="<?php echo $localUrl.$val; ?>?v=<?php echo date("ymdhis",filemtime($css['host'][$key])); ?>" />
<?php			}
			$data	=	ob_get_contents();
			ob_end_clean();
			
			return $data;
		}