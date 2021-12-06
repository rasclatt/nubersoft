<?php
namespace Nubersoft;
/**
 * @description This is a "kitchen sink" class that can be called from anywhere
 *              that includes a large array of common helpers
 */
use \Nubersoft\Helper\ {
    Router,
    FolderWorks,
    Request
};
use \Nubersoft\Dto\Helper\ {
    FolderWorks\IsDirRequest,
    Request\GetRequest
};
class nFunctions
{
    private static $msg;
    const TO_ARRAY  =   1;
    const TO_OBJECT =   false;
    /**
     * @description Used to retrieve global arrays
     */
    public function getGlobal()
    {
        $args = func_get_args();
        
        return Request::get(new GetRequest([
            'type' => ($args[0])?? 'post',
            'key' => ($args[1])?? null
        ]));
    }
    /**
     * @description Turns an object to an array by converting to json and back
     */
    public function toArray($array = false)
    {
        return $this->toArrObj($array, self::TO_ARRAY);
    }
    /**
     * @description Turns an array into an object by converting to json and back
     */
    public function toObject($array = false)
    {
        return $this->toArrObj($array, self::TO_OBJECT);
    }
    /**
     * @description Turns an array or object to either.
     */
    public function toArrObj($array = false, $type)
    {
        if ($array === false)
            return $array;

        return (is_object($array) || is_array($array)) ? json_decode(json_encode($array), $type) : $array;
    }
    /**
     * @description Autoloads a function
     * @returns  Array of errors or boolean (empty)
     */
    public function autoload($func, $path = false)
    {
        if (empty($path))
            $path = __DIR__ . DS . 'functions';

        $func = (!is_array($func)) ? [$func] : $func;

        foreach ($func as $function) {
            if (is_file($inc = $path . DS . $function . '.php'))
                include_once($inc);
            else
                $err[] = "Not found: ({$function}) in {$inc}.";
        }
        return (!empty($err)) ? $err : false;
    }

    public function toSingleDs($value)
    {
        return StringWorks::{__FUNCTION__}($value);
    }

    public function toSingleSlash($value)
    {
        return StringWorks::{__FUNCTION__}($value);
    }
    /**
     * @description Autoloads a function
     * @returns  Array of errors or boolean (empty)
     */
    public function render()
    {
        $args = func_get_args();
        $include = (!empty($args[0])) ? $args[0] : false;
        $useData = (!empty($args[1])) ? $args[1] : false;
        if (isset($args[0]))
            unset($args[0]);
        $data = $args;
        if (!is_file($include))
            return null;
        ob_start();
        include($include);
        $data = ob_get_contents();
        ob_end_clean();
        return $data;
    }

    public function toError($msg, $code = false, $log = true)
    {
        if ($code) {
            self::$msg['errors'][$code][] = $msg;
        } else
            self::$msg['errors'][]  = $msg;

        if ($log) {
            (new nAutomator\Controller)->createWorkflow('logger');
        }
    }

    public function toSuccess($msg, $code = false, $log = false)
    {
        if ($code) {
            self::$msg['success'][$code][] = $msg;
        } else
            self::$msg['success'][]  = $msg;

        if ($log) {
            (new nAutomator\Controller)->createWorkflow('logger');
        }
    }

    public function getSystemMessages($type = false)
    {
        if ($type)
            return (!empty(self::$msg[$type])) ? self::$msg[$type] : [];

        return self::$msg;
    }
    /**
     * @description Alias of siteUrl(). This used to contain country abbr but it deemed unecessary.
     * @deprecation Under review for removal
     */
    public function localeUrl($path = '/')
    {
        return $this->siteUrl($path);
    }

    public function siteUrl($path = false)
    {
        return Router::{__FUNCTION__}((string) $path);
    }
    /**
     * @description Checks to see if the server is using SSL
     */
    public function isSsl()
    {
        return Router::{__FUNCTION__}();
    }
    /**
     * @description Checks if a directory exists and attempts to make a new folder if not existing
     */
    public function isDir(string $dir, bool $make = false, int $perm = 0775): bool
    {
        return FolderWorks::{__FUNCTION__}(new IsDirRequest([
            'dir' => $dir,
            'create' => $make,
            'perm' => $perm
        ]));
    }

    public function __($string)
    {
        return $string;
    }
}
