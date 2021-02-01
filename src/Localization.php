<?php
namespace Nubersoft;

use \Nubersoft\ {
    nRender,
    Settings
};
/**
 *	@description	
 */
class Localization extends nRender
{
    use Settings\enMasse;
	/**
	 *	@description	
	 */
	public function transKeyExists($key)
	{
        return $this->getTransKey($key);
	}
	/**
	 *	@description	
	 */
	public function saveTransKey($key, $value, $group = false)
	{
        $this->addComponent([
            'title' => $key,
            'content' => $value,
            'component_type' => 'transkey'
        ]);
        
        return $this;
	}
	/**
	 *	@description	
	 */
	public function getTransKey($name):? array
	{
        $data   =   $this->getComponentBy([
            'title' => $name,
            'component_type' => 'transkey'
        ]);
        
        return (!empty($data))? $data : null;
	}
	/**
	 *	@description	
	 */
	public static function getSiteLocale()
	{
        return \Nubersoft\nApp::call('nCookie')->get('locale');
	}
}