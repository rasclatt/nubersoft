<?php
namespace Nubersoft;
/**
 *	@description	
 */
class JWTFactory extends nApp
{
    const DEFAULT   =   'fb';
    
    private static $Library =   [
        'fb' => 'Firebase'
    ];
    
    private static $JWT =   [
        'fb' => '/JWTI/Firebase'
    ];
    private static $current;
	/**
	 *	@description	
	 */
	public	static function get($name = false): JWT\Controller
	{
        if(empty($name))
            $name   =   self::DEFAULT;   
        # Check there is an available object
        if(!self::getLib($name))
            throw new \Exception('JTW of type '.self::call()->enc($name).' does not exist.');
        # Convert the string
        $objstr =   str_replace(['/','|','_'], '\\', self::$JWT[$name]);
        # Save the current designation
        self::$current  =   self::$Library[$name].'::'.$objstr;
        # Return the jwt object
        return new JWT\Controller(self::call()->getHelperClass($objstr));
	}
	/**
	 *	@description	
	 */
	public	static function getLib($name = false)
	{
        if($name)
            return (isset(self::$Library[$name]))? self::$Library[$name] : false;
        
        return self::$Library;
	}
	/**
	 *	@description	
	 */
	public static	function addLib(string $name, string $obj, $propper = false)
	{
        self::$JWT[$name]   =   $obj;
        self::$Library[$name]   =   (empty($propper))? $obj : $propper;
	}
	/**
	 *	@description	
	 */
	public	static function getCurrent(): string
	{
        return (self::$current)?? '';
	}
}