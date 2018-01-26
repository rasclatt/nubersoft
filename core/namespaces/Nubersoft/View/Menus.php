<?php
namespace Nubersoft\View;

class Menus extends \Nubersoft\View\Menus\Controller
{
	public	function getHtml($wrapper='div',$subwrapper='div',$class='menu-button',$func=false)
	{
		$matrix	=	$this->fetchAllMatrix();
		
		if(empty($matrix))
			return false;
		
		$html	=	(is_callable($wrapper))? $wrapper('open') : ['<'.$wrapper.' class="'.$class.'">'];
		
		if(!is_array($html))
			$html	=	[$html];
		$this->createNestedHtml($matrix,$subwrapper,$wrapper,$func,$html);
		$html[]	=	(is_callable($wrapper))? $wrapper('close') : '</'.$wrapper.'>';
		return implode(PHP_EOL,$html);
	}
	
	public	function createNestedHtml($array,$subwrapper,$wrapper,$func,&$html,$ignoreOff=true)
	{
		foreach($array as $row) {
			if($row['in_menubar'] == 'on' || !empty($ignoreOff)) {
				if(!empty($row['children'])) {
					$hasMenuChildren	=	(empty($ignoreOff))? $this->hasMenuBarActiveChild($row['children']) : $ignoreOff;
					
					$html[]	=	(is_callable($subwrapper))? $subwrapper($row,$array) : '<'.$subwrapper.'>';
					$html[]	=	(is_callable($func))? $func($row,$this) : '<a href="'.$this->safe()->decode($row['full_path']).'">'.$row['menu_name'].'</a>';

					
					if($hasMenuChildren) {
						$html[]	=	(is_callable($wrapper))? $wrapper('sub_open',$row,$array) : '<'.$wrapper.'>';
						$this->createNestedHtml($row['children'],$subwrapper,$wrapper,$func,$html);
						$html[]	=	(is_callable($wrapper))? $wrapper('sub_close',$row,$array) : '</'.$wrapper.'>';
					}
					
					if(!is_callable($subwrapper))
						$html[]	=	'</'.$subwrapper.'>';
				}
				else {
					$cont	=	(is_callable($func))? $func($row,$this) : '<a href="'.$this->safe()->decode($row['full_path']).'">'.$row['menu_name'].'</a>';
					$html[]	=	(is_callable($subwrapper))? $subwrapper($row,$array) : '<'.$subwrapper.'>'.$cont.'</'.$subwrapper.'>';
				}
			}
		}
	}
	
	public	function hasMenuBarActiveChild($array)
	{
		if(!is_array($array))
			return false;
		
		return (!empty(array_filter(array_map(function($arr){
			return ($arr['in_menubar'] == 'on');
		},$array))));
	}
}