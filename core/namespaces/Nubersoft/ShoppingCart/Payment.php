<?php
namespace Nubersoft\ShoppingCart;

class Payment extends \Nubersoft\ShoppingCart
	{
		public	function getCCTypeByNumber($value)
			{
				if(preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/',$value))
					return array("VI",'Visa');
				elseif(preg_match('/^(?:5[1-5][0-9]{2}|222[1-9]|22[3-9][0-9]|2[3-6][0-9]{2}|27[01][0-9]|2720)[0-9]{12}$/',$value))
					return array("CA",'MasterCard');
				elseif(preg_match('/^3[47][0-9]{13}$/',$value))
					return array("AX",'American Express');
				elseif(preg_match('/^6(?:011|5[0-9]{2})[0-9]{12}$/',$value))
					return array("DS",'Discover');
				return
					false;
			}
	}