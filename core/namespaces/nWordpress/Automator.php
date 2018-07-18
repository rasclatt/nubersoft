<?php
namespace nWordpress;

class Automator extends \Nubersoft\nApp
{
	public	function automate($content, $useData=false, $array=false)
	{
		if(!is_array($array))
			$array	=	[];
		
		$val	=	preg_replace_callback('/~[^~]+~/i',function($v) use ($useData, &$array) {
			$v	=	trim($v[0],'~');
			
			if(preg_match('/app::/i',$v)) {
				$v	=	preg_replace('/app::/i','',$v);
				if(function_exists($v)) {
					$val	=	$v($useData);
					
					if(is_array($val)) {
						$array	=	$val;
						return '';
					}
					else
						return $val;
				}
			}
			elseif(preg_match('/workflow::/i',$v)) {
				$v	=	preg_replace('/workflow::/i','',$v);
			}
			elseif(preg_match('/include::/i',$v)) {
				$v	=	preg_replace('/include::|~/i','',$v);
				if(is_file(ABSPATH.DS.$v)) {
					echo $this->render(ABSPATH.DS.$v);
					exit;
				}
			}
			elseif(preg_match('/function::/i',$v)) {
				$v	=	preg_replace('/function::|~/i','',$v);
				$v	=	array_values(array_filter(array_map(function($v){
					return rtrim($v,']');
				},explode('[',$v))));
				
				if(count($v) > 1) {
					$func	=	$v[0];
					unset($v[0]);
					$v	=	array_merge($v,[$useData]);
					
					$val	=	$func(...array_values($v));
					if(is_array($val)) {
						$array	=	$val;
						return '';
					}
					else
						return $val;
				}
				else {
					$func	=	$v[0];
					$val	=	$func($useData);
					
					if(is_array($val)) {
						$array	=	$val;
						return '';
					}
					else
						return $val;
				}
			}
			elseif(preg_match('/datanode::/i',$v)) {
				$v	=	preg_replace('/datanode::|\["|"\]/i','',$v);
				$xp	=	array_map(function($v){
					return array_map(function($v){
						return trim($v,'"'); 
					},explode('=',$v));
				},explode(',',$v));
				$new	=	[];
				foreach($xp as $key => $row) {
					$new[$row[0]]	=	$row[1];
				}
				$parent		=	(!empty($new['parent']))? $new['parent'] : '';
				$datanode	=	$this->toArray($this->getDataNode($parent));
				
				if(!empty($new['child']))
					return (isset($datanode[$new['child']]))? $datanode[$new['child']] : false;
				else
					return $datanode;
			}
			else
				return '~'.$v.'~';
			
		},$content);
		
		return (!empty($array))? $array : $val;
	}
}