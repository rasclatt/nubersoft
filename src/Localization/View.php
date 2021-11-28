<?php
namespace Nubersoft\Localization;

use \Nubersoft\nApp;

/**
 * @description 
 */
class View extends \Nubersoft\Localization
{
	private static $editor = [
		'class' => 'nbr_ajax_form'
	];
	/**
	 * @description 
	 */
	public static function setAttr(string $name, $value): void
	{
		self::$editor[$name] = $value;
	}
	/**
	 * @description 
	 */
	public static function setOpts(array $opts): void
	{
		foreach ($opts as $key => $value)
			self::setAttr($key, $value);
	}
	/**
	 * @description 
	 * @param 
	 */
	public static function isEditableMode()
	{
		$nApp = \Nubersoft\nApp::call();
		if (empty($nApp->getSession('translator_mode')))
			return false;

		return $nApp->isAdmin();
	}
	/**
	 * @description 
	 */
	public function getBlock($identifier)
	{
		$item = $this->getComponentBy([
			'category_id' => 'translator',
			'title' => $identifier
		]);

		return (!empty($item[0]['content'])) ? nApp::call()->dec($item[0]['content']) : false;
	}
	/**
	 * @description 
	 */
	public static function get($tag, $def, $max = '300px', $label = false)
	{
		$nApp = nApp::call();
		$locale = $nApp->getSession('locale');
		$lang = $nApp->getSession('locale_lang');

		$default = [
			'label' => $label,
			'id' => $tag . $locale . $lang,
			'height' => $max,
			'is_admin' => $nApp->isAdmin(),
			'text' => (new View())->getBlock($tag . $locale . $lang),
			'default' => $def
		];

		$default = array_merge($default, self::$editor);

		return $nApp->getHelper('Settings')
			->setPluginContent('translator', $default)->getPlugin('text_block_editor');
	}
	/**
	 * @description 
	 */
	public static function getOnly($tag, $def = false)
	{
		$nApp = nApp::call();
		$locale = $nApp->getSession('locale');
		$lang = $nApp->getSession('locale_lang');

		$comp = (new View())->getBlock($tag . $locale . $lang);

		if (!empty($comp))
			return $nApp->getHelper('nMarkUp')->useMarkUp($comp);

		return $def;
	}
}
