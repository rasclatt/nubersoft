<?php
namespace Nubersoft;

class ValidateLoginState extends \Nubersoft\nApp
	{
		public		$payload,
					$valid;
		
		protected	$session_status,
					$layout;
		
		private		$loginPage,
					$permsPage;
		
		public	function validate($session_status = 'off')
			{
				$pageURI	=	$this->getPageURI();
				# Login required = 'on'
				$this->session_status	=	$session_status;
				$login_valid			=	true;
				# IF Page requires login
				if(!empty($this->session_status) && $this->session_status == 'on') {
					# If the usergroup is not set exit with base login header
					if(empty($this->isLoggedIn())) {
						$login_valid	=	false;
						$error			=	"loggedin";	
					}
					else {
						$usergroup	=	(isset($pageURI->usergroup))? $pageURI->usergroup : 0;
						# If user  is not admin
						if(!$this->getHelper('UserEngine')->allowIf($usergroup)) {
							$login_valid	=	false;
							$error			=	"permission";
						}
					}
				}
				
				if($login_valid == false) {
					$_incidental['permission']	=	$this->getFunction('error_message',$error);
					$this->login_required		=	true;
					return $this;
				}
					
				$this->login_required =	false;
				
				return $this;
			}
			
		public	function isRequired()
			{
				return $this->login_required;
			}
	}