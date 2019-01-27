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