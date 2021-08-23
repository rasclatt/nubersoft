<?php
namespace Nubersoft\Dto\Session\User;

class GetResponse extends \SmartDto\Dto
{
    public $ID = 0;
    public $unique_id = '';
    public $username = '';
    public $password = '';
    public $first_name = '';
    public $last_name = '';
    public $email = '';
    public $address_1 = '';
    public $address_2 = '';
    public $city = '';
    public $state = '';
    public $country = '';
    public $postal = '';
    public $usergroup = 3;
    public $user_status = 'off';
    public $file = '';
    public $file_path = '';
    public $file_name = '';
    public $reset_password = '';
    public $attempts = 0;
    public $last_attempt = '';
    public $timestamp = '';
}