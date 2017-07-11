<?php
namespace nPlugins\Nubersoft\Automator;

class Observer extends \Nubersoft\nApp implements \Nubersoft\nObserver
	{
		public	function listen()
			{
				# Get the token and decode it
				$token	=	json_decode($this->getHelper('nToken')->nOnceDecode($this->getRequest('automate'))->getValue(),true);
				
				
				# Stop if no action is present
				if(empty($token['action']))
					return;
				# Set permissions
				$permissions	=	(!empty($token['usergroup']))? $this->isGroupMember(ltrim($token['usergroup'],'NBR_')) :  true;
				# Loop through actions
				switch($token['action']) {
					case('download'):
						# If permissions aren't available stop
						if(!$permissions)
							break;
						# Download
						(new \nPlugins\Nubersoft\CoreDownloader())->getFile($token['file']);
						exit;
				}
			}
	}