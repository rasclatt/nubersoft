<?php
namespace Nubersoft\Dto\Database;

class ConstructRequest extends \SmartDto\Dto
{
    public $host = '';
    public $dbname = '';
    public $user = '';
    public $pass = '';
    public $charset = 'utf-8';
    public $db = 'mysql';
    public $opts = [];
}