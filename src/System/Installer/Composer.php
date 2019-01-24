<?php
namespace Nubersoft\System\Installer;

class Composer
{
	public	static	function creatRoot()
	{
		$DS		=	DIRECTORY_SEPARATOR;
		$root	=	__DIR__.$DS.'..'.$DS.'..'.$DS.'..'.$DS.'..'.$DS.'..'.$DS.'..'.$DS.'..';
		
		file_put_contents($root.$DS.'test.txt', print_r(scandir($root), 1));
	}
}