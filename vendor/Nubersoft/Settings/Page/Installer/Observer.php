<?php
namespace Nubersoft\Settings\Page\Installer;
/**
 *	@description	
 */
class Observer extends \Nubersoft\nApp implements \Nubersoft\nObserver
{
	/**
	 *	@description	
	 */
	public	function listen()
	{
		# Check if database and/or registry file is created
		$dbcreds	=	NBR_CLIENT_SETTINGS.DS.'dbcreds.php';
		$registry	=	NBR_CLIENT_SETTINGS.DS.'registry.xml';
		
		if(!is_file($file = NBR_CLIENT_CACHE.DS.'defines.php')) 
			$this->getHelper("DataNode")->addNode('update_error', 'You need to reset your cache to get get client defines.');
		else
			include_once($file);
		
		
		if(!$this->isAdmin()) {
			if(is_file($dbcreds) && is_file($registry)) {
				if(filesize($dbcreds) > 0) {
					if(is_file($flag = NBR_CORE.DS.'installer'.DS.'firstrun.flag'))
						unlink($flag);

					$this->redirect('/');
				}
			}
		}
		
		if(!is_file($registry))
			$default	=	$this->toArray(simplexml_load_file(NBR_SETTINGS.DS.'registry.xml'));
		
		switch($this->getPost('action')){
			case('save_registry_doc'):
				$def	=	$this->getPost();
				unset($def['action']);
				$def	=	array_combine(array_keys($def), array_map(function($v){ return \Nubersoft\nApp::call()->dec($v); },$def));
				$default['ondefine']	=	$def;
				file_put_contents($registry, \Nubersoft\ArrayWorks::toXml($default, 'register'));
				break;
			case('save_dbcreds'):
				$POST	=	$this->getPost();
				unset($POST['action']);
				$dbdef	=	['<?php'];
				foreach($POST as $const => $value) {
					$value	=	trim($value);
					
					if(empty($value))
						continue;
					
					if($const != 'DB_CHARSET')
						$value	=	base64_encode($value);
						
					$dbdef[]	=	'define("'.$const.'", "'.$this->dec($value).'");';
					
					define($const, $value);
				}
				
				if(count($dbdef) == 6) {
					try {
						$testConn	=	new \PDO("mysql:host=".base64_decode(DB_HOST).";dbname=".base64_decode(DB_NAME), base64_decode(DB_USER), base64_decode(DB_PASS));
						file_put_contents($dbcreds, implode(PHP_EOL, $dbdef));
						$this->getHelper('nRouter')->redirect('/');
					}
					catch (\PDOException $e) {
						$this->getHelper('DataNode')->addNode('installer_error', "Database credentials were not created: ".$e->getMessage());
					}
				}
		}
		
		if(!is_file($registry)) {
			if(!is_file($registry)) {
				$defines	=	array_change_key_case($default['ondefine'], CASE_UPPER);
				$this->setLayout('do_defines', $defines);
			}
			else
				$this->getHelper('nRouter')->redirect('/');
		}
		
		if(!is_file($dbcreds)) 
			$this->setLayout('create_database', false);
		
		$this->setLayout('update_software', false);
	}
	
	protected	function setLayout($action, $data)
	{
		$this->getHelper('DataNode')->addNode('data', ['action' => $action, 'data' => $data]);
		$this->render(NBR_CORE.DS.'installer'.DS.'html'.DS.'index.php');
		exit;
	}
}