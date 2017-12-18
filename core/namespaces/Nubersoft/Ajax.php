<?php
namespace Nubersoft;

class Ajax extends \Nubersoft\Singleton
{
	/*
	**	@desciption	Fetches the ajax container name
	*/
	public	function getContainer($key = false)
	{
		$main		=	(!empty($key))? $key : 'main';
		$default	=	'#loadspace';
		$registry	=	nAll::call()->getRegistry('ajax');

		if(empty($registry))
			return $default;

		$find	=	nAll::call()->findKey($registry,$main)->getKeyList();

		return (!empty($find[0]))? $find[0] : $default;
	}
	/**
	*	@description	Creates a json response and dies for ajax-based requests
	*/
	public	static	function ajaxResponse($array)
	{
		return die(json_encode($array));
	}
	/**
	*	@description	Creates a javascript-based "redirect"
	*/
	public	static	function ajaxRouter($link)
	{
		self::ajaxResponse(array(
			'html'=>array(
				'<script>window.location="'.$link.'";</script>'
			),
			'sendto'=>array(
				'body'
			)
		));
	}
	/**
	*	@description	General alerting for ajax responses
	*/
	public	static	function ajaxAlert($message,$merge = false)
	{
		if(!self::isAjaxRequest())
			return false;

		$arr	=	array('alert'=>$message,'html'=>array(''),'sendto'=>array('.nbr_action_loader'));

		if(is_array($merge)) {
			if(isset($merge['html'])) {
				$arr['html']	=	array_merge($merge['html'],$arr['html']);
				$arr['sendto']	=	array_merge($merge['sendto'],$arr['sendto']);
			}
			else {
				$arr	=	array_merge($arr,$merge);
			}
		}

		self::ajaxResponse($arr);
	}
	/**
	*	@description	Checks if the request is ajax-based
	*/
	public	static	function isAjaxRequest($type = 'HTTP_X_REQUESTED_WITH')
	{
		# Allow changing by define
		if(defined('AJAX_HEADER_LOOKUP'))
			$type	=	AJAX_HEADER_LOOKUP;
		# If force is set
		if(defined('BROWSER_FORCED')) {
			# If request is set to force request to browser
			if(BROWSER_FORCED === true)
				# Ajax is not required
				return false;
		}
		# Check if the server key is set
		if(!empty(nApp::call()->getDataNode('_SERVER')->{$type}))
			return true;
		
		return (!empty($_SERVER[$type]));
	}
}