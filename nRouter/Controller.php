<?php
namespace Nubersoft\nRouter;

class Controller extends \Nubersoft\nRouter
{
	public	function setHeader()
	{
		$args	=	func_get_args();
		$exit	=	(isset($args[1]))? $args[1] : false;
		if(!is_array($args[0]))
			$args[0]	=	[$args[0]];
		
		foreach($args[0] as $header) {
			header($header);
		}
		
		if($exit)
			exit;
		
		return $this;
	}
	
	public	function redirect($location)
	{
		$this->setHeader('Location: '.$location, true);
	}
	
	public	function isAjaxRequest()
	{
		$type	=	$this->getDataNode('request');
		
		if(!empty($type))
			return ($type == 'ajax');
		else
			return (strtolower($this->getServer('HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest');
	}
	
	public	function ajaxResponse($item)
	{
		if(is_array($item) || is_object($item))
			die(json_encode($item));
		else
			die($item);
	}
}