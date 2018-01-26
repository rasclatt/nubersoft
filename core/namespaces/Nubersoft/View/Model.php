<?php
namespace Nubersoft\View;

class Model extends \Nubersoft\nRender
{	
	public	function getParentMenu($id = false,$toggled=true)
	{
		$live	=	($toggled)? " and `page_live` = 'on'" : "";
		if(!empty($id))
			$type	=	(is_numeric($id))? "ID = :0 AND " : "full_path = :0 AND ";
		else
			$type	=	'';
		
		return $this->doPageOrder($this->nQuery()->query("SELECT * FROM main_menus WHERE {$type}`parent_id` = ''{$live}",(!empty($type))? [$id] : false)->getResults());
	}
	/**
	**	@description	Get a child menu
	*/
	public	function getChildMenu($parent_id,$toggled=true)
	{
		$type	=	(is_numeric($parent_id))? "parent_id = :0" : "full_path = :0";
		$live	=	($toggled)? " and `page_live` = 'on'" : "";
		return $this->doPageOrder($this->nQuery()->query("SELECT * FROM main_menus WHERE {$type}{$live}",[$parent_id])->getResults());
	}
	/**
	**	@description	Count how many children a menu has
	*/
	public	function hasChildMenu($parent_id,$toggled=true)
	{
		$data	=	[];
		if(is_numeric($parent_id))
			$data['parent_id']	=	$parent_id;
		else
			$data['full_path']	=	$parent_id;
		
		if($toggled)
			$data['page_live']	=	'on';
		
		$count	=	$this->fetchMenu($data,"COUNT(*) as count",true)['count'];
		
		return ($count == 0)? false : $count;
	}
	/**
	**	@description	Fetch a menu by parameters
	*/
	public	function fetchMenu(array $search,$return = '*',$oneReturn=false)
	{
		if(is_array($return))
			$return	=	implode(', ',$return);
		
		$sql	=	[];
		$i = 0;
		foreach($search as $key => $value) {
			$sql[]	=	$key.' = :'.$i;
			$i++;
		}
		
		$query	=	$this->nQuery()
						->query("SELECT {$return} FROM main_menus WHERE ".implode(' AND ',$sql),array_values($search))
						->getResults($oneReturn);
		
		return $this->doPageOrder($query);
	}
	/**
	**	@description	Sort by page_order
	*/
	protected	function sortByPageOrder(&$array)
	{
		$this->sortBy('page_order',$array);
	}
	/**
	**	@description	Sorts an array by a key inside the array
	*/
	protected	function sortBy($key,&$array)
	{
		usort($array,function($a,$b) use ($key){
			if($a[$key] == $b[$key])
				return 0;
			else
				return ($a[$key] > $b[$key])? 1 : -1;
		});
	}
}