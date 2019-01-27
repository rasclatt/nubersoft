<?php
namespace Nubersoft\nRouter;

trait enMasse
{
	public	function isAjaxRequest()
	{
		return (new \Nubersoft\nRouter\Controller())->{__FUNCTION__}();
	}
	
	public	function ajaxResponse($arg, $modal = false)
	{
		return (new \Nubersoft\nRouter\Controller())->{__FUNCTION__}($arg, $modal);
	}
	
	public	function getPage($arg)
	{
		return (new \Nubersoft\nRouter\Controller())->{__FUNCTION__}($arg);
	}
	
	public	function redirect($arg)
	{
		return (new \Nubersoft\nRouter\Controller())->{__FUNCTION__}($arg);
	}
}