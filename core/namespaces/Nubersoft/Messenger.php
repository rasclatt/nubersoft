<?php
namespace Nubersoft;

class Messenger extends \Nubersoft\nApp
{
	const	LOGGED_OUT	=	'You must be logged in to view this content';
	/**
	*	@description	Alerts the user regarding logged in state.
	*/
	public	function alertLoggedIn($msg = false,$wrapper = 'nbr_comment')
	{
		$msg	=	(empty($msg))? self::LOGGED_OUT : $msg;

		if(!$this->isLoggedIn()) {
			if($this->isAjaxRequest())
				$this->ajaxResponse(array('alert'=>$msg));
			else
				return '<span class="'.$wrapper.'">'.$msg.'</span>';
		}
	}
	/**
	*	@description	Get alerts from data node
	*/
	public	function getAllAlerts($key = false)
	{
		return $this->getAlertsByKind($key);
	}
	/**
	*	@description	Get alerts from data node
	*/
	public	function getAlertsByKind($key=false,$type = false)
	{
		$default			=
		$success			=
		$incidentals		=
		$errors				=	[];
		if(!empty($key)) {
			$arr	=	$this->getSystemMessages($key);
			if(empty($arr))
				return $default;
			
			if(!empty($type))
				return (!empty($arr[$type]))? $arr[$type] : $default;
			
			$this->extractAll($arr,$default);
			
			return $default;
		}
		
		$alerts				=	$this->getSystemMessages();
		
		if(!empty($alerts['alert']))
			$this->extractAll($alerts['alert'],$incidentals);
		
		if(!empty($alerts['error']))
			$this->extractAll($alerts['error'],$errors);
		
		if(!empty($alerts['success']))
			$this->extractAll($alerts['success'],$success);
		
		$array['warnings']	=	$incidentals;
		$array['errors']	=	$errors;
		$array['success']	=	$success;
		
		if(!empty($key))
			return (isset($array[$key]))? $array[$key] : $default;

		return $array;
	}
	/**
	*	@description	
	*/
	public	function toAlert($msg, $action = 'general', $opts = false, $type = true, $presist = false)
	{
		if($this->isAjaxRequest())
			$this->ajaxAlert($msg);
		
		if($type)
			$meth	=	($presist)? "toMsgCoreAlert" : "toMsgAlert";
		else
			$meth	=	($presist)? "toMsgCoreError" : "toMsgError";

		$this->{$meth}($msg,$action);
		
		return $this;
	}
}