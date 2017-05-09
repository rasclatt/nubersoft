<?php
namespace nPlugins\Nubersoft;

class CorePreferences extends \Nubersoft\GetSitePrefs
	{
		public	function getAdminUserPrefPath($action)
			{
				$sessPath	=	realpath($this->getCacheFolder().DS.'..');
				return $sessPath.DS.'session'.DS.'admin'.DS.$this->getSession('usergroup').DS.$this->getSession('ID').DS.$action.'.json';
			}
		
		public	function saveAdminUserPref($data=false)
			{
				if(!$this->isAdmin())
					return;
				
				$data	=	(is_array($data) && !empty($data))? $data : $this->toArray($this->getPost('data'));
				$user	=	$this->getSession('ID');
				$path	=	$this->getAdminUserPrefPath($data['name']);
				$action	=	$data['action'];
				$push	=	(isset($data['push']))? $data['push'] : false;
				$store	=	(isset($data['store']))? $data['store'] : false;
				
				if(is_file($path)) {
					if($action == 'remove') {
						$deleted	=	unlink($path);
						if($this->isAjaxRequest())
							die(json_encode(array($action=>$deleted)));
					
						$this->saveIncidental($action,array('msg'=>$deleted));	
						return;
					}
					
					$curr	=	json_decode(file_get_contents($path),true);
				}
				else {
					if(!$this->isDir(pathinfo($path,PATHINFO_DIRNAME)))
						return false;
				}
				
				if(is_array($store)) {
					foreach($store as $key => $value) {
						if($push == $key)
							$curr[$key][]	=	$value;
						else
							$curr[$key]	=	$value;
					}
				}
				
				if(is_file($path))
					unlink($path);
					
				$final	=	json_encode($curr);
				$this->saveFile($final,$path);
				if($this->isAjaxRequest())
					die($final);
				else
					return $final;
			}
		
		public	function getAdminUserPref($name = false,$action = false)
			{
				if(!$this->isAdmin())
					return;
				
				$name	=	(!empty($name))? $name : $this->toArray($this->getPost('data')->name);
				$action	=	(!empty($action))? $action : $this->getPost('action');
				$path	=	$this->getAdminUserPrefPath($name);
				if(!is_file($path)) {
					if($this->isAjaxRequest())
						die(json_encode(array($action,'msg'=>'No pref file found.')));
					else
						return false;
				}
				
				if($this->isAjaxRequest()) {
					$contents	=	trim(file_get_contents($path));
					if(!empty($contents) && $contents != 'null')
						die($contents);
					else {
						unlink($path);
						die(json_encode(array($action,'msg'=>'No pref file found.')));
					}
				}
				else
					return json_decode(file_get_contents($path),true);
			}
		
		public	function saveSitePrefs()
			{
				$POST		=	$this->toArray($this->getPost());
				$content	=	$POST['content'];
				$element	=	$POST['page_element'];
				$getId		=	$this->nQuery()->query("select `ID` from `system_settings` where `page_element` = :0",array($element))->getResults(true);
				$ID			=	($getId != 0)? $getId['ID'] : false;
				$POST['requestTable']	=	'system_settings';
				$POST['ID']	=	$ID;
				$dbEngine	=	$this->get3rdPartyHelper('\nPlugins\Nubersoft\CoreTables');
				$dbEngine->setTable('system_settings')->saveComponent($POST,true);
				$this->deletePrefFile('preferences');
				$this->set();
				if(!empty($content['htaccess'])) {
					$this->getHelper('nReWriter')->createHtaccess(array('content'=>$this->safe()->decode($content['htaccess']),'save_to'=>NBR_ROOT_DIR));
				}
			}
		
		public	function makeDirReadable()
			{
				if(!$this->isAdmin())
					return;
				elseif(!$this->getPost('action') == 'nbr_make_path_viewable')
					return;
				
				$table	=	$this->safe()->sanitize($this->getPost('table'));
				$ID		=	$this->getPost('ID');
				
				$file	=	$this->nQuery()
								->query("select `file_path` from `{$table}` where `ID` = :0",array($ID))
								->getResults(true);
				
				if(!is_dir(NBR_ROOT_DIR.$file['file_path']))
					return;
				
				\Nubersoft\nReWriter::browserRead(array(
					'dir'=>NBR_ROOT_DIR.$file['file_path'],
					'write'=>true)
				);
			}
	}