<?php
namespace Nubersoft\nRouter\View;

class Media extends \Nubersoft\nRouter
	{
		public	$mediaToken;
		
		public	function toStyleSheet($path)
			{
				$this->saveSetting('dynamic_content','css');
				$this->addHeader('Content-type: text/css');
				die($this->render($path));
			}
			
		public	function toJavaScript($path)
			{
				$this->saveSetting('dynamic_content','js');
				$this->addHeader('Content-type: text/javascript');
				die($this->render($path));
			}
		/*
		**	@description	Listens for dynamic media
		*/
		public	function listen()
			{
				$this->setErrorMode();
				# Get the controller key
				$encode	=	$this->getGet('controller');
				# No controller stop
				if(empty($encode)) {
					trigger_error('Can not get controller',E_USER_NOTICE);
					return false;
				}
				$TokenEngine	=	$this->getHelper('nToken');
				# Decode
				$decode	=	json_decode($this->safe()->decOpenSSL($encode,array('urlencode'=>true)),true);
				# Fetch the token Engine
				$Token	=	$TokenEngine->nOnce($this->safe()->decOpenSSL($decode['token']));
				# Get the token
				$token	=	$Token->getToken();
				# Path stored in value
				$path	=	$Token->getValue();
				switch($decode['type']) {
					case('css'):
						if(!empty($path) && is_file($path))
							$this->toStyleSheet($this->safe()->decode($path));
					case('js'):
						if(!empty($path) && is_file($path))
							$this->toJavaScript($this->safe()->decode($path));
				}
			}
	}