<?php
/*Title: compare()*/
/*Description: This function just matches two values based on the operator.*/
/*Example: 
`$_GET['action'] = 'test';
$array['command'] = 'test';
if(compare($_GET['action'],$array['command'])) { // Do something because values match }`
*/

	function compare($arg1 = 0,$arg2 = 0,$comp = '=')
		{
			register_use(__FUNCTION__);
			switch ($comp) {
					case ('=') :
						return ($arg1 == $arg2);
					case ('==') :
						return ($arg1 == $arg2);
					case ('===') :
						return ($arg1 === $arg2);
					case ('>') :
						return ($arg1 > $arg2);
					case ('<') :
						return ($arg1 < $arg2);
					case ('<=') :
						return ($arg1 <= $arg2);
					case ('>=') :
						return ($arg1 >= $arg2);
				}
				
			return false;
		}