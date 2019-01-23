<?php
namespace Nubersoft\Settings;
/**
 *	@description	
 */
trait enMasse
{
	public	function getSystemOption($name, $substitute = false)
	{
		return $this->getHelper('Settings')->{__FUNCTION__}($name, $substitute);
	}
	
	public	function setSystemOption($name, $value)
	{
		return $this->setOption($name, $value, 'system');
	}
	
	public	function updateSystemOption($name, $value)
	{
		return $this->updateOption($name, $value, 'system');
	}
	
	public	function deleteSystemOption($name)
	{
		return $this->deleteOption($name, 'system');
	}
	/**
	 *	@description	
	 */
	public	function getOption($name, $substitute = false, $option_group_name = 'client')
	{
		$option	=	$this->getHelper('Settings')->{__FUNCTION__}($name, $option_group_name);
		
		if(empty($option))
			return $substitute;
		
		return (isset($option[$name]))? $option[$name]['option_attribute'] : array_map(function($v){
			return (isset($v['option_attribute']))? $v['option_attribute'] : false;
		}, $option);
	}
	
	public	function setOption($name, $value, $option_group_name = 'client')
	{
		return $this->getHelper('Settings')->{__FUNCTION__}($name, $value, $option_group_name);
	}
	
	public	function updateOption($name, $value, $option_group_name = 'client')
	{
		return $this->getHelper('Settings')->{__FUNCTION__}($name, $value, $option_group_name);
	}
	
	public	function deleteOption($name, $option_group_name = 'client')
	{
		return $this->getHelper('Settings')->{__FUNCTION__}($name, $option_group_name);
	}
	
	public	function optionExists($name, $option_group_name = 'client')
	{
		return $this->getHelper('Settings')->{__FUNCTION__}($name, $option_group_name);
	}
	
	public	function addComponent()
	{
		return $this->getHelper('Settings')->{__FUNCTION__}(...func_get_args());
	}
	
	public	function getComponent()
	{
		return $this->getHelper('Settings')->{__FUNCTION__}(...func_get_args());
	}
	
	public	function getComponentBy()
	{
		return $this->getHelper('Settings')->{__FUNCTION__}(...func_get_args());
	}
	
	public	function deleteComponent()
	{
		return $this->getHelper('Settings')->{__FUNCTION__}(...func_get_args());
	}
	
	public	function deleteComponentBy()
	{
		return $this->getHelper('Settings')->{__FUNCTION__}(...func_get_args());
	}
	
	public	function componentExists()
	{
		return $this->getHelper('Settings')->{__FUNCTION__}(...func_get_args());
	}
}