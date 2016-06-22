<?php
namespace Nubersoft;

class	nDI
	{
		private	$dynamics;
		
		public	static	function get($str,$inj = false)
			{
				return new $str($inj);
			}
		
		public	function getDynamics()
			{
				return $this->dynamics;
			}
	}