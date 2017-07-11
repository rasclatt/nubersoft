<?php
namespace nPlugins\Nubersoft\Accessibility;

class	Observer extends \Nubersoft\nApp
	{
		public	function listen()
			{
				if($this->getRequest('action') == 'nbr_set_accessible') {
					
					$is_toggled	=	$this->getSession('accessibility');
					
					if(!$is_toggled)
						$this->setSession('accessibility',true,true);
					else
						$this->getHelper('nSessioner')->destroy('accessibility');
				}
				
				$this->getHelper('nRouter')->addRedirect($this->localeUrl($this->getPageURI('full_path')));
			}
	}