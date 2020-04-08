<?php
namespace Nubersoft;

class Database extends \Nubersoft\nApp
{
    private    static $con;
    
    public    function __construct($host, $dbname, $user, $pass, $charset = 'utf-8', $db = 'mysql', $opts = false)
    {
        if(self::$con instanceof \PDO)
            return self::$con;
        
        if(empty($opts)) {
            $opts[\PDO::ATTR_ERRMODE]                =    \PDO::ERRMODE_EXCEPTION;
            $opts[\PDO::ATTR_DEFAULT_FETCH_MODE]    =    \PDO::FETCH_ASSOC;
            $opts[\PDO::ATTR_EMULATE_PREPARES]        =    false;
        }
        
        self::$con    =    new \PDO($db.':host='.base64_decode($host).';dbname='.base64_decode($dbname).';charset='.$charset, base64_decode($user), base64_decode($pass), $opts);
    }
    
    public    function getConnection()
    {
        return self::$con;
    }
}