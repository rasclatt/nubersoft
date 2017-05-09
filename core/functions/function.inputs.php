<?php
	function inputs($settings = false)
		{
			$default		=	(!empty($settings['default']))? $settings['default'] : '';
			$values			=	(!empty($settings['values']))? $settings['values'] : $default;
			$name			=	(!empty($settings['name']))? $settings['name'] : '';
			$size			=	(!empty($settings['size']))? $settings['values'] : '';
			$type			=	(!empty($settings['type']))? $settings['type'] : 'text';
			$dropdowns		=	(!empty($settings['options']))? $settings['options'] : false;
			$placeholder	=	(!empty($settings['placeholder']))? $settings['placeholder'] : '';
			$label			=	(!empty($settings['label']))? $settings['label'] : '';
			$id				=	(!empty($settings['id']))? $settings['id'] : '';
			$class			=	(!empty($settings['class']))? $settings['class'] : '';
			
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
			(is_file($file = NBR_RENDER_LIB.DS.'assets'.DS.'form.inputs'.DS.$type.".php"))? include($file) : include(NBR_RENDER_LIB.DS.'assets'.DS.'form.inputs'.DS.'text.php');
		}