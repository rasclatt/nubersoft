<?php
namespace Nubersoft\Settings\Page;
/**
 *	@description	
 */
class Controller extends \Nubersoft\Settings\Page
{
	protected	static	$page;
	
	/**
	 *	@description	
	 */
	public	function getContentStructure($page)
	{
		return $this->recurseLayout($this->getPageComponents($page, false));
	}
	/**
	 *	@description	
	 */
	public	function getPageComponents($page, $page_live = true)
	{
		if(empty(self::$page[$page])) {
			$sql	=	($page_live)? " AND `page_live` = 'on'" : false;
			self::$page[$page]	=	$this->getHelper('ArrayWorks')->organizeByKey($this->query("SELECT * FROM components WHERE `ref_page` = ? {$sql} ORDER BY `parent_id` ASC, `page_order` ASC, `ID` ASC", [$page])->getResults(), 'unique_id');
		}
		
		return self::$page[$page];
	}
	
	public	function getTemplateList()
	{
		$client_temps	=	(is_dir(NBR_CLIENT_TEMPLATES))? array_filter(array_map(function($v){
			$path	=	rtrim(NBR_CLIENT_TEMPLATES.DS.$v, DS).DS;
			
			if(in_array($v, ['.','..']))
				return false;
			
			return str_replace(NBR_ROOT_DIR, '', $path);
			
			},scandir(NBR_CLIENT_TEMPLATES))) : [];
		
		$def	=	str_replace(NBR_ROOT_DIR, '', NBR_DEFAULT_TEMPLATE);
		$opts	=	array_map(function($v){
			return [
				'name' => ucwords(\Nubersoft\nApp::call()->getHelper('Conversion')->columnToTitle(basename($v))),
				'value' => str_replace(DS.DS, DS, $v.DS)
			];
		},array_merge([$def],$client_temps));
		
		return $opts;
	}
	
	public	function siteLogoActive()
	{
		return ($this->getSystemOption('header_company_logo_toggle') == 'on');
	}
	
	public	function getSiteLogo()
	{
		$toggle	=	$this->siteLogoActive();
		return ($toggle == 'on')? $this->localeUrl($this->getSystemOption('header_company_logo')) : false;
	}
}