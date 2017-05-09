<?php
namespace Nubersoft;

abstract class	Singleton
	{
		protected	static	$singleton;
		
		public	function __construct()
			{
				if(!is_object(self::$singleton))
					self::$singleton	=	$this;
				
				return self::$singleton;
			}
	}