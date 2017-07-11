<?php
namespace nPlugins\Nubersoft\InspectorPallet;

class Observer extends \nPlugins\Nubersoft\InspectorPallet implements \Nubersoft\nObserver
	{
		/*
		**	@description	Listens for the inspector pallet to be turned on or off
		*/
		public	function listen()
			{
				$status	=	$this->getGet('toggle_editor');
				
				if(!empty($status))
					$this->setSession('admintools',array('editor'=>$status),true);
			}
		/*
		**	@description	Static alias to listen()
		*/
		public	static	function createListener()
			{
				return (new Observer())->listen();
			}
	}