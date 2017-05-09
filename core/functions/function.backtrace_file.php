<?php
	function backtrace_file($return = false)
		{
			
			$backtrace	=	debug_backtrace();
			$current	=	(!empty($backtrace))? $backtrace : false;
			
			$filter[]	=	"file";
			$filter[]	=	"line";
			$filter[]	=	"function";
			$filter[]	=	"class";
			
			if(is_array($current)) {
					$i = 0;
					foreach($current as $key => $row) {
							foreach($row as $keys => $values) {
									if(in_array($keys,$filter))
										$new[$i][$keys]	=	$values;
								}
							
							$i++;
						}
				}
			
			$backtrace	=	NULL;
			$current	=	NULL;
			unset($backtrace);
			unset($current);
			
			return (isset($new))? $new:false;
		}
?>