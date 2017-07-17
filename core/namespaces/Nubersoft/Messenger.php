<?php
namespace Nubersoft;

class Messenger extends \Nubersoft\nApp
	{
		const	LOGGED_OUT	=	'You must be logged in to view this content';
		/*
		**	@description	Alerts the user regarding logged in state.
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
		/*
		**	Get alerts from data node
		*/
		public	function getAllAlerts($key = false)
			{
				$errors				=
				$warnings			=	array();
				$err				=	$this->toArray($this->getError());
				$inc				=	$this->toArray($this->getIncidental());
				
				if(!empty($err))
					$this->flattenArrayByKey($err,$errors,'msg');
				
				if(!empty($inc))
					$this->flattenArrayByKey($inc,$warnings,'msg');
				
				$array['warnings']	=	$warnings;
				$array['errors']	=	$errors;
				
				return (!empty($key) && isset($array[$key]))? $array[$key] : $array;
			}
		/*
		**	Get alerts from data node
		*/
		public	function getAlertsByKind($key = false,$type = false)
			{
				$errors				=
				$warnings			=	array();
				$err				=	$this->toArray($this->getError());
				$inc				=	$this->toArray($this->getIncidental());
				
				if(!empty($err) && isset($err[$key]))
					$this->flattenArrayByKey($err,$errors,'msg');
				
				if(!empty($inc) && isset($inc[$key]))
					$this->flattenArrayByKey($inc,$warnings,'msg');
				
				$array['warnings']	=	$warnings;
				$array['errors']	=	$errors;
			
				if(!empty($type))
					return (isset($array[$type]))? $array[$type] : array();
					
				return $array;
			}
		
		public	function toAlert($msg, $action = 'general', $opts = false, $type = true, $toSess = true)
			{
				if($this->isAjaxRequest())
					$this->ajaxAlert($msg);
					
				$msgArr	=	array('msg'=>$msg);
				$array	=	(is_array($opts) && !empty($opts))? array_merge($msgArr,$opts) : $msgArr;
				if($type)
					$this->saveIncidental('alerts',array($action=>$array));
				else
					$this->saveError('alerts',array($action=>$array));
				
				if($toSess)
					$this->setSession('alerts',array($action=>$array),true);
			}
	}