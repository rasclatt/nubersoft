<?php
namespace nPlugins\Nubersoft;

class	MenuEngine extends \Nubersoft\nApp
{
	public		$data,
				$current,
				$current_menu,
				$menu_dir;

	public	function fetchMenuData($menu = false)
	{
		$this->data		=	$this->getAllMenus($menu);
		$this->saveSetting('menu_data',$this->organizeByKey($this->data,'unique_id'));
		$this->organizeById();

		return $this;
	}

	public	function useLayout($layout = false)
	{
		if(is_file($layout)) {
			include($layout);
		}

		return $this;
	}

	protected	function organizeById()
	{
		if(!empty($this->data)) {
			$this->autoload(array('menu_organize_id'));
			$menu_layout	=	$this->organizeId();
			$this->saveSetting('menu_struc',$menu_layout);
			$this->getBaseMenu();
			return $this;
		}
		else
			$this->saveSetting('menu_struc',array());
	}

	protected	function organizeId()
	{
		if(is_array($this->data) && !empty($this->data)) {
			return $this->getTreeStructure($this->data);
		}
	}

	protected	function getBaseMenu()
	{	
		$id	=	$this->getPage('unique_id');

		if(empty($this->getData()->getMenuStruc()) || !$id)
			return $this;

		$iterated	=	new \ArrayObject($this->toArray($this->getData()->getMenuStruc()));

		foreach($iterated as $keys => $values) {
			$struct[$keys]	=	$values;
		}

		$new	=	new \RecursiveIteratorIterator(
						new \RecursiveArrayIterator($struct),
						\RecursiveIteratorIterator::SELF_FIRST
					);

		$this->current_menu	=	false;
		$this->menu_dir		=	array();

		// Cycle through saved objects and assign temp for processing
		$menu_struc	=	$this->toArray($this->getData()->getMenuStruc());
		$menu_data	=	$this->toArray($this->getData()->getMenuData());

		foreach($new as $key => $value) {
			if(isset($menu_struc[$key])) {
				$current			=	$key;
				$set[$current][]	=	$key;
				$dirs[$current][]	=	(isset($menu_data[$key]['link']))? $menu_data[$key]['link'] : false;
			}
			else {
				$set[$current][]	=	$key;
				$dirs[$current][]	=	(isset($menu_data[$key]['link']))? $menu_data[$key]['link'] : false;
			}

			if(!$this->current_menu) {
				if($key == $id) {
					$currentMenu	=	(isset($menu_data[$current]))? $menu_data[$current] : false;
					if(!empty($currentMenu['page_options']))
						$currentMenu['page_options']	=	json_decode(self::call('Safe')->decode($currentMenu['page_options']));

					$this->current_menu	=	$currentMenu;
				}
			}
			# This is not accurate, so just hide it
			// $this->menu_dir[$current]	=	$dirs[$current];
		}

		$this->buildDirectoryStructure();

		if(isset($set))
			$this->saveSetting('menu_hiearchy',$set);

		$this->saveSetting('menu_current',$this->current_menu);

		return $this;
	}

	protected	function buildDirectoryStructure()
	{
		$alt	=	array();
		foreach($this->menu_dir as $key => $array) {
			$str	=	"/";
			$aCount	=	count($array);
			for($i = 0; $i < $aCount; $i++) {
				$str			.=	$array[$i]."/";
				$new[$key][]	=	str_replace(DS.DS,DS,$str);
			}

			$alt	=	array_merge($alt,$new[$key]);
		}

		$this->saveSetting('menu_dir',$alt);
	}

	public	function getPageOptions($key = false,$page = false)
	{
		if(empty($page))
			$page	=	$this->toArray($this->getPageURI());
		else {
			$page	=	$this->nQuery()->query("SELECT * FROM `main_menus` WHERE `ID` = :0",array($page))->getResults(true);
			if(!empty($page['page_options']))
				$page['page_options']	=	json_decode($this->safe()->decode($page['page_options']),true);
		}

		if(!empty($key)) {
			if(isset($page['page_options'][$key]))
				return $page['page_options'][$key];
			else
				return false;
		}

		return (isset($page['page_options']))? $page['page_options'] : false;
	}
}