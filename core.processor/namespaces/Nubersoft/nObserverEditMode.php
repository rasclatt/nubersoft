<?php
namespace Nubersoft;

class nObserverEditMode implements nObserver
	{
		public	static	function listen()
			{
				\nApp::nFunc()->autoload('initialize_edit_mode',NBR_FUNCTIONS);
				initialize_edit_mode();
			}
	}