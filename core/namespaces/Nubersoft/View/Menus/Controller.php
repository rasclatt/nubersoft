<?php
namespace Nubersoft\View\Menus;

class Controller extends \Nubersoft\View\Model
{
	/**
	**	@description	Orders the page my page_order
	*/
	protected	function doPageOrder($menus)
	{
		if(is_array($menus) && count($menus) > 1)
			$this->sortByPageOrder($menus);
		
		return $menus;
	}
	/**
	**	@description	Turns the page_option into an array from json
	*/
	public	function parsePageOptions(&$menu)
	{
		if(!empty($menu['page_options']))
			$menu['page_options']	=	json_decode($this->safe()->decode($menu['page_options']),true);
	}
	/**
	**	@description	Fetches the menu and it's recursive children
	*/
	public	function getMenuMatrix($array)
	{
		$arr	=	[];
		foreach($array as $key => $menu) {
			$children			=	$this->getChildMenu($menu['unique_id']);
			$menu['usergroup']	=	trim($menu['usergroup']);
			if(!empty($menu['usergroup']) && is_string($menu['usergroup'])) {
				if(defined($menu['usergroup'])) {
					$numeric	=	$this->getUsergroup($menu['usergroup']);
				}
			}
			elseif(is_numeric($menu['usergroup']))
				$numeric	=	$menu['usergroup'];
			else
				$numeric	=	$menu['usergroup'];
			
			$menu['permissions']['usergroup']	=	$numeric;
			$menu['permissions']['constant']	=	(is_string($menu['usergroup']))? $menu['usergroup'] : false;
			$menu['permissions']['allowed']		=	(!empty($numeric))? $this->getUsergroup() <= $numeric : true;
			
			$this->parsePageOptions($menu);
			ksort($menu);
			if(!empty($children))
				$menu['children']	=	$this->getMenuMatrix($children);
			$arr[$key]	=	$menu;
		}
		return $arr;
	}
	/**
	**	@description	Fetches all menus in one dimensional array
	*/
	public	function fetchAllMenus($toggled=true)
	{
		return $this->fetchMenu(['page_live' => ((!empty($toggle))? "on" : "")]);
	}
	/**
	**	@description	Fetches all menus in one dimensional array
	*/
	public	function fetchMenuItems($menu=false,$reset=false)
	{
		if(!empty(self::$settings->menu_bar) && empty($reset))
			return $this->toArray(self::$settings->menu_bar);
			
		$def	=	[
			'in_menubar' => 'on',
			'page_live' => 'on',
			'parent_id' => ''
		];
		
		if(!empty($menu))
			$def	=	array_merge($def,$menu);
		
		$matrix	=	$this->getMenuMatrix($this->fetchMenu($def));
		
		self::$settings->menu_bar	=	$this->toObject($matrix);
		
		return $matrix;
	}
	/**
	**	@description	Fetches all menus in one dimensional array
	*/
	public	function fetchAllMatrix($menu=false,$reset=false)
	{
		if(!empty(self::$settings->menu_list) && empty($reset))
			return $this->toArray(self::$settings->menu_list);
			
		$def	=	[
			'page_live' => 'on',
			'parent_id' => ''
		];
		
		if(!empty($menu))
			$def	=	array_merge($def,$menu);
		
		$matrix	=	$this->getMenuMatrix($this->fetchMenu($def));
		
		self::$settings->menu_list	=	$this->toObject($matrix);
		
		return $matrix;
	}
}