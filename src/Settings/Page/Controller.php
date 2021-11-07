<?php
namespace Nubersoft\Settings\Page;

use \Nubersoft\Settings\Page;
/**
 *    @description    
 */
class Controller extends Page
{
    protected static $page;
    
    /**
     *    @description    
     */
    public function getContentStructure(string $page = null)
    {
        return $this->recurseLayout($this->getPageComponents($page, false));
    }
    /**
     *    @description    
     */
    public function getPageComponents(string $page = null, bool $page_live = true)
    {
        if(empty(self::$page[$page])) {
            $sql = ($page_live)? " AND `page_live` = 'on'" : false;
            self::$page[$page] = \Nubersoft\ArrayWorks::organizeByKey($this->query("SELECT * FROM components WHERE `ref_page` = ? {$sql} ORDER BY `parent_id` ASC, `page_order` ASC, `ID` ASC", [$page])->getResults(), 'unique_id');
        }
                
        return self::$page[$page];
    }
    
    public function getTemplateList()
    {
        $templates  =   array_merge(
            $this->fetchTemplateListFromScan(NBR_CLIENT_TEMPLATES),
            $this->fetchTemplateListFromScan(NBR_CORE.DS.'template')
        );
        
        $opts    =    array_map(function($v){
            return [
                'name' => ucwords(\Nubersoft\nApp::call()->getHelper('Conversion')->columnToTitle(basename($v))),
                'value' => str_replace(DS.DS, DS, $v.DS)
            ];
        }, $templates);
        
        return $opts;
    }
    
    public function siteLogoActive()
    {
        return ($this->getSystemOption('header_company_logo_toggle') == 'on');
    }
    
    public function getSiteLogo()
    {
        $toggle    =    $this->siteLogoActive();
        return ($toggle == 'on')? $this->localeUrl($this->getSystemOption('header_company_logo')) : false;
    }
	/**
	 *	@description	
	 */
	public function fetchTemplateListFromScan($dir)
	{
        if(!is_dir($dir))
            return [];
        
        return array_filter(array_map(function($v) use ($dir) {
            $path    =    rtrim($dir.DS.$v, DS).DS;
            
            if(in_array($v, ['.','..']))
                return false;
            elseif(strtolower($v) == 'plugins')
                return false;
            
            return str_replace(NBR_ROOT_DIR, '', $path);
            
            }, scandir($dir)));
	}
}