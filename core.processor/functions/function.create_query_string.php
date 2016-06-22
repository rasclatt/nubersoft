<?php
/*Title: create_query_string()*/
/*Description: This function takes an array such as `$_GET` and creates a query string. The query string can be filtered using an `array` or `string` value. By default it removes `admintools`. By adding `true` to the last setting, you can make the variable only include, or only remove key/value pairs. */
/*Example: 
`$_GET['key1'] = 'No thank you';
$_GET['key2'] = 'Yes please';
// Option 1
echo create_query_string('key1',$_GET);
// Option 2
echo create_query_string('key1',$_GET,true);
// Option 1 Gives you
key2=Yes+please
// Option 2 Gives you
key1=No+thank+you`*/

	function create_query_string($notvar = false,$request = array(),$keep = false)
		{
			
			$type		=	$request;
			$filter		=	(!is_array($notvar))? array($notvar):$notvar;
			
			if(is_array($type) && !empty($type)) {
				
					foreach($type as $key => $value) {
							
							if(strpos($key,"/") !== false) {
									unset($type[$key]);
									continue;
								}
							
							if(in_array($key,$filter)) {
									if($keep == false)
										unset($type[$key]);
								}
							else {
									if($keep != false)
										unset($type[$key]);
								}
						}
				}
			
			if(isset($type) && is_array($type)) {		
					$useAnd	=	(!empty($type))? "&":"";
					return $useAnd.http_build_query($type);
				}
		}
?>