<?php
	class FormElementSelector
		{
			protected	static	function AssemblePrefs($settings = false)
				{
					$use['table']		=	(!empty($settings['table']))? trim($settings['table']): false;
					$use['payload']		=	(!empty($settings['payload']))? $settings['payload'] : array();
					$use['name']		=	(!empty($settings['name']))? 'name="'.trim($settings['name']).'"' : "";
					$use['id']			=	(!empty($settings['id']))? 'id="'.trim($settings['id']).'"' : "";
					$use['class']		=	(!empty($settings['class']))? 'class="'.trim($settings['class']).'"' : "";
					$use['label']		=	(!empty($settings['label']))? 'label="'.trim($settings['label']).'"' : "";
					$use['placeholder']	=	(!empty($settings['placeholder']))? 'placeholder="'.trim($settings['placeholder']).'"' : "";
					$use['checked']		=	(!empty($settings['checked']))? 'checked="checked"' : "";
					$use['disabled']	=	(!empty($settings['disabled']))? 'disabled="disabled"' : "";
					$use['size']		=	(!empty($settings['size']))? 'style="'.Safe::decode($settings['size']).'"' : "";
					
					return $use;
				}
				
			public	static	function Build($settings = false)
				{
					$kind	=	(!empty($settings['type']))? $settings['type']:'text';

					if($kind == 'hidden')
						formInputs::hidden($settings);
					elseif($kind == 'fullhide')
						formInputs::fullhide($settings);
					elseif($kind == 'password')
						formInputs::password($settings);
					elseif($kind == 'text')
						formInputs::text($settings);
					elseif($kind == 'disabled')
						formInputs::disabled($settings);
					elseif($kind == 'textarea')
						formInputs::textarea($settings);
					elseif($kind == 'checkbox')
						formInputs::checkbox($settings);
					elseif($kind == 'select')
						formInputs::select($settings);
					elseif($kind == 'radio')
						formInputs::radio($settings);
					elseif($kind == 'file')
						formInputs::fileInput($settings);
					else
						formInputs::text($settings);
				}
		}
?>