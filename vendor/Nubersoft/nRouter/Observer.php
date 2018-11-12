<?php
namespace Nubersoft\nRouter;

class Observer extends \Nubersoft\nRouter\Controller implements \Nubersoft\nObserver
{
	public	function listen()
	{
	}
	/**
	 *	@description	Checks the last activity in the session and destroys old session if past timeout
	 */
	public	function checkLastActive()
	{
		# Fetch session obj
		$Session	=	$this->getHelper('nSession');
		# Get the current active time
		$time		=	$Session->get('LAST_ACTIVE');
		# If there is none, it hasn't been set yet, stop
		if(empty($time))
			return false;
		# If the current time is past the timeout
		if($time < strtotime('now')) {
			# Destroy session
			$Session->destroy();
			# If the user is actually logged in
			if($this->isLoggedIn()) {
				# Notify the user (request dependent)
				if(!$this->isAjaxRequest())
					$this->redirect($this->localeUrl($this->getPage('full_path')));
				else
					$this->ajaxResponse([
						'alert' => 'Your session has expired.',
						'html' => [
							'<script>window.location = "'.$this->getPage('full_path').'?action=logout";</script>'
						],
						'sendto' => [
							'#loadspot-modal'
						]
					]);
			}
		}
	}
}