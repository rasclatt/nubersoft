<?php
namespace Nubersoft;

class nRouter extends \Nubersoft\nQuery
{
    public    function getPage($id, $column = 'full_path')
    {
        $path    =    trim($id);
        $nQuery    =    $this->getHelper('nQuery');
        $query    =    $nQuery->query("SELECT * FROM main_menus WHERE `{$column}` = ?",[$id])->getResults(1);
        
        if(!empty($query['page_options'])) {
            $query['page_options']    =    json_decode(html_entity_decode($query['page_options'], ENT_QUOTES),true);
        }
        
        return $query;
    }
    
    public function route($path, $func)
    {
        $curr    =    $this->getCurrentPage();
        $path    =    strtolower(trim($path));
        
        if($curr == $path)
            return $this->createContainer($func);
        else
            throw new HttpException('Page Not Found.', 404);
    }
    
    public function getCurrentPage()
    {
        return strtolower(trim($this->getDataNode('_SERVER')['REQUEST_URI']));
    }
    
    public function convertToStandardPath($string, $preg = '/[^A-Z0-9\-\_\/]/i')
    {
        return str_replace('//','/', '/'.trim(preg_replace($preg, '', str_replace(["\s","\t",PHP_EOL,' '],'-',$string)),'/').'/');
    }
	/**
	 *	@description	
	 */
	public static function createRoutingData(string $string)
	{
        $harr   =   parse_url($string);
        $host   =   explode('.', $harr['host']);
        $host   =   [
            'ssl' => ($harr['scheme'] == 'https'),
            'subdomain' => (count($host) > 2)? array_shift($host) : '',
            'tld' => array_pop($host),
            'domain' => implode($host)
        ];
        
        if(empty($harr['path']))
            $host['path']   =   '';
        
        $host['locale'] =   \Nubersoft\nApp::call()->getSession('locale');
        $host['locale_lang'] =   \Nubersoft\nApp::call()->getSession('locale_lang');
        return array_merge($host, ['query' => ($harr['query'])?? [] ]);
	}
}