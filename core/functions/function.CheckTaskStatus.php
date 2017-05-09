<?php
/*Title: CheckTaskStatus()*/
/*Description: This function is used in `render_header()` function to check if a variable and matching value line up to create a new session variable. This is not necessarily valuable outside of that scope.*/

	function CheckTaskStatus($array = false)
		{
			
			AutoloadFunction('check_empty');
			$content	=	(is_array($array))? (!empty($array['value']))? $array['value']:"":$array;
			$toggle		=	(is_array($array))? (!empty($array['toggle']))? $array['toggle']:"":false;
			$trigger	=	(is_array($array))? (!empty($array['hidden_task_trigger']))? $array['hidden_task_trigger']:false:false;
			$task		=	(is_array($array))? (!empty($array['hidden_task']))? $array['hidden_task']:false:false;
					
			if($trigger != false) {
					if($task != false) {
							if($task == 'session') {
									if(!isset($_SESSION[$trigger])) {
											if(check_empty($_REQUEST,$trigger,$task)) {
													$_SESSION['tasks'][$trigger]	=	true;
													return Safe::decode(Safe::decode($content));
												}
										}
									elseif(check_empty($_SESSION,$trigger,'off')) {
											$_SESSION['tasks'][$trigger]	=	NULL;
											unset($_SESSION['tasks'][$trigger]);
											return false;
										}
								}
						}
				}
		}
?>