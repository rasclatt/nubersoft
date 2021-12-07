<?php
namespace Nubersoft\nQuery;

use \Nubersoft\nQuery;

trait enMasse
{
    protected static $db;
    
    public function query($sql, $bind = false, $conn = false)
    {
        return $this->nQuery()->query($sql, $bind, $conn);
    }
    
    public function getColumnsInTable($table,$ticks = '`')
    {
        return $this->nQuery()->{__FUNCTION__}($table, $ticks);
    }
    
    public function stripTableName(string $string)
    {
        return $this->nQuery()->stripTableName($string);
    }
    
    public function nQuery()
    {
        if(empty(self::$db))
            self::$db    =    new nQuery();
        
        return self::$db;
    }
}