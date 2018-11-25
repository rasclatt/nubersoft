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
		return (!empty($option[$name]['option_attribute']))? $option[$name]['option_attribute'] : $substitute;
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
}