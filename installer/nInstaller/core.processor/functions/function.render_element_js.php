<?php
	function render_element_js($dir = false,$recursive = true,$useLocalUrl = true)
		{
			AutoloadFunction("get_directory_list");
			$dir		=	(!empty($dir))? $dir : CLIENT_DIR.'/js/';
			$use		=	($recursive)? array("dir"=>$dir,"type"=>array("js")) : array("dir"=>$dir,"type"=>array("js"),"recursive"=>false);
			$js			=	get_directory_list($use);
			$localUrl	=	"";
			if($useLocalUrl) {
					AutoloadFunction("site_url");
					$localUrl	=	site_url();
				}
			if(empty($js['root']))
				return;
				
			ob_start();
			foreach($js['root'] as $key => $val) {
?>
<script src="<?php echo $localUrl.$val; ?>?v=<?php echo date("ymdhis",filemtime($js['host'][$key])); ?>"></script>
<?php			}
			$data	=	ob_get_contents();
			ob_end_clean();
			
			return $data;
		}