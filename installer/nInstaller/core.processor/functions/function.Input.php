<?php
	function Input($values = false, $name = 'name', $size = false, $type = 'text', $dropdowns = array(),$placeholder = false,$label = false)
		{
			register_use(__FUNCTION__);
			$column		=	$name;
			(is_file($file = RENDER_LIB."/assets/form.inputs/".$type.".php"))? include($file) : include(RENDER_LIB."/assets/form.inputs/text.php");
			//echo $file;
		}