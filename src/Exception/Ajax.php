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
	public function ajaxResponse()
	{
        header('Content-Type: application/json');
        http_response_code($this->getCode());
        
        die(json_encode([
            'success' => ($this->getCode() == 200),
            'msg' => "Exception thrown: {$this->getMessage()}"
        ]));
	}
}