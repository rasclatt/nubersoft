<?php
namespace Nubersoft\Dto\Tables;

class MembersConnected extends \Nubersoft\Dto\Tables
{
    public string $ip_address = '';
    public string $username = '';
    public string $domain = '';
    public string $timestamp = '';
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        $array = parent::{__FUNCTION__}($array);
        $array['table'] = 'members_connected';
        return $array;
    }
}