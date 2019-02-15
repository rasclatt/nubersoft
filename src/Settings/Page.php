<?php
namespace Nubersoft\Settings;
/**
 *	@description	
 */
class Page extends \Nubersoft\Settings\Admin
{
	use \Nubersoft\nQuery\enMasse;
	/**
	 *	@description	
	 */
	public	function createPage($data)
	{
		if(empty($data['unique_id']))
			$data['unique_id']	=	$this->fetchUniqueId();
		
		$data['full_path']	=	'/'.trim($data['full_path'],'/').'/';
		
		if(empty($data['link']))
			$data['link']	=	pathinfo($data['full_path'], PATHINFO_BASENAME);
			
		$columns	=	array_keys($data);
		$values		=	array_values($data);
		
		$this->insert("main_menus")
			->columns($columns)
			->values([$values])
			->write();
	}
	/**
	 *	@description	
	 */
	public	function recurseLayout($array)
	{
		$stored	=
		$new	=	[];
		foreach($array as $key => $value) {
			if(empty($value['parent_id'])) {
				$stored[]	=	$key;
				$new[$key]	=	[];
			}
			else {
				$parented	=	$this->recurseToParent($new, $stored, $value['parent_id'], $key);
		
				if(!$parented) {
					if(!in_array($key, $stored)) {
						$new[$key]	=	[];
					}
				}
				else {
					$new[$key]	=	$parented;
				}
			}
		}
		
		return $new;
	}
	
	public	function recurseToParent(&$new, &$stored, $parent, $child)
	{
		foreach($new as $id => $arr) {
			if($parent == $id) {
				$new[$id][$child]	=	[];
				$stored[]	=	$child;
				return [];
			}
			else {
				$this->recurseToParent($new[$id], $stored, $parent, $child);
			}
		}
	}
}