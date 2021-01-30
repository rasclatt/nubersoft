<?php
namespace Nubersoft\Exception;
/**
 *	@description	
 */
class Ajax extends \Exception
{
	/**
	 *	@description	
	 */
	public	function __construct()
	{
        header('Content-Type: application/json');
	}
	/**
	 *	@description	
	 */
	public function ajaxResponse()
	{
        http_response_code($this->errorCode());
        
        die(json_encode([
            'msg' => $this->getHelper('ErrorMessaging')->getMessageAuto($this->getMessage())
        ]));
	}
}