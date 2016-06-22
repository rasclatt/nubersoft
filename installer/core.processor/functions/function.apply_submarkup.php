<?php

	function apply_submarkup($match)
		{
			register_use(__FUNCTION__);
			AutoloadFunction('get_markup_command');
			$command	=	strtolower($match[1]);
			$string		=	$match[2];
			$array		=	get_markup_command($command);
			
			// Strip markup
			$strrep		=	str_replace(array("[","]"),"",$string,$strcnt);
			
			if(!$array) {
				if($command == 'date')
					return date($strrep);
				elseif($command == 'time')
					return date("g:i:s a",strtotime($strrep));	
			}
			
			// Single array
			if(is_array($array)) {
				// If exactly 2 replaced
				if($strcnt == 2) {
					// Check if result is an array+key
					if(isset($array[$strrep]) && !is_array($array[$strrep]))
						return $array[$strrep];
				}
			}
			
			// Multi-dimensional array
			if((strpos($string,'[') !== false) && isset($array)) {
				preg_match_all( "/\[([a-zA-Z0-9]+)]/", $string, $matches );
				// Recursive search engine will find and match keys
				$Search		=	new RecurseSearch();
				$countSub	=	count($matches[1]);
				$baseKey	=	array_shift($matches[1]);
				if(isset($matches[1]) && !empty($matches[1])) {
				
					$val	=	(isset($array[$baseKey]))? $Search->Find($array[$baseKey],$matches[1])->item:"";
					
					if(!empty($val))
						return $val;
				}
			}
		}