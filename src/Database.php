<?php
namespace Nubersoft;

use \Nubersoft\Dto\Database\ConstructRequest;

class Database
{
    private static $con;
    
    public function __construct(ConstructRequest $settings)
    {
        if(self::$con instanceof \PDO)
            return self::$con;
        
        if(empty($settings->opts)) {
            $settings->opts[\PDO::ATTR_ERRMODE] = \PDO::ERRMODE_EXCEPTION;
            $settings->opts[\PDO::ATTR_DEFAULT_FETCH_MODE] = \PDO::FETCH_ASSOC;
            $settings->opts[\PDO::ATTR_EMULATE_PREPARES] = false;
        }
        
        self::$con = new \PDO($settings->db.':host='.base64_decode($settings->host).';dbname='.base64_decode($settings->dbname).';charset='.$settings->charset, base64_decode($settings->user), base64_decode($settings->pass), $settings->opts);
    }
    
    public function getConnection(): \PDO
    {
        return self::$con;
    }
}