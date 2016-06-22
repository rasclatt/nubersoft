<?php
namespace Nubersoft;

class nObserverMenus implements nObserver
	{
		public	static	function listen()
			{
				$menus	=	new \MenuEngine();
				$menus->FetchMenuData();
			}
	}