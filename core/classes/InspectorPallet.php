<?php
	class InspectorPallet
		{
			protected	static	$nubquery;
				
			public	static	function execute($settings = false)
				{
					$animate	=	(!empty($settings['fx']))? $settings['fx']:'fade';
					$buttons	=	(!empty($settings['toolbar']) && is_array($settings['toolbar']))? $settings['toolbar']:false;
					
					register_use(__METHOD__);
					AutoloadFunction('is_admin,create_js_trigger');
					if(is_admin()) {
						echo create_js_trigger("runinspector",($animate == 'slide')? 'slide':'fade');
						include(NBR_RENDER_LIB.DS.'class.html'.DS.'InspectorPallet'.DS.'execute.php');
					}	
				}
				
			protected	static	function Button()
				{
					register_use(__METHOD__);
					
					// ToggleFunction required to run the current session state for the toggle edit function
					if(is_admin())
						include(NBR_RENDER_LIB.DS.'class.html'.DS.'InspectorPallet'.DS.'Button.php');
				}
			
			public	static	function AdminToolsQuickLinks($admin_link = array())
				{
					register_use(__METHOD__);
					
					AutoloadFunction('site_valid');
					if(nApp::siteValid()) {
							// Show tables in database
							AutoloadFunction('FetchTables,nQuery');
							$results	=	FetchTables();
							include(NBR_RENDER_LIB.DS.'class.html'.DS.'InspectorPallet'.DS.'AdminToolsQuickLinks.php');
						}
				}
			
			protected	static function	AdminToolsPallet()
				{
					return new ToolInspector(false);
				}
		}