<?php

	class	formInputs
		{
			public	static	$inputArray;
			public	static	$key;
			public	static	$value;
			public	static	$size;
			public	static	$table;
			public	static	$payload;
			
			protected	static	function AssemblePrefs($settings = false)
				{
					$settings['table']			=	(!empty($settings['table']))? trim($settings['table']): false;
					$settings['payload']		=	(!empty($settings['payload']))? $settings['payload'] : array();
					$settings['name']			=	(!empty($settings['name']))? 'name="'.trim($settings['name']).'"' : "";
					$settings['id']				=	(!empty($settings['id']))? 'id="'.trim($settings['id']).'"' : "";
					$settings['class']			=	(!empty($settings['class']))? 'class="'.trim($settings['class']).'"' : "";
					$settings['label']			=	(!empty($settings['label']))? 'label="'.trim($settings['label']).'"' : "";
					$settings['placeholder']	=	(!empty($settings['placeholder']))? 'placeholder="'.trim($settings['placeholder']).'"' : "";
					$settings['checked']		=	(!empty($settings['checked']))? 'checked="checked"' : "";
					$settings['disabled']		=	(!empty($settings['disabled']))? 'disabled="disabled"' : "";
					$settings['size']			=	(!empty($settings['size']))? 'style="'.Safe::decode($settings['size']).'"' : "";
					
					return $settings;
				}
			
			public	static	function Initialize()
				{
					register_use(__METHOD__);
				}
			
			public	static	function hidden($settings = false)
				{
					register_use(__METHOD__);
					$settings	=	self::AssemblePrefs($settings);
					
					?>
                    
					<span style="float: left; font-size: 12px;"><?php echo (!empty(self::$value))? /* !m1 */Safe::decodeForm(self::$value): 'Empty'; ?></span>
					<input type="hidden" name="<?php echo Safe::decode(self::$key); ?>" value="<?php echo Safe::decodeForm(self::$value); ?>" /><?php
				}
				
			public	static	function fullHide($settings = false)
				{
					register_use(__METHOD__);
					
					$settings	=	self::AssemblePrefs($settings);
					?>
					<input type="hidden" name="<?php echo Safe::decode(self::$key); ?>" value="<?php echo self::$value; ?>" /><?php
				}
			
			public	static	function password($settings = false)
				{
					register_use(__METHOD__);
					$settings	=	self::AssemblePrefs($settings);
					?>
                    <?php if($label == true) { ?><label for="<?php echo Safe::decode(self::$key); ?>">Password</label><br /><?php } ?>
					<input type="password" name="<?php echo Safe::decode(self::$key); ?>" value="" size="<?php echo Safe::decode(self::$size); ?>" autocomplete="off" /><?php
				}
			
			public	static	function text($settings = false)
				{
					register_use(__METHOD__);
					$settings	=	self::AssemblePrefs($settings);
					
					ob_start();
					include(NBR_RENDER_LIB._DS_.'assets'._DS_.'form.inputs'._DS_.'text.php');
					$data	=	ob_get_contents();
					ob_end_clean();
					return $data;
				}
				
			public	static	function disabled($settings = false)
				{
					register_use(__METHOD__);
					$settings	=	self::AssemblePrefs($settings);
					
					ob_start();
					include(NBR_RENDER_LIB._DS_.'assets'._DS_.'form.inputs'._DS_.'disabled.php');
					$data	=	ob_get_contents();
					ob_end_clean();
					return $data;
				}
				
			public	static	function textarea($settings = false)
				{
					register_use(__METHOD__);
					$settings	=	self::AssemblePrefs($settings);
					
					ob_start();
					include(NBR_RENDER_LIB._DS_.'assets'._DS_.'form.inputs'._DS_.'textarea.php');
					$data	=	ob_get_contents();
					ob_end_clean();
					return $data;
				}
				
			public	static	function checkbox($settings = false)
				{
					register_use(__METHOD__);
					$settings	=	self::AssemblePrefs($settings);
					
					ob_start();
					include(NBR_RENDER_LIB._DS_.'assets'._DS_.'form.inputs'._DS_.'checkbox.php');
					$data	=	ob_get_contents();
					ob_end_clean();
					return $data;
				}
				
			public	static	function select($settings = false)
				{
					register_use(__METHOD__);
					$settings	=	self::AssemblePrefs($settings);
					ob_start();
					include(NBR_RENDER_LIB._DS_.'assets'._DS_.'form.inputs'._DS_.'select.php');
					$data	=	ob_get_contents();
					ob_end_clean();
					return $data;
				}
				
			public	static	function radio($settings = false)
				{
					register_use(__METHOD__);
					$settings	=	self::AssemblePrefs($settings);
					
					ob_start();
					include(NBR_RENDER_LIB._DS_.'assets'._DS_.'form.inputs'._DS_.'radio.php');
					$data	=	ob_get_contents();
					ob_end_clean();
					return $data;	
				}
				
			public	static	function fileInput($settings = false)
				{
					register_use(__METHOD__);
					$settings	=	self::AssemblePrefs($settings);
					
					ob_start();
					include(NBR_RENDER_LIB._DS_.'assets'._DS_.'form.inputs'._DS_.'file.php');
					$data	=	ob_get_contents();
					ob_end_clean();
					return $data;
				}
			
			public	static	function Compile($kind = false,$settings = false)
				{
					register_use(__METHOD__);
					AutoloadFunction("backtrace_file");
					
					echo printpre(backtrace_file());
					
					exit;
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