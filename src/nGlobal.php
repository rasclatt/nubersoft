<?php
namespace Nubersoft;

class nGlobal extends \Nubersoft\nApp
{
    private static $enttable;
    public static function sanitize($value)
    {
        if (!is_array($value) && !is_object($value)) {
            if (!is_numeric($value) && !is_bool($value) && !is_int($value) && !is_float($value)) {
                return (string) htmlentities(trim((string) $value), ENT_QUOTES, 'UTF-8');
            } else
                return trim((string) $value);
        }

        if (is_object($value))
            $value = parent::call()->toArray($value);

        foreach ($value as $key => $subval) {
            $value[$key] = self::sanitize($subval);
        }

        return $value;
    }
}