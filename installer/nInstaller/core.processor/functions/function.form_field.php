<?php
/*Title: form_field()*/
/*Description: This function creates `<form>` elements.*/

	function form_field($settings = array(),$nuber = false)
		{
			register_use(__FUNCTION__);
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
			$label			=	(!empty($settings['label']))? true:false;
			$column			=	$name;
			
			ob_start();
			if(empty($name))
				echo '<!-- NAME ATTRIBUTE IS REQUIRED -->';
			else
				(is_file($file = RENDER_LIB."/assets/form.inputs/".$type.".php"))? include($file) : include(RENDER_LIB."/assets/form.inputs/text.php");
			
			$data	=	ob_get_contents();
			ob_end_clean();
			
			return $data;
		}