<?php
namespace Nubersoft;

use \Nubersoft\{
	Settings,
	Settings\enMasse as SettingsTrait
};

/**
 * @description 
 */
class Localization extends nApp
{
	use SettingsTrait;
	/**
	 * @description 
	 */
	public function transKeyExists($key)
	{
		return $this->getTransKey($key);
	}
	/**
	 * @description 
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
	 * @description 
	 */
	public function getTransKey($name): ?array
	{
		$data   =   $this->getComponentBy([
			'title' => $name,
			'component_type' => 'transkey'
		]);

		return (!empty($data)) ? $data : null;
	}
	/**
	 * @description 
	 */
	public function saveTranslation(string $transkey, $value, $translation_key = 'translator', $ref_page = false)
	{
		$args  = [
			'title' => $transkey,
			'category_id' => 'translator'
		];

		if (!empty($ref_page))
			$args['ref_page']   =  $ref_page;

		$component = $this->getComponentBy($args);

		if (!empty($component))
			$this->deleteComponentBy($args);

		$args['content'] = $this->enc($value);
		$this->addComponent($args);

		return $this->getComponentBy([
			'title' => $transkey,
			'category_id' => 'translator'
		]);
	}
	/**
	 * @description 
	 */
	public function translationExists(string $transkey)
	{
		$d = $this->getComponentBy([
			'title' => $transkey,
			'category_id' => 'translator'
		]);
		return (empty($d)) ? false : $d;
	}
	/**
	 * @description 
	 */
	public static function getSiteLocale($def = 'us')
	{
		$l = nApp::call('nCookie')->get('locale');
		return (empty($l)) ? $def : $l;
	}
	/**
	 * @description 
	 */
	public static function getSiteLanguage($def = 'en')
	{
		$l = nApp::call('nCookie')->get('language');
		return (empty($l)) ? $def : $l;
	}
	/**
	 * @description 
	 */
	public static function getSiteCountry($def = 'us')
	{
		$l = nApp::call('nCookie')->get('country');
		return (empty($l)) ? $def : $l;
	}
}
