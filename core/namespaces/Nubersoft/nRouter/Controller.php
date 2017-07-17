<?php
namespace Nubersoft\nRouter;

class Controller extends \Nubersoft\nApp
	{
		/*
		**	@description	Creates a autoloader for classes
		**	@param	$path	[string | anon func]	This can be a path where classes can be found OR<br>
		**					a callable function that the spl uses to create a loader
		*/
		public	function addNamespace($path)
			{
				$nApp	=	$this;
				
				if(is_callable($path))
					spl_autoload_register($path);
				else {
					spl_autoload_register(function($class) use ($path,$nApp) {
						
						if(is_array($path)) {
							foreach($path as $namespace) {
								$classPath	=	$nApp->toSingleDs($namespace.DS.str_replace('\\',DS,$class)).'.php';
						
								if(is_file($classPath))
									require_once($classPath);
								}
						}
						else {
							$classPath	=	$nApp->toSingleDs($path.DS.str_replace('\\',DS,$class)).'.php';
						
							if(is_file($classPath))
								require_once($classPath);
						}
					});
				}
				
				return $this;
			}
	}