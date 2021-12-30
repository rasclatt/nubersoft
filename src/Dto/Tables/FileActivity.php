<?php
namespace Nubersoft\Dto\Tables;

class FileActivity extends \Nubersoft\Dto\Tables
{
    public string $username = '';
    public string $ip_address = '';
    public string $action_slug = '';
    public string $file_id = '';
    public string $timestamp = '';
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        $array = parent::{__FUNCTION__}($array);
        $array['table'] = 'file_activity';
        return $array;
    }
}