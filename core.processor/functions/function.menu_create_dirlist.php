<?php
function menu_create_dirlist($array = false,$id = false)
	{
		
		if(empty($array) || !is_array($array))
			return;
			
		AutoloadFunction('nQuery,organize,menu_recurse_url');
		$nubquery	=	nQuery();
		$iterated	=	new ArrayObject($array);
		
		foreach($iterated as $keys => $values) {
				$struct[$keys]	=	$values;
			}
		
		$iterator	=	new RecursiveIteratorIterator(new RecursiveArrayIterator($struct),RecursiveIteratorIterator::SELF_FIRST);
		$all		=	organize($nubquery->select(array("parent_id","unique_id","link"))->from("main_menus")->fetch(),'unique_id');
		$sets 		=	array();
		
		foreach ($iterator as $k => $v) {
				// Not at end: show key only
				if ($iterator->hasChildren()) {
						//echo $k."/";
						// At end: show key, value and path
					}
				else {
						for ($conc = '', $p = array(), $i = 0, $z = $iterator->getDepth(); $i <= $z; $i++) {
								//echo 
								$key	=	$iterator->getSubIterator($i)->key();
								$conc	.=	"/".$all[$key]['link'];
								
								if(!in_array($conc,$sets))
									$sets[$key]	=	str_replace(_DS_._DS_,_DS_,$conc._DS_);
							}
					}
			}
			
		if($id != false)
			return	(isset($sets[$id]))? $sets[$id]:false;
		
		return $sets;
	}