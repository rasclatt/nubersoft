<?php
/*Title: compare_post_data()*/
/*Description: This function uses a value-filled `array` and matches an `associative` array to the keys from the array*/
/*Example: 
`$matching = array('key1','key2','key3');
$values = array('key5'=>'value is 5','key2'=>'value is 2','key6'=>'value is 6','key1'=>'value is 1');
$new = compare_post_data($matching, $values);`*/
/*Gives you: `Array ([0]=>'value is 1',[1]=>'value is 2')`*/

	function compare_post_data($array = false, $compare = array())
		{
			register_use(__FUNCTION__);
			if(!is_array($array))
				return;
			
			foreach($array as $key) {
					// If post key exists, return column
					if(!empty($compare[$key]))
						$valid[]	=	$key;
				}
			
			return	(isset($valid))? $valid:false;
		}
?>