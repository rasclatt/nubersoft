<?php
namespace Nubersoft;
/**
 *	@description	
 */
class Request
{
    private $request, $get, $post, $put, $delete, $patch, $session, $cookie;
	/**
	 *	@description	
	 */
	public function __construct()
	{
        $this->get  =   $this->filter($_GET);
        $this->post =   $this->filter($_POST);
        $this->request =   $this->filter($_REQUEST);
        $this->put =   (isset($_PUT))? $this->filter($_PUT) : [];
        $this->delete =   (isset($_DELETE))? $this->filter($_DELETE) : [];
        $this->patch =   (isset($_PATCH))? $this->filter($_PATCH) : [];
	}
	/**
	 *	@description	
	 */
	private function filter($array):? array
	{
        if(!is_array($array))
            return trim($array);
        $new    =   [];
        foreach($array as $key => $value) {
            $new[$key]    =   (is_array($value))? $this->filter($value) : trim($value);
        }
        
        return $new;
	}
	/**
	 *	@description	
	 */
	public function __call($method, $args = false)
	{
        $method =   strtolower(preg_replace('/^get/', '', $method));
        $use    =   $this->{$method};
        
        if(!empty($args[0]))
            return ($use[$args[0]])?? null;
        
        return $use;
	}
}