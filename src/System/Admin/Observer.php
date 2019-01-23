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
		$layout		=	$this->getSettingsLayout($this->getPost('deliver')['subaction']);
		
		
		if(empty($layout)) {
			$this->ajaxResponse([
				"alert" => "Layout for this settings page is not set."
			]);
		}
		$response	=	[
			'html' => [
				$layout
			],
			'sendto' => [
				'#admin-content'
			]
		];
		
		$this->ajaxResponse($response);
	}
	
	private function getSettingsLayout($type)
	{
		return $this->getPlugin('settings', DS.$type.'.php');
	}
	
	public	function saveSettings()
	{
		foreach($this->getPost('setting') as $name => $value) {
			if(is_array($value))
				$value	=	json_encode($value);
			
			if($name == 'htaccess') {
				file_put_contents(NBR_ROOT_DIR.DS.'.htaccess', $this->dec($value));
			}
			
			$this->deleteSystemOption($name);
			$this->setSystemOption($name, $value);
		}
		
		$this->toSuccess("Options Saved.");
	}
	
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