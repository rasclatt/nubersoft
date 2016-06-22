<?php
	function render_thumb_previewer($file = false, $resource = false,$thumb = 60)
		{
			if(!$file)
				return false;
			
			$icn	=	(is_file($thmb = THUMB_DIR."/".basename($file)))? $thmb:$file;
			$icn	=	str_replace(ROOT_DIR,"",$icn);
			$info	=	false;
			
			if($resource) {
				AutoloadFunction('get_file_type');
				$doctype	=	get_file_type($file,$resource);
				if((isset($doctype->type) && $doctype->type != 'image') || (!isset($doctype->type)))
					return false;
				
				$info	=	(function_exists("getimagesize"))? getimagesize($file) : false;
			}
			ob_start();
?>			<div style="margin: 5px;" class="transpattern">
				<div style="background-image: url('<?php echo $icn; ?>'); background-repeat: no-repeat; background-size: contain; border: 1px solid #888; height: <?php echo $thumb; ?>px; width: <?php echo $thumb; ?>px; background-position: center;"></div>
			</div><?php
			$data	=	ob_get_contents();
			ob_end_clean();
			
			return array("layout"=>$data,"dimensions"=>$info);									
		}