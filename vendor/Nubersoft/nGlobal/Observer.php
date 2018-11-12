<?php
namespace Nubersoft\nGlobal;

class Observer extends \Nubersoft\nGlobal implements \Nubersoft\nObserver
{
	public	function listen()
	{
		$this->createContainer(function(\Nubersoft\DataNode $DataNode){
			
			foreach([
				'_ENV' => $_ENV,
				'_GET' => $_GET,
				'_POST' => $_POST,
				'_FILES' => $_FILES,
				'_REQUEST' => $_REQUEST,
				'_SERVER' => $_SERVER,
				'_SESSION' => $_SESSION,
				'_COOKIE' => $_COOKIE
			] as $key => $array) {
				$DataNode->setNode($key, $this->sanitize($array));
			}
			
		});
	}
}