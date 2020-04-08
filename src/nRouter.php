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
    
    public    function route($path, $func)
    {
        $curr    =    $this->getCurrentPage();
        $path    =    strtolower(trim($path));
        
        if($curr == $path)
            return $this->createContainer($func);
        else
            throw new HttpException('Page Not Found.', 404);
    }
    
    public    function getCurrentPage()
    {
        return strtolower(trim($this->getDataNode('_SERVER')['REQUEST_URI']));
    }
    
    public    function convertToStandardPath($string, $preg = '/[^A-Z0-9\-\_\/]/i')
    {
        return str_replace('//','/', '/'.trim(preg_replace($preg, '', str_replace(["\s","\t",PHP_EOL,' '],'-',$string)),'/').'/');
    }
}