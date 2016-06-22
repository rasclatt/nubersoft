<?php
namespace Nubersoft;

class nUploader extends nFileHandler
	{
		private	$queryData;
		
		public	function recordTransaction($table = 'file_activity')
			{
				$this->queryData	=	array();
				// If the data array is empty, skip
				if(empty($this->data))
					return false;
						
				$istable		=	\nApp::nFunc()->tableExists($table);
				
				try {
					if(!$istable) 
						throw new \Exception('No table to save transactions to.',1001);
					$nFunc		=	\nApp::nFunc();
					$qEngine	=	nQuery();
					$nFunc->autoload('FetchUniqueId',NBR_FUNCTIONS);
					foreach($this->data as $files) {
						$files['unique_id']		=	FetchUniqueId();
						$files['username']		=	(!empty($_SESSION['username']))? $_SESSION['username'] : $_SERVER['REMOTE_ADDR'];
						$files['ip_address']	=	$_SERVER['REMOTE_ADDR'];
						$files['timestamp']		=	date('Y-m-d H:i:s');
						$files['full_path']		=	$files['file_path'].$files['file_name'];
						$files['action']		=	'upload';
						
						$this->queryData[]		=	$files;
						
						$data		=	$nFunc->filterArrayByTable($table,$files);
						$aCols		=	(!isset($aCols))? array_keys($data) : $aCols;
						$record[]	=	$data;
					}
					
					$qEngine	->insert($table)
								->setColumns($aCols)
								->setValues($record)
								->write();
				}
				catch (\Exception $e) {
					$msg	=	$e->getMessage().' CODE:'.$e->getCode();
					if(is_admin()) {
						$msg	.=	" Table {$table} added!";
						$this->createTable($table);
						die($msg);
					}
					\nApp::saveToLogFile('error_uploads.txt',$msg);
				}
			}
		
		public	function createTable($tablename)
			{
				try {
					require(__DIR__._DS_.'nUploader'._DS_.__FUNCTION__._DS_.'table.php');
					$qEngine	=	nQuery();
					$qEngine->query($sql);
				}
				catch (\Exception $e) {
					if(is_admin()) {
						die($e->getMessage().': '.__FILE__);
					}
					else {
						\nApp::saveToLogFile('error_uploads.txt',$e->getMessage());
					}
				}
			}
		
		public	function getData()
			{
				return (!empty($this->queryData))? $this->queryData : false;
			}
	}