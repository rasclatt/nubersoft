<?php
function filter_error_reporting($errormsg = false)
	{
		if(preg_match_all("/(duplicate)|('[^']{1,}')/i",$errormsg,$match)) {
			
			$table	=	nApp::getColumns(nApp::getDefaultTable());
			
			foreach($match[0] as $values) {
				if(strpos($values,"'") !== false)
					$new[]	=	trim($values,"'");
			}
			
			$diff	=	array_diff($new,array_diff($new,$table));
			
			return json_encode(array("dup"=>$diff));
		}
		
		return json_encode(array("sql"=>$errormsg));
	}