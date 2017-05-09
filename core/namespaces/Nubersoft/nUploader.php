<?php
namespace Nubersoft;

class nUploader extends \Nubersoft\nFileHandler
	{
		private	$queryData;
		
		public	function recordTransaction($table = 'file_activity')
			{
				$nApp				=	nApp::call();
				$this->queryData	=	array();
				// If the data array is empty, skip
				if(empty($this->data))
					return false;
						
				$istable		=	$this->tableExists($table);
				# Sets timezone
				$nApp->getHelper('Settings')->setTimeZone();
				
				try {
					if(!$istable) {
						if(!empty($table))
							$this->createTable($table);
						
						trigger_error('No table to save transactions to. Attempted to create table.',E_USER_NOTICE);
						
						if(!$this->tableExists($table)) {
							if(!$nApp->isAdmin())
								return;
							
							throw new \Exception('Could not create a file transaction table.');
						}
					}
						
					$qEngine	=	$nApp->nQuery();
					foreach($this->data as $files) {
						$files['unique_id']		=	$nApp->fetchUniqueId();
						$files['username']		=	(!empty($nApp->getSession('username')))? $nApp->getSession('username') : $nApp->getDataNode('_SERVER')->REMOTE_ADDR;
						$files['ip_address']	=	$nApp->getDataNode('_SERVER')->REMOTE_ADDR;
						$files['timestamp']		=	date('Y-m-d H:i:s');
						$files['full_path']		=	$files['file_path'].$files['file_name'];
						$files['action']		=	(!empty($nApp->getRequest('action')))? $nApp->getRequest('action') : 'upload';
						$files['file_mime']		=	(isset($files['file_type']))? $files['file_type'] : pathinfo($files['full_path'],PATHINFO_EXTENSION);
						
						$this->queryData[]		=	$files;
						
						$data		=	$this->filterArrayByTable($table,$files);
						$aCols		=	(!isset($aCols))? array_keys($data) : $aCols;
						$record[]	=	$data;
					}
					
					$qEngine	->insert($table)
								->columnsValues($record)
								->write();
				}
				catch (\Exception $e) {
					$msg	=	$e->getMessage().' CODE:'.$e->getCode();
					if($nApp->isAdmin()) {
						die($msg);
					}
					
					$nApp->saveToLogFile('error_uploads.txt',$msg,array('logging','exceptions'));
				}
			}
		
		public	function createTable($tablename)
			{
				try {
					nApp::call()->getHelper('CoreMySQL')->installTable($tablename);
				}
				catch (\Exception $e) {
					if(nApp::call()->isAdmin())
						die($e->getMessage().': '.__FILE__);
					
					nApp::call()->saveToLogFile('error_uploads.txt',$e->getMessage(),array('logging','exceptions'));
				}
			}
		
		public	function getData()
			{
				return (!empty($this->queryData))? $this->queryData : false;
			}
		
		public	function setData($data)
			{
				$this->data	=	$data;
				return $this;
			}
	}