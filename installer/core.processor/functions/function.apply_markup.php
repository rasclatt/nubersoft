<?php
/*Title: apply_markup()*/
/*Description: Used in conjunction with the `use_markup()` function. It is used in context with a `preg_replace_callback()` function.*/

	function apply_markup($match)
		{
			if(isset($match[0])) {
				$replaced	=	str_replace("~","",$match[1]);
				if(preg_match('/eval::/i',$replaced)) {
					$allow	=	(defined("ALLOW_EVAL"))? ALLOW_EVAL : false;
					if($allow) {
						$replaced	=	str_replace(array("eval::","EVAL::"),"",$replaced);
						$command	=	Safe::decode($replaced);
						ob_start();
						eval($command);
						$data		=	ob_get_contents();
						ob_end_clean();
						return $data;
					}
				}
				elseif(preg_match('/app::/i',$replaced)) {
					$replaced	=	str_replace(array("app::","APP::"),"",$replaced);
					$settings	=	explode("[",trim($replaced,"]"));
					$settings	=	array_filter($settings);
					$function	=	array_shift($settings);
					$keypairs	=	(isset($settings[0]))? $settings[0]:false;
					
					if(!function_exists($function))
						AutoloadFunction($function);
					
					if(function_exists($function)) {
						// Match key value pairs
						preg_match_all('/([^\=]{1,})="([^"]{1,})"/i',$keypairs,$pairs);
						// If there are key value pairs loop through the pairs
						if(!empty($pairs[0])) {
							$pairs[0]	=	NULL;
							
							$i = 0;
							foreach($pairs[1] as $values) {
								$values	=	trim($values);
								$array[$values]	=	(isset($pairs[2][$i]))? trim($pairs[2][$i]):false;
								$i++;
							}
						}
		
						$array	=	(isset($array))? $array : false;
						$access	=	true;
						// This will disallow access to full core functions
						if(defined("F_ACCESS") && F_ACCESS !== true) { 
							if(is_file(NBR_FUNCTIONS."/function.{$function}.php"))
								$access	=	false; 
						}
						
						return ($access)? $function($array) : "";
					}
						
					return $replaced;
				}
				elseif(strpos($replaced,"[") === false) {
					if(is_file(NBR_ROOT_DIR.$replaced)) {
						ob_start();
						include(NBR_ROOT_DIR.$replaced);
						$data	=	ob_get_contents();
						ob_end_clean();
						
						return $data;
					}
					else {
						AutoloadFunction('get_markup_command');
						$array	=	get_markup_command($replaced);
						if(is_array($array))
							return printpre($array,"Array: ".strtoupper($replaced));
						else
							return $replaced;
					}
						
					return;
				}
				
				
				AutoloadFunction('apply_submarkup');
				return preg_replace_callback('/([a-z0-9]{1,})::([a-z0-9\/\.\_\-\>\[\]\s\:\,]{1,})/i','apply_submarkup',$replaced);
			}
		}