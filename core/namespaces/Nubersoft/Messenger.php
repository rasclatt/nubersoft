<?php
namespace Nubersoft;

class Messenger extends \Nubersoft\nApp
	{
		const	LOGGED_OUT	=	'You must be logged in to view this content';
		/*
		**	@description	Alerts the user regarding logged in state.
		*/
		public	function alertLoggedIn($msg = false,$wrapper = 'nbr_comment')
			{
				$msg	=	(empty($msg))? self::LOGGED_OUT : $msg;
				
				if(!$this->isLoggedIn()) {
					if($this->isAjaxRequest())
						$this->ajaxResponse(array('alert'=>$msg));
					else
						return '<span class="'.$wrapper.'">'.$msg.'</span>';
				}
			}
	}