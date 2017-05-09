<?php
namespace nPlugins\Nubersoft\View;

use \Nubersoft\nApp as nApp;

class User extends \Nubersoft\UserEngine
	{
		public	function getUserFirst()
			{
				if(!nApp::call()->isLoggedIn())
					return;
				
				$user	=	nApp::call()->getSession('first_name');
				
				if(!empty($user))
					$user	=	'Hello, '.$user;
				
				$display	=	'
			<div class="nbr_popwrap">'.$user.'
				<div class="nbr_popup">
					<a class="menu_pop" href="'.nApp::call()->localeUrl('/account/').'">Account</a><br />
					<a class="menu_pop" href="?action=logout">Sign Out?</a>
				</div>
			</div>';
					
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