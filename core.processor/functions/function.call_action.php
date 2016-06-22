<?php
/*Title: call_action()*/
/*Description: This function just matches a request to see if the action is being called.*/
/*Example: 
`AutoloadFunction('call_action');
if(call_action('command')) { // Do stuff because $_POST['command'] exists }`*/
/*Example 2 (Use an array):
`$array = array('tester'=>'Just a test');
if(call_action('tester',$array)) { // Do something because $array['tester'] exists }`
*/
	function call_action($key = false, $type = 'post')
		{
			
			if($key == false)
				return false;
			
			if(!is_array($type)) {
					switch ($type) {
							case ('post'):
								return (isset($_POST[$key]))? true:false;
							case ('get'):
								return (isset($_GET[$key]))? true:false;
							case ('request'):
								return (isset($_REQUEST[$key]))? true:false;
						}
						
					return false;
				}
				
			return (isset($type[$key]))? true:false;	
		}
?>