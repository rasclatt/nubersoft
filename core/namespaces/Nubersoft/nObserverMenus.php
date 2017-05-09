<?php
namespace Nubersoft;

class nObserverMenus implements nObserver
	{
		public	function listen()
			{
				$menus	=	new \nPlugins\Nubersoft\MenuEngine();
				$menus->FetchMenuData();
			}
	}