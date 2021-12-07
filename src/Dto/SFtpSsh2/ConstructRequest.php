<?php
namespace Nubersoft\Dto\SFtpSsh2;

class ConstructRequest extends \SmartDto\Dto
{
    public $host = '';
    public $user = '';
    public $pass = '';
    public $root = '';
    public $port = 22;
    public $timeout = 90;
}