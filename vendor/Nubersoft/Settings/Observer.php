<?php
namespace Nubersoft\Settings;

class Observer extends Controller implements \Nubersoft\nObserver
{
	public	function listen()
	{
		$Router		=	$this->getHelper('nRouter\Controller');
		$DataNode	=	$this->getHelper('DataNode');
		$Token		=	$this->getHelper('nToken');
		$SERVER		=	$this->getServer();
		$path		=	(strpos(strtolower($SERVER['SCRIPT_URL']), '.') !== false)? str_replace('//', '/', '/'.implode('/',array_filter(array_map(function($v){
			return (strpos(strtolower($v), '.') !== false)? false : $v;
		},explode('/',$SERVER['SCRIPT_URL'])))).'/') : $SERVER['SCRIPT_URL'];
		$query		=	(empty($path) || $path == '/')? $Router->getPage('2', 'is_admin') : $Router->getPage($path);
		$settings	=	$this->getSettings(false, 'system');
		$DataNode->setNode('cache_folder', (!defined('CLIENT_CACHE'))? NBR_CLIENT_CACHE : CLIENT_CACHE);
		$DataNode->setNode('routing', $query);
		$DataNode->setNode('settings', ['system' => $settings]);
		# Fetch any preferences that have the action
		if(!empty($this->getRequest('action'))) {
			$actions	=	$this->getSettingsByAction($this->getRequest('action'));
			if(!empty($actions)) {
				$DataNode->addNode('settings', $actions, 'actions');
			}
		}
		# Check for a rewrite file
		$this->setReWrite($this->getReWrite());
		# Save default paths for templating
		$DataNode->setNode('templates', ['paths' => $this->getTemplatePaths()]);
		# Save default paths for plugins
		$DataNode->setNode('plugins', ['paths' => $this->getPluginPaths()]);
		# Create a 404 if routing is missing
		$DataNode->setNode('header', [
			'header_response_code' => (empty($this->getDataNode('routing')))? 404 : 200
		]);
		# Checks if request is likely ajax in nature
		$DataNode->setNode('request', (($Router->isAjaxRequest())? 'ajax' : 'http'), 'type');
		# Creates a page token
		if(!$Token->tokenExists('page'))
			$Token->setToken('page');
		# Set the status of the site (on or off)
		$DataNode->setNode('site_status', $this->getSiteStatus());
		return $this;
	}
	
	protected	function getClientDefines()
	{
		return NBR_CLIENT_CACHE.DS.'defines.php';
	}
	
	public	function checkUserSettings()
	{
		$registry	=	NBR_CLIENT_SETTINGS.DS.'registry.xml';
		
		if(is_file($definc = $this->getClientDefines())) {
			@include_once($definc);
			return $this;
		}
		
		if(!is_file($this->getClientDefines())) {
			if(!is_file($registry))
				throw new \Nubersoft\HttpException('Registry file to create important settings is missing. Reinstall required.', 100);
			
			$registry	=	$this->toArray(simplexml_load_file($registry));
			
			if(!empty($registry['ondefine'])) {
				$nMarkup	=	$this->getHelper('nMarkUp');
				$def[]		=	'<?php'.PHP_EOL;
				foreach($registry['ondefine'] as $key => $value) {
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
				
				file_put_contents($this->getClientDefines(), implode(PHP_EOL, $def));
				$msg	=	($this->isAdmin())? "?msg=".urlencode('Cache has been removed.') : '';
				$this->getHelper('nRouter')->redirect($this->getPage('full_path'));
			}
		}
		
		return $this;
	}
	
	public	function setReWrite($content = false)
	{
		$system	=	(defined('NBR_SERVER_TYPE'))? NBR_SERVER_TYPE : 'linux';
		$file	=	NBR_ROOT_DIR.DS.(($system == 'linux')? '.htaccess' : 'web.config');
		
		if(is_file($file))
			return false;
		
		if(empty($content)) {
			$content	=	file_get_contents(NBR_SETTINGS.DS.'rewrite'.DS.$system.DS.'root.txt');
		}
		
		file_put_contents($file, $content);
		
		return is_file($file);
	}
	
	public	function setTimezone()
	{ 
		date_default_timezone_set($this->getTimezone());
		return $this;
	}
	/**
	 *	@description	
	 */
	public	function formatFileUploads()
	{
		$FILES	=	$this->getDataNode('_FILES');
		
		if(empty($FILES))
			return $this;
		
		$this->isDir(NBR_CLIENT_DIR.DS.'media'.DS.'images', true);
		
		$files	=	[];
		$Conversion	=	$this->getHelper('Conversion\Data');
		foreach($FILES as $keyname => $rows) {
			foreach($rows as $key => $value) {

				foreach($value as $i => $val) {
					# Stop if there is nothing uploaded
					if($key == 'name' && empty($val)) {
						return false;
					}
					$files[$i][$key]	=	$val;
					if($key == 'size') {
						$files[$i]['size_attr'] = [
							'kb' => round($Conversion->getByteSize($val,['from' => 'b', 'to'=>'kb']), 2),
							'mb' => round($Conversion->getByteSize($val,['from' => 'b', 'to'=>'mb']), 2),
							'gb' => round($Conversion->getByteSize($val,['from' => 'b', 'to'=>'gb']), 2)
						];
					}
					elseif($key == 'name') {
						$files[$i]['file_key_name']	=	$keyname;
						$files[$i]['name_date'] 	=	date('YmdHis').'.'.strtolower(pathinfo($val, PATHINFO_EXTENSION));
						$files[$i]['path_default']	=	DS.'client'.DS.'media'.DS.'images'.DS.$val;
						$files[$i]['path_alt']		=	DS.'client'.DS.'media'.DS.'images'.DS.$files[$i]['name_date'];
					}
				}
			}
		}
		
		$this->setNode('_FILES', $files);
		
		return $this;
	}
}