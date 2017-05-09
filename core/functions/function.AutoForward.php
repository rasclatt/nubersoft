<?php
/*Title: AutoForward()*/
/*Description: This will check if the page is supposed to auto-forward using a `header("Location: ")` command.*/
	function AutoForward()
		{	
			$prefs		=	nApp::getPage();
			
			if(!nApp::siteValid() || !$prefs)
				return;
				
			$auto_fwd	=	(!empty($prefs->auto_fwd))? $prefs->auto_fwd:false;
			$post_redir	=	(isset($prefs->auto_fwd_post) && $prefs->auto_fwd_post == 'on');
			// If autoforward set
			if($auto_fwd != false) {
				$forward	=	Safe::decode($auto_fwd);
				// If the path of the forward is the same as the path of the directory,
				// return or there will be a failed infinite loop
				if($prefs->auto_fwd ==  $prefs->full_path)
					return;
				// if forward is on
				if(!empty($forward)) {
					if($post_redir) {
						if(nApp::loggedInNotAdmin()) {
							header('Location: '.$auto_fwd);
							exit;
						}
					}
					// Else if not checked
					else {
						// If usergroup not admin
						if(nApp::loggedInNotAdmin() || !is_loggedin()) {
							header('Location: '.$auto_fwd);
							exit;
						}
					}
				}
			}
		}