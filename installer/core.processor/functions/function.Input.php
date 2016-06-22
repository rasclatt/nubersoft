<?php
	function Input($values = false, $name = 'name', $size = false, $type = 'text', $dropdowns = array(),$placeholder = false,$label = false)
		{
			// Create a recursive search for arrayed inputs
			AutoloadFunction("fullRecFind");
			if(strpos($name,'[') !== false) {
				$sColumn	=	explode('[',$name);
				$key		=	array_shift($sColumn);

				if(!empty($values[$key])) {
					$jSon = json_decode(Safe::decodeSingle($values[$key]),true);
					if(is_array($jSon)) {
						$values[$name]	=	fullRecFind($name,$jSon);
					}
				}
			}
			
			$column		=	$name;
			(is_file($file = NBR_RENDER_LIB."/assets/form.inputs/".$type.".php"))? include($file) : include(NBR_RENDER_LIB."/assets/form.inputs/text.php");
		}