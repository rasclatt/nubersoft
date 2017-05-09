<?php
namespace Nubersoft;

class nObserverTemplate extends \Nubersoft\nApp implements nObserver
	{
		public	static	function listen()
			{
				$this->autoload(array('get_site_options'));
				$this->saveSetting('system', array("site"=>get_site_options()));
			}
	}