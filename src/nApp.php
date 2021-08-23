<?php
namespace Nubersoft;

class nApp extends \Nubersoft\nFunctions
{
    use nUser\enMasse,
        Plugin\enMasse,
        nRouter\enMasse,
        DataNode\enMasse;

    private static $Reflect;
	/**
	 *	@description	
	 */
	public function __construct()
	{
	}
    
    public function userGet($key = false)
    {
        $SESS = (!empty($this->getSession('user')))? $this->getSession('user') : [];
        
        if(!empty($key))
            return (isset($SESS[$key]))? $SESS[$key] : false;
        
        return $SESS;
    }
	/**
	 *	@description	
	 */
	public function requestFetch($method, $key = false, $encode = true)
	{
        $data   =   (new Request())->{$method}($key);
        
        if($encode)
            return nGlobal::sanitize($data);
        return $data;
	}
    
    public function getPost($key = false, $encode = true)
    {
        return $this->requestFetch(__FUNCTION__, $key, $encode);
    }
    
    public function getGet($key = false, $encode = true)
    {
        return $this->requestFetch(__FUNCTION__, $key, $encode);
    }
    
    public function getRequest($key = false, $encode = true)
    {
        return $this->requestFetch(__FUNCTION__, $key, $encode);
    }
    
    public function getCookie($key = false, $encode = true)
    {
        $data    =    $this->getHelper('nCookie')->pullFromNode()->get($key);
        return ($encode)? nGlobal::sanitize($data) : $data;
    }
    
    public function getServer($key = false, $encode = true)
    {
        $data    =    $this->getGlobal('SERVER', $key);
        return ($encode)? nGlobal::sanitize($data) : $data;
    }
    
    public function getSession($key = false)
    {
        $SESS    =    $this->getDataNode('_SESSION');
        
        if($key)
            return (!empty($SESS[$key]))? $SESS[$key] : false;
        
        if(is_array($SESS))
            ksort($SESS);
        
        return $SESS;
    }
    
    public function getFiles()
    {
        $files = $this->getDataNode('_FILES');
        
        if(empty($files))
            return [];

        return array_map(function($v){
            return new \Nubersoft\Dto\File($v);
        }, $files);
    }
    /**
     *	@description	
     *	@param	
     */
    public function getFile(): \Nubersoft\Dto\File
    {
        $files = $this->getFiles();
        return (count($files) == 1)? $files[0] : new \Nubersoft\Dto\File();

    }

    public function getHelper()
    {
        $args = func_get_args();
        $class = (!empty($args[1]))? $args[0] : str_replace('\\\\', '\\', "\\Nubersoft\\".$args[0]);
        try {
            $Reflect = $this->getReflector();
            return $Reflect->execute($class);
        }
        catch(\Exception $e) {
            throw new HttpException('Class doesn\'t exist: <pre>'.print_r(array_map(function($v){ return (isset($v['file']))? str_replace(NBR_ROOT_DIR, '', $v['file']).'('.$v['line'].')' : $v; },debug_backtrace()),1).'</pre>', 100);
        }
    }
    
    public function getHelperClass($class)
    {
        return nReflect::instantiate("\\Nubersoft{$class}");
    }
    
    public    static function call($class = false, $plugin= false)
    {
        return (!empty($class))? (new nApp())->getHelper($class, $plugin) : new nApp();
    }
    
    public static function createContainer($func, $cache = false)
    {
        $Reflect    =    (new nApp())->getReflector();
        
        if($cache) {
            ob_start();
            $Reflect->reflectFunction($func);
            $data    =    ob_get_contents();
            ob_end_clean();
            
            return $data;
        }
        else
            return $Reflect->reflectFunction($func);
    }
    
    public function getReflector()
    {
        if(self::$Reflect instanceof \Nubersoft\nReflect)
            return self::$Reflect;
        
        return self::$Reflect    =    new nReflect();
    }
    
    public function getDataNode($key = false)
    {
        return $this->getHelper('DataNode')->getDataNode($key);
    }
    
    public function decode($value)
    {
        return json_decode($this->dec($value),true);
    }
    
    public function encode($value)
    {
        return $this->enc(json_encode($value));
    }
    
    public function enc($value)
    {
        if(is_array($value) || is_object($value))
            return $value;
        
        return htmlentities($value, ENT_QUOTES, 'UTF-8');
    }
    
    public function dec($value)
    {
        if(is_array($value) || is_object($value))
            return $value;
        
        return html_entity_decode($value, ENT_QUOTES, 'UTF-8');
    }
    
    public function getAdminPage($key = 'full_path')
    {
        return $this->getHelper('Settings\Admin')->{__FUNCTION__}($key);
    }
    
    public function isAdminPage()
    {
        $this->getHelper('Settings\Admin')->{__FUNCTION__}();
    }
    
    public function saveSetting($key, $value, $clear = false)
    {
        $DataNode    =    $this->getHelper('DataNode');
        
        if($clear)
            $DataNode->removeNode($key);
        
        $DataNode->addNode($key, $value);
    }
    
    public function reportErrors($rep = true)
    {    
        ini_set('display_errors', $rep);
    
        if($rep)
            error_reporting(E_ALL);
        
        return $this;
    }
    
    public function fetchUniqueId($other = false, $sub = 20)
    {
        return substr(date('YmdHis').rand(1000000, 9999999).$other, 0, $sub);
    }
	/**
	 *	@description	
	 */
	public function getHost($key = false)
	{
        $data   =   $this->getDataNode('routing_info');
        if(!empty($key))
            return ($data[$key])?? null;
        
        return $data;
	}
}