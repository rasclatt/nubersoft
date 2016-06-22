<?php
/*Title: form_field()*/
/*Description: This function creates `<form>` elements.*/

	function form_field($settings = array())
		{
			
			AutoloadFunction('check_empty');
			$values			=	(isset($settings['values']))? $settings['values']:"";
			$name			=	(isset($settings['name']))? $settings['name']:"";
			$size			=	(isset($settings['size']))? $settings['size']:"";
			$type			=	(isset($settings['type']))? $settings['type']:"text";
			$dropdowns		=	(isset($settings['dropdowns']))? $settings['dropdowns']:array();
			$disabled		=	(check_empty($settings,'disabled',true))? ' disabled="disabled" disabled':"";
			$ElemId			=	(!empty($settings['id']))? ' id="'.$settings['id'].'"':"";
			$placeholder	=	(!empty($settings['placeholder']))? ' placeholder="'.$settings['placeholder'].'"':"";
			$ElemData		=	(!empty($settings['data']) && !empty($ElemId))? ' data-'.$settings['id'].'="'.$settings['datq'].'"':"";
			$label			=	(!empty($settings['label']));
			$column			=	$name;
			
			ob_start();
			if(empty($name))
				echo '<!-- NAME ATTRIBUTE IS REQUIRED -->';
			else
				(is_file($file = NBR_RENDER_LIB._DS_.'assets'._DS_.'form.inputs'._DS_.$type.".php"))? include($file) : include(NBR_RENDER_LIB._DS_.'assets'._DS_.'form.inputs'._DS_.'text.php');
			
			$data	=	ob_get_contents();
			ob_end_clean();
			
			return $data;
		}