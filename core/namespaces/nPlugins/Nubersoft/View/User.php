<?php
namespace nPlugins\Nubersoft\View;

use \Nubersoft\nApp as nApp;

class User extends \Nubersoft\UserEngine
	{
		public	function getUserFirst()
			{
				$display	=	nApp::call()->getHelper('nRender')->useTemplatePlugin('nbr_user_welcome_container');
				
				if(nApp::call()->isAjaxRequest())
					nApp::call()->ajaxResponse(array(
						'html'=>array(
							$display
						),
						'sendto'=>array(
							'#user_name_loggedin'
						)
					));
				else
					return $display;
			}
	}