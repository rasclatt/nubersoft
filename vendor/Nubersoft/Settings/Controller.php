<?php
namespace Nubersoft\Settings;

class Controller extends \Nubersoft\Settings\Model
{
	use \Nubersoft\nMarkUp\enMasse;
	
	public	function getReWrite()
	{
		return $this->getSettingContent('system', 'htaccess');
	}
	
	public	function getTimezone()
	{
		$timezone	=	(defined('SYSTEM_TIMEZONE'))? SYSTEM_TIMEZONE : 'America/Los_Angeles';
		return $this->getSettingContent('system', 'timezone', $timezone);
	}
	
	public	function getSiteStatus()
	{
		return $this->getSettingContent('system', 'site_live');
	}
	
	public	function getDefaultTemplate()
	{
		# See if it's already stored
		if(!empty($this->getDataNode('settings')['system']['template']['option_attribute']))
			return $this->toSingleDs(NBR_ROOT_DIR.DS.$this->getDataNode('settings')['system']['template']['option_attribute'].DS);
		# Get the saved template option if there is one
		$template	=	$this->getOption('template', 'system');
		# Get the template value
		$template	=	(!empty($template['template']['option_attribute']))? $template['template']['option_attribute'] : false;
		# Stop if no template
		if(!$template)
			return NBR_ROOT_DIR.DS;
		# Return the template 
		return $this->toSingleDs(NBR_ROOT_DIR.DS.$template.DS);
	}
	
	public	function getTemplatePaths()
	{
		$page	=	$this->getPage('template');
		$paths	=	[
			'page' => (!empty($page))? rtrim($this->toSingleDs(NBR_ROOT_DIR.DS.$page), DS) : false,
			'site' => rtrim($this->getDefaultTemplate(), DS),
			'default' => NBR_DEFAULT_TEMPLATE
		];
		
		return array_unique($paths);
	}
	
	public	function getPluginPaths()
	{
		$page	=	$this->getPage('template');
		return array_unique([
			'root' => (!empty($page))? $this->toSingleDs(NBR_CLIENT_TEMPLATES.DS.$page.DS.'plugins') : false,
			'page' => (!empty($page))? $this->toSingleDs(NBR_ROOT_DIR.DS.$page.DS.'plugins') : false,
			'site' =>  $this->toSingleDs(NBR_CLIENT_TEMPLATES.DS.'plugins'),
			'default' => NBR_TEMPLATE_DIR.DS.'plugins'
		]);
	}
	
	public	function getFooterPrefs()
	{
		if(empty($this->getDataNode('settings')['system']['footer_html_toggle']))
			return false;
		
		if($this->getDataNode('settings')['system']['footer_html_toggle']['option_attribute'] == 'on')
			return $this->getDataNode('settings')['system']['footer_html']['option_attribute'];
	}
	
	public	function getHeaderPrefs($key = false)
	{
		return $this->getSettingContent('head', $key);
	}
	
	public	function setTemplateLayout()
	{
		$DataNode	=	$this->getHelper('DataNode');
		$frontend	=	
		$backend	=	false;
		$thisObj	=	$this;
		
		if(!empty($DataNode->addNode('templates')))
			return false;
		
		foreach($this->getDataNode('templates')['paths'] as $dir) {
			if(empty($dir))
				continue;
			
			if(empty($frontend) && is_file($finc = $dir.DS.'frontend'.DS.'index.php')) {
				$frontend	=	true;
				$DataNode->addNode('templates', $finc, 'frontend');
			}
			
			if(empty($backend) && is_file($binc = $dir.DS.'backend'.DS.'index.php')) {
				$backend	=	true;
				$DataNode->addNode('templates', $binc, 'backend');
			}
			
			if(empty($error) && is_file($einc = $dir.DS.'errors'.DS.'index.php')) {
				$error	=	true;
				$DataNode->addNode('templates', $einc, 'errors');
			}
			
			if(!isset($config) && is_file($conf = $dir.DS.'settings'.DS.'config.xml')) {
				$configs	=	$this->setTemplateConfig($conf, $DataNode);
			}
			
			if(!empty($configs))
				break;
		}
		
		if(empty($frontend))
			$DataNode->addNode('templates', '', 'frontend');
		
		if(empty($backend))
			$DataNode->addNode('templates', '', 'backend');
		
		if(empty($error))
			$DataNode->addNode('templates', '', 'errors');
	}
	
	public	function setTemplateConfig($template, \Nubersoft\DataNode $DataNode)
	{
		$thisObj	=	$this;
		$configs	=	$this->getHelper('ArrayWorks')
			->arrayWalkRecursive($this->getHelper('Conversion\Data')
				->xmlToArray($template), function($v) use ($thisObj) {
					return $thisObj->useMarkUp($v);
			});

		if(isset($configs['stylesheet'])) {
			if(!isset($configs['stylesheet']['include'][0]))
				$configs['stylesheet']['include']	=	[$configs['stylesheet']['include']];
		}
		if(isset($configs['javascript']['include'])) {
			if(!isset($configs['javascript']['include'][0]))
				$configs['javascript']['include']	=	[$configs['javascript']['include']];
		}

		$DataNode->addNode('templates', $configs, 'config');
		
		return $configs;
	}
	
	public	function getFormAttr($table)
	{
		
		$cols	=	$this->getColumnsInTable($table);

		$inputs	=	$this->query("SELECT `column_type`, `column_name` FROM form_builder WHERE `column_name` IN ('".implode("', '", $cols)."') AND page_live = 'on'")->getResults();
		
		if(empty($inputs))
			return [];
		
		foreach($inputs as $key => $input) {
			if(in_array($input['column_type'], ['select','radio','checkbox'])) {
				$inputs[$key]['options']	=	$this->query("SELECT `menuName` as `name`, `menuVal` as `value` FROM dropdown_menus WHERE `assoc_column` = ? order by page_order ASC", [$input['column_name']])->getResults();
			}
		}
		
		return $this->getHelper('ArrayWorks')->organizeByKey($inputs, 'column_name');
	}
	
	public	function createDefines($registry)
	{
		$registry	=	$this->toArray(@simplexml_load_file($registry));

		if(!empty($registry['ondefine'])) {
			$nMarkup	=	$this->getHelper('nMarkUp');
			$def[]		=	'<?php'.PHP_EOL;
			foreach($registry['ondefine'] as $key => $value) {
				if(is_array($value) || is_object($value))
					continue;
				$arg	=	$nMarkup->useMarkUp($value);
				if(is_string($arg)) {
					switch(true){
						case($arg == 'true'):
							$arg	=	'true';
							break;
						case($arg == 'false'):
							$arg	=	'false';
							break;
						case(is_numeric($arg)):
							$arg	=	$arg;
							break;
						default:
							$arg	=	"'{$arg}'";
					}
				}
				$def[]	=	'if(!defined(\''.strtoupper($key).'\'))'.PHP_EOL."\t".'define(\''.strtoupper($key).'\', '.$arg.');';
			}
				
			if($this->isDir(pathinfo($this->getClientDefines(), PATHINFO_DIRNAME), true))
				file_put_contents($this->getClientDefines(), implode(PHP_EOL, $def));
			
			return is_file($this->getClientDefines());
		}
	}
	
	protected	function getClientDefines()
	{
		return NBR_CLIENT_CACHE.DS.'defines.php';
	}
}