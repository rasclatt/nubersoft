<?php
namespace Nubersoft\System\View;
/**
 *	@description	
 */
class Menu extends \Nubersoft\nRender
{
	use \Nubersoft\Settings\enMasse;
	
	/**
	 *	@description	
	 */
	public	function recurseMenu($array, &$new)
	{
		foreach($array as $value) {
			if(!isset($new[$value])) {
				$new[$value]	=	[];
			}
			array_shift($array);
			$this->recurseMenu($array, $new[$value]);
			return false;
		}
	}
	
	public	function recurseMenuBuild($new, $prev = '')
	{
		$html	=	PHP_EOL.'<ul>';
		foreach($new as $key => $children) {
			$html .=	'<li'.(!empty($children)? ' class="menu-group"' : '').'><a href="'.$prev.'/'.$key.'/">'.ucwords(str_replace(['_', '-'],' ',$key)).'</a>'.(!empty($children)? $this->recurseMenuBuild($children, $prev.'/'.$key) : "").'</li>'.PHP_EOL;
		}
		$html	.=	'</ul>';

		return $html;
	}
	/**
	 *	@description	
	 */
	public	function create($menuset = false)
	{
		if(!$menuset)
			$menuset	=	$this->getHelper('Settings\Model')->getMenu();
		
		$new		=	[];
		foreach($menuset as $menu) {
			$this->recurseMenu(array_filter(explode('/', $menu['full_path'])), $new);	
		}

		ksort($new);

		return $this->recurseMenuBuild($new);
	}
}