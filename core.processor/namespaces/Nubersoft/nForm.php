<?php
namespace Nubersoft;

class nForm
	{
		protected	static	$singleton;
		protected	$settings,
					$NBR_ROOT_DIR,
					$labelWrap;

		public	function __construct($dir = false)
			{
				// Create default root
				$this->setRootDir($dir);
				// Set the label as wrapped
				$this->labelPos();
				if(!empty(self::$singleton))
					return self::$singleton;
				
				self::$singleton	=	$this;
				
				return self::$singleton;
			}

		public	function labelPos($val = 'wrap')
			{
				$this->labelWrap	=	($val == 'wrap');
				return $this;
			}

		public	function setRootDir($dir)
			{
				$this->NBR_ROOT_DIR	=	(!empty($dir))? $dir : NBR_RENDER_LIB.'/class.html/nForm';
			}

		protected	function resetSettings()
			{
				return	array(
							'value'=>false,
							'name'=>false,
							'id'=>false,
							'class'=>false,
							'size'=>false,
							'class'=>false,
							'options'=>false,
							'style'=>false,
							'placeholder'=>false,
							'label'=>false,
							'selected'=>false,
							'disabled'=>false,
							'other'=>false
						);
			}

		protected	function processSettings($settings = false)
			{
				$default	=	(!empty($settings['default']))? preg_replace_callback('/[^:]{1,}[:]{2}[^:]{1,}/',function($v) {
					$exp	=	explode('::',$v[0]);
					switch($exp[0]) {
						case('SESSION'):
							return (isset($_SESSION[$exp[1]]))? $_SESSION[$exp[1]] : $v[0];
						case('POST'):
							return (!empty(\nApp::getPost($exp[1])))? \nApp::getPost($exp[1]) : $v[0];
						case('GET'):
							return (!empty(\nApp::getGet($exp[1])))? \nApp::getGet($exp[1]) : $v[0];
						case('REQUEST'):
							return (!empty(\nApp::getRequest($exp[1])))? \nApp::getRequest($exp[1]) : $v[0];
						case('SERVER'):
							return (isset($_SERVER[$exp[1]]))? $_SERVER[$exp[1]] : $v[0];
						case('FUNC'):
							return (function_exists($exp[1]))? $exp[1]() : $v[0];
					}
				},trim($settings['default'],'~')): '';
				
				$class			=	false;
				$name			=	(!empty($settings['name']))? $settings['name']: false;
				$value			=	(!empty($settings['value']))? $settings['value']: $default;
				$options		=	(!empty($settings['options']))? $settings['options']: array(array('','Select',true));
				$id				=	(!empty($settings['id']))? ' id="'.$settings['id'].'"': false;
				$size			=	(!empty($settings['size']))? $settings['size']: false;
				$type			=	(!empty($settings['type']))? $settings['type']: 'text';
				$style			=	(!empty($settings['style']))? ' style="'.$settings['style'].'"': false;
				$label			=	(!empty($settings['label']))? $settings['label']: false;
				$placeholder	=	(!empty($settings['placeholder']))? ' placeholder="'.$settings['placeholder'].'"': false;
				$selected		=	(!empty($settings['selected']))? ' selected' : false;
				$disabled		=	(!empty($settings['disabled']))? ' disabled': false;
				$other			=	(!empty($settings['other']))? ' '.$settings['other']: false;

				if(!empty($settings['class'])) {
					$class	=	' class="';
					$class	.=	(is_array($settings['class']))? implode(' ',$settings['class']): $settings['class'];
					$class	.=	'"';
				}

				$this->settings	=	array(
										'name'=>$name,
										'value'=>$value,
										'id'=>$id,
										'class'=>$class,
										'size'=>$size,
										'class'=>$class,
										'options'=>$options,
										'style'=>$style,
										'placeholder'=>$placeholder,
										'label'=>$label,
										'selected'=>$selected,
										'disabled'=>$disabled,
										'other'=>$other
									);
			}

		protected function includeFile($file)
			{
				if(is_file($file) && !empty($this->settings['name'])) {
					ob_start();
					include($file);
					$this->settings	=	$this->resetSettings();
					$data	=	ob_get_contents();
					ob_end_clean();
					return $data;
				}
			}

		protected	function getType($type)
			{
				return $this->NBR_ROOT_DIR.'/'.$type.'/index.php';
			}

		public	function checkBox($settings = false)
			{
				return $this->useLayout($settings,strtolower(__FUNCTION__),'chk');
			}

		public	function file($settings = false)
			{
				return $this->useLayout($settings,__FUNCTION__);
			}

		public	function fullHide($settings = false)
			{
				return $this->useLayout($settings,strtolower(__FUNCTION__));
			}

		public	function hidden($settings = false)
			{
				return $this->useLayout($settings,__FUNCTION__);
			}

		public	function text($settings = false)
			{
				return $this->useLayout($settings,__FUNCTION__,'mod');
			}

		public	function password($settings = false)
			{
				return $this->useLayout($settings,__FUNCTION__,'mod');
			}

		public	function textArea($settings = false)
			{
				return $this->useLayout($settings,strtolower(__FUNCTION__));
			}

		public	function radio($settings = false)
			{
				return $this->useLayout($settings,__FUNCTION__);
			}

		public	function select($settings = false)
			{
				return $this->useLayout($settings,__FUNCTION__);
			}
			
		public	function wrapper($settings = false)
			{
				return $this->useLayout($settings,__FUNCTION__);
			}

		protected	function useLayout($settings,$type,$layout = 'std')
			{
				// process the settings
				$this->processSettings($settings);
				switch($layout) {
					case('mod'):
						if($this->settings['size'])
							$this->settings['size']	=	' size="'.$this->settings['size'].'"';
						break;
					case('chk'):
						if($this->settings['selected'])
							$this->settings['selected']	=	' checked';
						break;
				}
				// Include the file
				return $this->includeFile($this->getType($type));
			}
	}