<?php
namespace Nubersoft\Conversion;

class Money extends \Nubersoft\Singleton
{
	/**
	*	@description	Simple dollar rendering
	*/
	public	static	function toDollar($string,$curr = '$',$dec=2,$sep=',',$dectype='.',$front=true)
	{
		$string	=	preg_replace('/[^0-9\.]/','',$string);
		$number	=	number_format($string,$dec,$dectype,$sep);
		return ($front)? $curr.$number : $number.$curr;
	}
}