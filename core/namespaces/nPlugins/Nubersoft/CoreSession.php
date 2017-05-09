<?php
namespace nPlugins\Nubersoft;

class CoreSession extends \Nubersoft\nSessioner
	{
		/*
		**	@description	Meant to force user to home page if they are logged in as non-admin
		*/
		public	function checkUserStatus()
			{
				$nApp	=	\Nubersoft\nApp::call();
				if($nApp->getPageURI('is_admin') == 1) {
					if($nApp->isLoggedIn() && !$nApp->isAdmin())
						$nApp->getHelper('nRouter')->addRedirect($nApp->siteUrl());
				}
			}
	}