<?php
	function render_inline_css($settings = false)
		{
			$cssArray		=	(!empty($settings['css']))? $settings['css'] : false;
			$decode			=	(!empty($settings['decode']))? $settings['decode'] : false;
			$component_id	=	(!empty($settings['ID']))? $settings['ID'] : false;
			
			
			
			if(!$cssArray && !$cssArray)
				return;
				
			if($component_id != false) {
					AutoloadFunction('nQuery');
					$nubquery	=	nQuery();
					
					$id		=	$nubquery	->select("css")
											->from("components")
											->where(array("ID"=>$component_id))
											->fetch();
					
					if($id != 0) {
							if(isset($id[0]['css']) && !empty($id[0]['css'])) {
									$cssArray	=	$id[0]['css'];
									$decode		=	true;
								}
						}
				}
				
			// jSON decode if requested
			$css	=	(!empty($cssArray) && $decode)? json_decode($cssArray,true) : $cssArray;

			// Return whatever remains
			if(!empty($css)) {
					foreach($css as $key => $value) {
							$value	=	trim($value);
							if(!empty($value))
								$new[]	=	$key.": ".$value;
						}
					
					return (!empty($new))? 'style="'.implode("; ",$new).';"':"";
				}
		}
?>