<?php
namespace Nubersoft;

class Settings extends \Nubersoft\nQuery
{
	use nRouter\enMasse;
	
	protected	$def_component	=	'components';
	protected	$def_system		=	'system_settings';
	
	/**
	 *	@description	
	 */
	public	function setDefaultTable($table, $replace = 'components')
	{
		if($replace == 'components')
			$this->def_component	=	$table;
		else
			$this->def_system	=	$table;
		
		return $this;
	}
	
	public	function getSettings($name = false, $option_group_name = 'client')
	{
		if(empty($option_group_name))
			$option_group_name	=	'client';
		
		$sql	=	'SELECT * FROM `'.$this->def_system.'`';
		
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
		
		return (count($query) > 1)? $query : $this->getHelper('ArrayWorks')->organizeByKey($query, 'category_id');
	}
	
	public	function getSettingsByAction($action)
	{
		$query	=	$this->query("SELECT * FROM `{$this->def_system}` WHERE `action_slug` = ? AND `page_live` = 'on'",[$action])->getResults();
		
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
		if(!is_string($name) && is_array($name)) {
			foreach($name as $i => $key) {
				$this->setOption($key, $value[$i], $option_group_name);
			}
			
			return $this;
		}
		
		if(!is_string($name) (is_array($value) || is_object($value)))
			$value	=	json_encode($value);
		
		$this->insert($this->def_system)
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
			$this->deleteOption($name, $option_group_name)->setOption($name, $value, $option_group_name);
		}
		
		return $this;
	}
	
	public	function deleteOption($name, $option_group_name = 'client')
	{
		$this->query("DELETE FROM `{$this->def_system}` WHERE `category_id` = ? AND `option_group_name` = ?", [$name, $option_group_name]);

		return $this;
	}
	
	public	function optionExists($name, $option_group_name = 'client')
	{
		$count	=	$this->query("SELECT COUNT(*) as count FROM `{$this->def_system}` WHERE `category_id` = ? AND `option_group_name` = ?",[$name, $option_group_name])->getResults(1);
		
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

	public	function addComponent()
	{
		$params	=	func_get_args();
		
		if(is_array($params[0])) {
			$args	=	$params[0];
		}
		else {
			$args	=	[
				'category_id'=> ((!empty($params[1]))? $params[1] : 'client'),
				'content' => (is_object($params[0]) || is_array($params[0]))? json_encode($params[0]) : $params[0],
			];
		}
		
		if(!isset($args['unique_id']))
			$args['unique_id']	=	$this->fetchUniqueId();
		
		if(!isset($args['page_live']))
			$args['page_live']	=	'on';
		
		$this->insert("components")
			->columns(array_keys($args))
			->values([array_values($args)])
			->write();
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
	public	function getComponentBy($args, $op = "=", $glue = "AND", $select = '*', $orderby = false)
	{
		$sql	=	"SELECT ".((is_array($select))? implode(', ', $select) : $select)." FROM components WHERE ";
		
		foreach($args as $key => $value) {
			$where[]	=	"{$key} {$op} ?";
		}
		
		$sql	.=	implode(' '.$glue.PHP_EOL, $where);
		
		if(!empty($orderby))
			$sql	.=	' ORDER BY '.$orderby;
		
		return $this->query($sql, array_values($args))->getResults();
	}
	/**
	 *	@description	Retrieves a component
	 */
	public	function deleteComponent($ID, $where = 'ID')
	{
		return $this->query("DELETE FROM components WHERE {$where} = ?",[$ID])->getResults(1);
	}
	/**
	 *	@description	Retrieves a component by multiple arguments
	 */
	public	function deleteComponentBy($args, $op = "=", $glue = "AND")
	{
		$sql	=	"DELETE FROM components WHERE ";
		
		foreach($args as $key => $value) {
			$where[]	=	"{$key} {$op} ?";
		}
		
		return $this->query($sql.implode(' '.$glue.PHP_EOL, $where), array_values($args));
	}
	/**
	 *	@description	
	 */
	public	function componentExists()
	{
		$params	=	func_get_args();
		$args	=	(!empty($params[0]))? $params[0] : [];
		$op		=	(!empty($params[1]))? $params[1] : "=";
		$glue	=	(!empty($params[2]))? $params[2] : "AND";
		$select =	'COUNT(*) as count';
		
		($this->getComponentBy($args, $op, $glue, $select)[0]['count'] == 1);
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