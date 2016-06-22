<?php
	function FetchMixedId($range = 10,$prefix = "")
		{
			register_use(__FUNCTION__);
			if(!function_exists("upperset")) {
				function upperset(&$item1,$key)
					{
						$item1	=	strtoupper($item1);
					}
			}
			$alpha		=	range('a','z');
			$alpha_l	=	$alpha;
			$alpha_u	=	$alpha;
			array_walk($alpha_u,'upperset');
			$num		=	range(1,20);
			
			$merged		=	array_merge($alpha_l,$alpha_u,$num);
			shuffle($merged);
			shuffle($merged);
			$string		=	implode("",$merged);
			$count		=	strlen($string);
			$cut		=	($count >= $range)? $range:$count;
			
			if($range < 0)
				$cut	=	1;
			
			return $prefix.substr($string,0,$cut);
		}
?>