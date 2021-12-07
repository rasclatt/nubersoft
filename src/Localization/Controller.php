<?php
namespace Nubersoft\Localization;

use \Nubersoft\{
	nApp,
	Localization,
	nQuery\enMasse as nQuery
};

/**
 * @description 
 */
class Controller extends Localization
{
	use nQuery;
	/**
	 * @description 
	 */
	public function getActiveCountries($format = false)
	{
		return $this->getLocaleAttr('country', $format);
	}
	/**
	 * @description 
	 */
	public function getActiveLanguages($format = false)
	{
		return $this->getLocaleAttr('language', $format);
	}
	/**
	 * @description 
	 */
	protected function getLocaleAttr(string $name, $format = false)
	{
		$data = $this->query("SELECT * FROM system_settings WHERE category_id = ? AND option_group_name = 'locale' ORDER BY page_order ASC", [$name])->getResults();

		if (empty($data))
			return [];

		return (!$format) ? $data : array_map(function ($v) {
			return [
				'abbr' => $v['option_attribute'],
				'active' => $v['page_live']
			];
		}, $data);
	}
	/**
	 * @description 
	 */
	public function localeAttrActive(string $kind)
	{
		$data = $this->query("SELECT page_live as attr FROM system_settings WHERE category_id = ? AND option_group_name = 'locale' AND page_live != 'off'", [$kind])->getResults(1);

		if (empty($data))
			return false;

		return $data['attr'];
	}
}
