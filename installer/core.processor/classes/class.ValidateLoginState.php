<?php

	class ValidateLoginState
		{
			public		$payload;
			public		$valid;
			public		$login_required;
			
			protected	$session_status;
			protected	$nuber;
			protected	$nubquery;
			protected	$layout;
			
			private		$loginPage;
			private		$permsPage;
			
			public	function __construct()
				{
					AutoloadFunction('nQuery');
					$this->nubquery	=	nQuery();
				}
			
			public	function Validate($session_status = 'off')
				{
					//	Login required = 'on'
					$this->session_status	=	$session_status;
					$login_valid			=	true;
					
					global	$_incidental;
					// IF Page requires login
					if(isset($this->session_status) && $this->session_status == 'on') {
							// If the usergroup is not set exit with base login header
							if(NubeData::$settings->user->loggedin == false) {
									$login_valid	=	false;
									$error			=	"loggedin";	
								}
							else {
									AutoloadFunction('allow_if');
									// If user  is not admin
									if(!allow_if(NubeData::$settings->page_prefs->usergroup)) {
											$login_valid	=	false;
											$error			=	"permission";
										}
								}
						}
					
					if($login_valid == false) {
							AutoloadFunction('error_message');
							global	$_incidental;
							$_incidental['permission']	=	error_message($error);
							$this->login_required		=	true;
							return $this;
						}
						
					$this->login_required =	false;
					
					return $this;
				}
			
			public	function useLayout($useLayout = false,$def = 'd')
				{
					// P for permissions page
					if($def == 'p')
						$this->permsPage	=	$useLayout;
					else
						$this->loginPage	=	$useLayout;
						
					return $this;
				}
			
			public	function LoginPage()
				{
					$defLayout			=	NBR_RENDER_LIB.'/assets/login/dialogue.php';
					$defPerms			=	NBR_RENDER_LIB.'/assets/form.bad.permissions.php';
					
					$this->loginPage	=	(!empty($this->loginPage))? $this->loginPage : $defLayout;
					$this->permsPage	=	(!empty($this->permsPage))? $this->permsPage : $defPerms;
					
					// If login is required
					if($this->login_required) {
							// If not logged in
							if(!NubeData::$settings->user->loggedin) {
									// Insert login Form
									include_once($this->loginPage);
									// If required to login
									$this->valid	= false;
								}
							// If logged in
                            else {
									global $_error;
									$_error['login'][]	= "PERMISSION DENIED.";
									// Notify permissions not good enough for viewing content
									include_once($this->permsPage);	
									// If required to login
									$this->valid	= false;
								}
						}
					else
						$this->valid	=	true;
					
					return $this;
				}
		}