<?php
namespace Nubersoft\Dto\Tables;

class Emailer extends \Nubersoft\Dto\Tables
{
    public string $content = '';
    public string $content_back = '';
    public string $return_copy = '';
    public string $return_address = '';
    public string $return_response = '';
    public string $email_id = '';
    public string $page_live = 'off';
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        $array = parent::{__FUNCTION__}($array);
        $array['table'] = 'emailer';
        return $array;
    }
}