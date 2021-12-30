<?php
namespace Nubersoft\Dto\Tables;

class Users extends \Nubersoft\Dto\Tables
{
    public string $username = '';
    public string $password = '';
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $address_1 = '';
    public string $address_2 = '';
    public string $city = '';
    public string $state = '';
    public string $country = '';
    public string $postal = '';
    public $usergroup;
    public string $user_status = '';
    public string $file = '';
    public string $file_path = '';
    public string $file_name = '';
    public string $reset_password = '';
    public string $attempts = '';
    public string $last_attempt = '';
    public string $timestamp = '';
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        $array = parent::{__FUNCTION__}($array);
        $array['table'] = 'users';
        return $array;
    }
}