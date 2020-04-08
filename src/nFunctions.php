<?php
namespace Nubersoft;

class nFunctions
{
    private    static    $singleton,
                    $msg;
    const    TO_ARRAY     =    1;
    const    TO_OBJECT     =    false;
    /**
     *    @description    Create a singleton to reuse
     */
    public    function __construct()
    {
        if(self::$singleton instanceof \Nubersoft\nFunctions)
            return self::$singleton;
        
        self::$singleton    =    $this;
        
        return self::$singleton;
    }
    /**
     *    @description    Used to retrieve global arrays
     */
    public    function getGlobal()
    {
        $args    =    func_get_args();
        $type    =    (!empty($args[0]))? strtolower($args[0]) : 'POST';
        $key    =    (!empty($args[1]))? $args[1] : false;
        
        switch($type) {
            case('get'):
                $REQ    =    $_GET;
                break;
            case('request'):
                $REQ    =    $_REQUEST;
                break;
            case('files'):
                $REQ    =    $_FILES;
                break;
            case('put'):
                $REQ    =    $_PUT;
                break;
            case('delete'):
                $REQ    =    $_DELETE;
                break;
            case('server'):
                $REQ    =    $_SERVER;
                break;
            default:
                $REQ    =    $_POST;
        }
        
        if($key)
            return (isset($REQ[$key]))? $REQ[$key] : null;
        
        return (!empty($args[2]))? $this->toObject($REQ) : $REQ;
    }
    /**
     *    @description    Turns an object to an array by converting to json and back
     */
    public    function toArray($array = false)
    {
        return $this->toArrObj($array, self::TO_ARRAY);
    }
    /**
     *    @description    Turns an array into an object by converting to json and back
     */
    public    function toObject($array = false)
    {
        return $this->toArrObj($array, self::TO_OBJECT);
    }
    /**
     *    @description    Turns an array or object to either.
     */
    public    function toArrObj($array = false, $type)
    {    
        if($array === false)
            return $array;
        
        return (is_object($array) || is_array($array))? json_decode(json_encode($array),$type) : $array;
    }
    /**
     *    @description    Autoloads a function
     *    @returns        Array of errors or boolean (empty)
     */
    public    function autoload($func, $path = false)
    {
        if(empty($path))
            $path    =    __DIR__.DS.'functions';
            
        $func    =    (!is_array($func))? [$func] : $func;
        
        foreach($func as $function) {
            if(is_file($inc = $path.DS.$function.'.php'))
                include_once($inc);
            else
                $err[]    =    "Not found: ({$function}) in {$inc}.";
        }
        return (!empty($err))? $err : false;
    }
    
    public    function toSingleDs($value)
    {
        return str_replace(DS.DS, DS, $value);
    }
    
    public    function toSingleSlash($value)
    {
        return str_replace('//', '/', $value);
    }
    
    public    function render()
    {
        $args        =    func_get_args();
        $include    =    (!empty($args[0]))? $args[0] : false;
        $useData    =    (!empty($args[1]))? $args[1] : false;
        
        if(isset($args[0]))
            unset($args[0]);
        
        $data    =    $args;
        
        if(!is_file($include))
            return null;
        
        ob_start();
        include($include);
        $data    =    ob_get_contents();
        ob_end_clean();
        
        return $data;
    }
    
    public    function toError($msg, $code = false, $log = true)
    {
        if($code) {
            self::$msg['errors'][$code][]    =    $msg;
        }
        else
            self::$msg['errors'][]        =    $msg;
        
        if($log) {
            $this->getHelper('nAutomator\Controller')->createWorkflow('logger');
        }
    }
    
    public    function toSuccess($msg, $code = false, $log = false)
    {
        if($code) {
            self::$msg['success'][$code][]    =    $msg;
        }
        else
            self::$msg['success'][]        =    $msg;
        
        if($log) {
            $this->getHelper('nAutomator\Controller')->createWorkflow('logger');
        }
    }
    
    public    function getSystemMessages($type = false)
    {
        if($type)
            return (!empty(self::$msg[$type]))? self::$msg[$type] : [];
        
        return self::$msg;
    }
    /**
     *    @description    Alias of siteUrl(). This used to contain country abbr but it deemed unecessary.
     *    @deprecation    Under review for removal
     */
    public    function localeUrl($path = '/')
    {
        //$this->getHelper('nSession')->get('locale');
        return $this->siteUrl($path);
    }
    
    public    function siteUrl($path = false)
    {
        $proto    =    ($this->isSsl())? 'https://' : 'http://';
        
        if(defined('BASE_URL')) {
            if(BASE_URL == '{domain}')
                $domain    =    $proto.$this->toSingleSlash($this->getServer('HTTP_HOST').'/'.$path);
            else
                $domain    =    BASE_URL.$this->toSingleSlash('/'.$path);
        }
        else {
            $domain    =    $proto.$this->toSingleSlash($this->getServer('HTTP_HOST').'/'.$path);
        }
        
        return $domain;
    }
    
    public    function isSsl()
    {
        return (!empty($this->getServer('HTTPS')));
    }
    
    public    function isDir($dir, $make = false, $perm = 0775)
    {
        $exists    =    is_dir($dir);
        
        if(!$exists) {
            if(!$make) {
                return $exists;
            }
            else {
                mkdir($dir, $perm, true);
            }
        }
        
        return is_dir($dir);
    }
    
    public    function __($string)
    {
        return $string;
    }
}