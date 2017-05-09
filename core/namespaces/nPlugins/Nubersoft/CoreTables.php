<?php
namespace nPlugins\Nubersoft;

class CoreTables extends \nPlugins\Nubersoft\CoreDatabase
	{
		public	function saveComponent($POST = false, $skip = false)
			{
				# Get post values
				$POST	=	(empty($POST))? $this->toArray($this->getPost()) : $POST;
				# Sets the update table
				$this->setTable($POST['requestTable']);
				# See if there is an option to remove empty
				$filter	=	(!empty($POST['action_options']['filter']));
				# See if a thumbnail is required to be created
				$thumb	=	(!empty($POST['action_options']['thumb']));
				# See if there is a token
				$match	=	$this->getHelper('nToken')->getSetToken();
				# If not admin stop
				if(!$this->isAdmin()) {
					$this->saveError($POST['action'],'You must be an admin user.',true);
					return;
				}
				# Allows for skipping form authentication
				if(!$skip) {
					# If token doesn't match, stop
					if($match != $POST['token']['nProcessor']) {
						$this->saveError($POST['action'],'Invalid token',true);
						return;
					}
				}
				# Filter columns/values
				$filtered	=	$this->filterAvailableColumns($POST);
				# If empty set, remove empty fields
				if($filter) {
					$filtered	=	array_filter($filtered);
				}
				
				$files	=	$this->uploadFile();
				$count	=	(is_array($files))? count($files) : 1;
				
				for($i = 0; $i < $count; $i++) {
					$base	=	array();
					$base	=	(is_array($files))? array_merge($filtered, $files[$i]) : $filtered;
					# If there is an id, the update
					if(!empty($POST['ID']))
						$this->updateComponent($base,$POST['ID']);
					else {
						$this->addNewRow($base);
					}
				}
			}
		
		public	function addNewRow($POST)
			{
				# add a timestamp
				if(isset($POST['date_created'])) {
					# By default set timestamp on system save
					if(empty($POST['date_created'])) {
						# Make sure the timezone is already set so it doesn't do a false timezone
						$this->getHelper('GetSitePrefs')->setAppTimeZone();
						# Populate the field
						$POST['date_created']	=	date('Y-m-d H:i:s');
					}
				}
				
				$table				=	$this->getTable();
				$POST				=	array_filter($POST);
				$POST['unique_id']	=	$this->fetchUniqueId();
				$columns			=	array_keys($POST);
				$cols				=	'`'.implode('`, `',$columns).'`';
				$vals				=	':'.implode(', :',$columns);
				try {
					$query	=	$this->getConnection()->prepare("INSERT INTO `{$table}` ({$cols}) VALUES({$vals})");
					$query->execute($this->standardBind($POST));
				}
				catch(\PDOException $e) {
					die($e->getMessage());
				}
			}
		
		public	function saveComponentById($id = false,$content = false)
			{
				$content	=	($content !== false)? $content : $this->getPost('data')->html;
				$id			=	(!empty($id) && is_numeric($id))? $id : $this->getPost('data')->deliver->ID;
				
				if(!is_numeric($id))
					return false;
				
				$this->nQuery()->query("UPDATE `components` SET `content` = :0 WHERE `ID` = :1",array($content,$id));
				
				if(!$this->isAjaxRequest())
					die(json_encode(array("alert"=>"compnent")));
				else
					return true;
			}
	}