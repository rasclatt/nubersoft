<?php
namespace Nubersoft\ShoppingCart;

class Payment extends \Nubersoft\ShoppingCart
	{
		protected	static $cardTypes =	array(
			'VI'=>array(
					'pattern'=>'/^4[0-9]{12}(?:[0-9]{3})?$/',
					'name'=>'Visa',
					'abbr'=>'VI'
				),
			'CA'=>array(
					'pattern'=>'^(?:5[1-5][0-9]{2}|222[1-9]|22[3-9][0-9]|2[3-6][0-9]{2}|27[01][0-9]|2720)[0-9]{12}$',
					'name'=>'MasterCard',
					'abbr'=>'CA'
				),
			'AX'=>array(
					'pattern'=>'/^3[47][0-9]{13}$/',
					'name'=>'American Express',
					'abbr'=>'AX'
				),
			'DS'=>array(
					'pattern'=>'/6(?:011|5[0-9]{2})[0-9]{12}$/',
					'name'=>'Discover',
					'abbr'=>'DS'
				)	
			);
		
		public	function getCCTypeByNumber($value)
			{
				foreach(self::$cardTypes as $card) {
					if(preg_match($card['pattern'],$value))
						return $card;
				}
			}
			
		public	function addCard($name,$abbr,$pattern)
			{
				self::$cardTypes[$abbr]	=	array(
					'pattern'=>$pattern,
					'name'=>$name,
					'abbr'=>$abbr
				);
				
				return $this;
			}
			
		public	function getCardTypes($json = false)
			{
				return ($json)? json_encode(self::$cardTypes) : self::$cardTypes;
			}
	}