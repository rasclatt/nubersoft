<?php
function printpre()
{
	$args		=	func_get_args();
	$value		=	(empty($args[0]))? false : $args[0];
	$backtrace	=	(empty($args[1]));
	
	if($backtrace) {
		$str		=	array_map(function($v){
			$func	=	(isset($v['function']))? $v['function'] : false;
			if(isset($v['function']) && strtolower($v['function']) == 'printpre')
				$func	=	false;
			else {
				if(stripos($func,'closure') !== false) {
					$func	=	'<span class="red">Anon Function</span>';
				}
				if(!empty($v['class']))
					$func	=	'<span class="blue">'.$v['class'].'</span>::<span class="green">'.$func.'</span>';

				$func	.=	' – ';
			}
			
			if(empty($v['file']))
				$v['file']	=	'EVAL::runtime';
			
			if(empty($v['line']))
				$v['line']	=	'Interal app pointer';
			
				return $func.'<span class="gray">'.str_replace(NBR_ROOT_DIR, '', $v['file']).'</span> ('.$v['line'].')';

		}, debug_backtrace());

		return '<pre class="code"><span class="pre-value">'.print_r($value,1).'</span><br />'.implode('<br />'.PHP_EOL, $str).'</pre>';
	}
	
	return '<pre class="code">'.print_r($value,1).'</pre>';
}