<?php
namespace Nubersoft;

class Settings extends \Nubersoft\nQuery
{
	use nRouter\enMasse;
	
	public	function getSettings($name = false, $option_group_name = 'client')
	{
		if(empty($option_group_name))
			$option_group_name	=	'client';
		
		$sql	=	'SELECT * FROM `system_settings`';
		
		if($name)
			$bind[]	=	$name;
		
		if(!empty($option_group_name)) {
			$bind[]			=	$option_group_name;
			$option_group_name	=	" AND `option_group_name` = ?";
		}
		
		if($name) {
			$query	=	$this->query($sql." WHERE `category_id` = ? AND `page_live` = 'on'{$option_group_name}", $bind)->getResults();
		}
		else {
			$option_group_name	=	ltrim($option_group_name, ' AND');
			$bind			=	(empty($option_group_name))? false : $bind;
			$query			=	$this->query($sql." WHERE {$option_group_name}", $bind)->getResults();
		}
		
		if(empty($query))
			return $query;
		
		foreach($query as $key => $value) {
			if(!empty($value['option_attribute'])) {
				$json	=	$this->decode($query[$key]['option_attribute']);
				$query[$key]['option_attribute']	=	(empty($json))? $query[$key]['option_attribute'] : $json;
			}
		}
		
		$final	=	$this->getHelper('ArrayWorks')->organizeByKey($query, 'category_id');
		
		return $final;
	}
	
	public	function getSettingsByAction($action)
	{
		$query	=	$this->query("SELECT * FROM `system_settings` WHERE `action` = ? AND `page_live` = 'on'",[$action])->getResults();
		
		if(empty($query))
			return $query;
		
		foreach($query as $key => $value) {
			if(!empty($value['content'])) {
				$query[$key]['content']	=	$this->decode($query[$key]['content']);
			}
		}
		
		return $this->getHelper('ArrayWorks')->organizeByKey($query, 'page_element');
	}
	
	public	function setOption($name, $value, $option_group_name = 'client')
	{
		if(is_array($name)) {
			foreach($name as $i => $key) {
				$this->setOption($key, $value[$i], $option_group_name);
			}
			
			return $this;
		}
		
		if(is_array($value) || is_object($value))
			$value	=	json_encode($value);
		
		$this->insert('system_settings')
			->columns([
				'category_id',
				'option_attribute',
				'option_group_name',
				'page_live'
			])
			->values([
				[$name, $value, $option_group_name, 'on']
			])
			->write();
		
		return $this;
	}
	
	public	function updateOption($name, $value, $option_group_name = 'client')
	{
		if(!$this->optionExists($name, $option_group_name)) {
			$this->setOption($name, $value, $option_group_name);
		}
		else {
			$this->deleteOption($name, $option_group_name)->setOption($name, $value);
		}
		
		return $this;
	}
	
	public	function deleteOption($name, $option_group_name = 'client')
	{
		$this->query("DELETE FROM `system_settings` WHERE `category_id` = ? AND `option_group_name` = ?", [$name, $option_group_name]);

		return $this;
	}
	
	public	function optionExists($name, $option_group_name = 'client')
	{
		$count	=	$this->query("SELECT COUNT(*) as count FROM `system_settings` WHERE `category_id` = ? AND `option_group_name` = ?",[$name, $option_group_name])->getResults(1);
		
		return	($count['count'] > 0)? $count['count'] : false;
	}
	
	public	function getOption($name, $option_group_name = 'client')
	{
		return $this->getSettings($name, $option_group_name);
	}
	
	public	function getSystemOption($name, $substitute = false)
	{
		if(!empty($this->getDataNode('settings')['system'][$name]['option_attribute']))
			return $this->getDataNode('settings')['system'][$name]['option_attribute'];
		
		$option	=	$this->getOption($name, 'system');
		if(empty($option))
			return $substitute;
		
		return $option[$name]['option_attribute'];
	}
	/**
	 *	@description	Retrieves a component
	 */
	public	function getComponent($ID, $where = 'ID', $live = true)
	{
		$sql	=	($live)? " and `page_live` = 'on'" : "";
		return $this->query("SELECT * FROM components WHERE {$where} = ?{$sql}",[$ID])->getResults(1);
	}
	/**
	 *	@description	Retrieves a component by multiple arguments
	 */
	public	function getComponentBy($args, $op = "=", $glue = "AND")
	{
		$sql	=	"SELECT * FROM components WHERE ";
		
		foreach($args as $key => $value) {
			$where[]	=	"{$key} {$op} ?";
		}
		
		return $this->query($sql.implode(' '.$glue.PHP_EOL, $where), array_values($args))->getResults();
	}
	/**
	 *	@description	Clears all the ref_page fields to remove all components from a page.
	 *					Doesn't delete them, however
	 *	@param $page_ID [int]	The column ID
	 *	@returns	[boolean(false)|array]
	 */
	public	function clearPageComponents($page_ID, $val = false)
	{
		$page	=	$this->getPage($page_ID, 'ID');
		if(empty($page['unique_id'])) {
			return false;
		}
		
		$this->query("UPDATE components SET ref_page = ?, page_live = 'off' WHERE ref_page = ?", [$val, $page['unique_id']]);
		
		return $this->pageHasComponents($page_ID);
	}
	
	public	function deletePageComponents($page_ID)
	{
		$this->query("DELETE FROM components WHERE ref_page = ?", [$page_ID]);
		
		return ($this->pageHasComponents($page_ID) > 0);
	}
	
	public	function pageHasComponents($page_ID)
	{
		$query	=	$this->query("SELECT COUNT(*) as count FROM components WHERE ref_page = ?", [$page_ID])->getResults(1)['count'];
		
		return ($query['count'] > 0)? $query['count'] : false; 
	}
}