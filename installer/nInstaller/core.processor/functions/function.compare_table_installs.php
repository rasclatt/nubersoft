<?php
	function compare_table_installs($col_setting = array(),$currCols = array(),$table = false)
		{
			AutoloadFunction('nQuery');
			$nubquery	=	nQuery();
			register_use(__FUNCTION__);
			$test	=	array_filter(explode(",",$col_setting));
			foreach($test as $sqlrow) {
					$match = false;
					preg_match('/(\`.*\`)([^\`]{1,})/',$sqlrow,$match);
					
					if(isset($match[1])) {
							$match[1]		=	str_replace("`","",$match[1]);
							
							$match[0]		=	trim($match[0]);
							
							if(!in_array($match[1],$currCols)) {
									$nubquery->addCustom("alter table `$table` add column ".$match[0],true)->write();
								}
						}
				}
		}
?>