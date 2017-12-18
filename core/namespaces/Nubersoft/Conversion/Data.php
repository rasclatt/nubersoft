<?php
namespace Nubersoft\Conversion;

class Data extends \Nubersoft\Singleton
{
	/**
	*	@description	Basic human-readable file size builder
	*/
	public	static	function getByteSize($val,$settings = false)
	{
		$to			=	(!empty($settings['to']))? strtoupper($settings['to']) : 'KB';
		$from		=	(!empty($settings['from']))? strtoupper($settings['from']) : 'MB';
		$ext		=	(!empty($settings['extension']) || !empty($settings['ext']));
		$round		=	(!empty($settings['round']) && is_numeric($settings['round']))? $settings['round']: false;
		# Match it
		preg_match('/^([0-9]{1,})([a-z]{1,})$/i',$val,$match);
		if(!empty($match)) {
			$num		=	(!empty($match[1]))? $match[1] : $match[0];
			$type		=	(!empty($match[2]))? $match[2] : $from;
		}

		$num		=	$val;
		$type		=	$from;
		$div		=	1024;
		$b			=	1;
		$kb			=	$div*$b;
		$mb			=	$div*$kb;
		$gb			=	$div*$mb;
		$tb			=	$div*$gb;

		$conv['B']	=	$b;
		$conv['KB']	=	$kb;
		$conv['MB']	=	$mb;
		$conv['GB']	=	$gb;
		$conv['TB']	=	$tb;

		if(!isset($conv[$type])) {
			trigger_error('FROM value not valid: '.$type,E_USER_NOTICE);
			return false;
		}
		elseif(!isset($conv[$to])) {
			trigger_error('TO value not valid: '.$to,E_USER_NOTICE);
			return false;
		}

		$currVal	=	$val*$conv[$type];
		$returnVal	=	$currVal/$conv[$to];

		if($round)
			$returnVal	=	round($returnVal,$round);

		return ($ext)? $returnVal.$to : $returnVal;
	}
	/**
	*	@description	Takes a value (string, int, bool) and determines it's BOOL value
	*/
	public	static	function getBoolVal($val)
	{
		if(is_array($val) || is_object($val))
			return $val;

		if(empty($val))
			return false;
		elseif(is_bool($val))
			return $val;
		elseif(is_int($val)) {
			if($val == (int) 0 || $val == (int) 1)
				return ($val == (int) 1);
		}
		else {
			$subVal	=	strtolower($val);
			if($subVal == '1' || $subVal == '0')
				return ($subVal == '1');
			elseif($subVal == 'on' || $subVal == 'off')
				return ($subVal == 'on');
			elseif($subVal == 'true')
				return true;
			elseif($subVal == 'false')
				return false;
		}

		return $val;
	}
}