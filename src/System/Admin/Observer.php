<?php
namespace Nubersoft\System\Admin;
/**
 *	@description	
 */
class Observer extends \Nubersoft\System\Observer
{
	use \Nubersoft\Settings\enMasse;
	
	/**
	 *	@description
	 */
	public	function listen()
	{
		$subaction	=	$this->getPost('deliver')['subaction'];
		$layout		=	$this->getSettingsLayout($subaction);
		$modal		=	(!empty($this->getPost('deliver')['modal']));
		
		if(empty($layout)) {
			$this->ajaxResponse([
				"alert" => "Layout for this settings page is not set."
			]);
		}
		$response	=	[
			'html' => [
				$layout
			],
			'title' => 'Editing '.ucwords($subaction).' Settings.',
			'sendto' => [
				(!empty($this->getPost('deliver')['sendto']))? $this->getPost('deliver')['sendto'] : '#admin-content'
			]
		];
		
		$this->ajaxResponse($response, $modal);
	}
	/**
	 *	@description	
	 */
	public	function widgetManager()
	{
		if(!$this->isAdmin())
			return false;
		
		$plugin	=	$this->getRequest('slug');
		
		if(empty($plugin)) {
			$this->toError('Can not activate Widget. Missing widget name.');
			return false;
		}
		
		$Widget	=	new \Nubersoft\Widget($this->getRequest('slug'));
		
		switch($this->getRequest('action')) {
			case('activate_widget'):
				if($Widget->isActive()) {
					$this->toSuccess("Plugin is already activated.");
					return false;
				}
				$this->installWidget($Widget);
				
				break;
			case('deactivate_widget'):
				if(!$Widget->isActive()) {
					$this->toSuccess("Plugin is already deactivated.");
					return false;
				}
				
				$this->deleteWidget($Widget);
				break;
		}
	}
	/**
	 *	@description	
	 */
	private	function installWidget(\Nubersoft\Widget $Widget, $preflight = false)
	{
		$act			=	$Widget->getActions();
		$blf			=	$Widget->getBlockflows();
		$actions		=	(!empty($act))? str_replace(["\t",PHP_EOL],["",""],rtrim(ltrim($act->default->asXML(), '<default>'), '</default>')) : false;
		$blockflows		=	(!empty($blf))? str_replace(["\t",PHP_EOL],["",""],rtrim(ltrim($Widget->getBlockflows()->default->asXML(), '<default>'), '</default>')) : false;
		
		$path			=	NBR_CLIENT_DIR;
		$plugin_name	=	$Widget->getSlug();
		$base			=	$path.DS.'plugins'.DS.$plugin_name;
		
		if(!empty($actions)) {
			if($this->getComponentBy(["category_id" => $plugin_name, 'component_type' => 'plugin_action'],'=','AND','COUNT(*) as count')[0]['count'] == 0) {
				if(!$preflight) {
					$this->addComponent([
						"category_id" => 'widget_'.$plugin_name,
						'component_type' => 'plugin_action',
						'content' => $this->enc($actions)
					]);
				}
			}
		}
		
		if(!empty($blockflows)) {
			if($this->getComponentBy(["category_id" => $plugin_name, 'component_type' => 'plugin_blockflow'],'=','AND','COUNT(*) as count')[0]['count'] == 0) {
				if(!$preflight) {
					$this->addComponent([
						"category_id" => 'widget_'.$plugin_name,
						'component_type' => 'plugin_blockflow',
						'content' => $this->enc($blockflows)
					]);
				}
			}
		}
		# Convert the xml to settings
		$widget			=	$this->toArray($Widget->getConfig()); 
		# Loop each router
		if(isset($widget['router'])) {
			if(!$preflight)
				$this->createRouters($widget);
		}
		if(!empty($widget['vendor'])) { 
			if(isset($widget['vendor']['name'])) {
				if(!is_array($widget['vendor']['name'][0]))
					$widget['vendor']['name']	=	[$widget['vendor']['name']];

				$to		=	NBR_VENDOR;
				$from	=	$base.DS.'vendor';
				$Files	=	$this->getHelper('nFileHandler');
				foreach($widget['vendor']['name'] as $vendor) {
					
					$this->isDir($to.DS.$vendor, 1);
					$this->isDir($from.DS.$vendor, 1);
					
					$Files->recurseClone($from.DS.$vendor, $to.DS.$vendor, ['composer.json'], $preflight);
				}
			}
		}

		if(!empty($widget['template']['name'])) {
			if(!is_array($widget['template']['name']))
				$widget['template']['name']	=	[$widget['template']['name']];

			if(!isset($Files))
				$Files	=	$this->getHelper('nFileHandler');

			foreach($widget['template']['name'] as $template) {
				
				if(!is_dir($base.DS.'template'.DS.$template))
					continue;
				
				$Files->recurseClone($base.DS.'template'.DS.$template, NBR_CLIENT_DIR.DS.'template'.DS.$template, false, $preflight);
			}
		}

		if(!empty($widget['plugins']['name'])) {
			if(!is_array($widget['plugins']['name']))
				$widget['plugins']['name']	=	[$widget['plugins']['name']];

			if(!isset($Files))
				$Files	=	$this->getHelper('nFileHandler');

			foreach($widget['plugins']['name'] as $plugin) {
				$fromPlug	=	$base.DS.$plugin.DS.'plugins'.DS.$plugin;
				if(!is_dir($fromPlug))
					continue;
				
				$Files->recurseClone($fromPlug, NBR_CLIENT_DIR.DS.'template'.DS.'plugins'.DS.$plugin, false, $preflight);
			}
		}
		
		$Widget->activate();
	}
	/**
	 *	@description	
	 */
	private	function deleteWidget(\Nubersoft\Widget $Widget, $preflight = false)
	{
		$act			=	$Widget->getActions();
		$blf			=	$Widget->getBlockflows();
		$actions		=	(!empty($act))? str_replace(["\t",PHP_EOL],["",""],rtrim(ltrim($act->default->asXML(), '<default>'), '</default>')) : false;
		$blockflows		=	(!empty($blf))? str_replace(["\t",PHP_EOL],["",""],rtrim(ltrim($Widget->getBlockflows()->default->asXML(), '<default>'), '</default>')) : false;
		
		$path			=	NBR_CLIENT_DIR;
		$plugin_name	=	$Widget->getSlug();
		$base			=	$path.DS.$plugin_name;
		
		if(!empty($actions)) {
			$this->deleteComponentBy([
				"category_id" => 'widget_'.$plugin_name,
				'component_type' => 'plugin_action'
			]);
		}
		
		if(!empty($blockflows)) {
			$this->deleteComponentBy([
				"category_id" => 'widget_'.$plugin_name,
				'component_type' => 'plugin_blockflow'
			]);
		}
		# Convert the xml to settings
		$widget			=	$this->toArray($Widget->getConfig()); 
		# Loop each router
		if(isset($widget['router'])) {
			$this->query("DELETE FROM main_menus WHERE parent_id = ?", 'widget_'.$plugin_name);
		}
		if(!empty($widget['vendor'])) {
			if(isset($widget['vendor']['name'])) {
				if(!is_array($widget['vendor']['name'][0]))
					$widget['vendor']['name']	=	[$widget['vendor']['name']];

				$to		=	NBR_VENDOR;
				$from	=	$base.DS.'vendor';
				$Files	=	$this->getHelper('nFileHandler');
				foreach($widget['vendor']['name'] as $vendor) {
					$Files->recurseDelete($to.DS.$vendor);
					if(is_dir($vendor))
						rmdir($vendor);
				}
			}
		}

		if(!empty($widget['template']['name'])) {
			if(!is_array($widget['template']['name']))
				$widget['template']['name']	=	[$widget['template']['name']];

			if(!isset($Files))
				$Files	=	$this->getHelper('nFileHandler');

			foreach($widget['template']['name'] as $template) {
				$Files->recurseDelete(NBR_CLIENT_DIR.DS.'template'.DS.$template);
				if(is_dir($vendor))
						rmdir($vendor);
			}
		}

		if(!empty($widget['plugins']['name'])) {
			if(!is_array($widget['plugins']['name']))
				$widget['plugins']['name']	=	[$widget['plugins']['name']];

			if(!isset($Files))
				$Files	=	$this->getHelper('nFileHandler');

			foreach($widget['plugins']['name'] as $plugin) {
				$Files->recurseDelete(NBR_CLIENT_DIR.DS.'template'.DS.'plugins'.DS.$plugin);
			}
		}
		
		$Widget->deactivate();
	}
	
	private	function createRouters($widget)
	{
		$Page		=	new \Nubersoft\Settings\Page();
		if(!isset($widget['router'][0]))
			$widget['router']	=	[$widget['router']];
		
		foreach($widget['router'] as $router) {
			if(stripos($router['parent_id'], 'widget_') === false)
				$router['parent_id']	=	'widget_'.$router['parent_id'];

			$Page->createPage($router);
		}
	}

	private function getSettingsLayout($type)
	{
		return $this->getPlugin('settings', DS.$type.'.php');
	}
	/**
	 *	@description	Saves settings from the admin area(s)
	 */
	public	function saveSettings()
	{
		# Go throught the post
		foreach($this->getPost('setting') as $name => $value) {
			# If the value is an array, save the array to json
			if(is_array($value))
				$value	=	json_encode($value);
			# Create the htaccess by default
			if($name == 'htaccess') {
				file_put_contents(NBR_ROOT_DIR.DS.'.htaccess', $this->dec($value));
			}
			# Remove the option so it can be resaved
			$this->deleteSystemOption($name);
			# Resave
			$this->setSystemOption($name, $value);
		}
		# After saving the prerences, reload them to the data node so they are updated.
		$this->getHelper('DataNode')->setNode('settings', [
			'system' => $this->getHelper('Settings\Controller')->getSettings(false, 'system')
		]);
		# Create a success message
		$this->toSuccess("Options Saved.");
	}
	/**
	 *	@description	Saves the site logo from admin settings
	 */
	public	function saveSiteLogo()
	{
		$FILES	=	(!empty($this->getDataNode('_FILES')[0]['name']))? $this->getDataNode('_FILES')[0] : false;
		$toggle	=	(!empty($this->getPost('setting')['header_company_logo_toggle']))? $this->getPost('setting')['header_company_logo_toggle'] : 'off';
		
		$this->deleteSystemOption('header_company_logo_toggle');
		$this->setSystemOption('header_company_logo_toggle', $toggle);
		
		if(empty($FILES))
			return false;
		
		if(!in_array($FILES['type'], ['image/jpeg','image/png','image/gif'])) {
			$this->toError('File must be a PNG, GIF, or JPG');
			return false;
		}
		
		$destination	=	NBR_CLIENT_DIR.DS.'media'.DS.'images'.DS.'default'.DS.'company_logo.'.pathinfo($FILES['name'], PATHINFO_EXTENSION);
		
		$this->isDir(pathinfo($destination, PATHINFO_DIRNAME), true);
		
		if(move_uploaded_file($FILES['tmp_name'], $destination)) {
			$this->deleteSystemOption('header_company_logo');
			$this->setSystemOption('header_company_logo', str_replace(NBR_ROOT_DIR, '', $destination));
			$this->toSuccess("Site logo uploaded.");
		}
		else
			$this->toError("Site logo failed to upload.");
	}
}