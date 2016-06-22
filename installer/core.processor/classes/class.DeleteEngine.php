<?php
	class	DeleteEngine
		{
			private	static	$singleton;
			
			public	static	function addTarget($place = false)
				{
					if(empty(self::$singleton))
						self::$singleton	=	new recursiveDelete();
					
					$register	=	new RegisterSetting();
					$engine		=	self::$singleton;
					
					if(is_array($place)) {
							foreach($place as $value) {
									if(is_file($value) || is_dir($value))
										$engine->addTarget($value);
									else
										$register->UseData("DeleteEngine",array("target"=>$value))->SaveTo("settings");
								}
							
							$engine->deleteAll();
						}
					else {
							if(is_file($place) || is_dir($place))
								$engine->delete($place);
							else
								$register->UseData("DeleteEngine",array("target"=>$place))->SaveTo("settings");
						}
				}
		}