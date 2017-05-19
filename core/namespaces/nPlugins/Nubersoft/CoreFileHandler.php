<?php
namespace nPlugins\Nubersoft;

class CoreFileHandler extends \Nubersoft\nFileHandler
	{	
		public	function getBaseHtaccess(\Nubersoft\nApp $nApp)
			{
				$filepath	=	NBR_ROOT_DIR.DS.'.htaccess';
				
				if(!is_file($filepath)) {
					if(!$nApp->isAjaxRequest())
						return false;
					
					die(json_encode(array('success'=>false,'msg'=>'No htaccess file found.')));
				}
				
				$contents	=	file_get_contents($filepath);
				
				if(!$nApp->isAjaxRequest())
					return $contents;
				
				$array	=	array(
					'sendto'=>array($nApp->getPost()->deliver->sendback),
					'input'=>array($contents)
				);
				
				die(json_encode($array));
			}
		
		public	function formDataManager(\Nubersoft\nApp $nApp,$FILES = false,$POST = false)
			{
				if(!$nApp->isAdmin())
					return;
				
				$FILES	=	(!empty($FILES))? $FILES : $nApp->getNode('_SERVER_REQUEST')->get_FILES();
				$instr	=	(!empty($POST))? $POST : json_decode($nApp->safe()->decode($nApp->getPost('data')));
				$FILES	=	(!empty($FILES['file'][0]['name']))? $FILES['file'][0] : false;
				# Run an empty check
				$valid	=	$nApp->checkEmptyResponse($FILES,'Files can not be empty');
				if(empty($valid))
					return false;
				
				$FILES['name']	=	'default.'.pathinfo($FILES['name'],PATHINFO_EXTENSION);
				$dir			=	NBR_CLIENT_DIR.DS.'client'.DS.'images'.DS.'logo';
				
				if(!$nApp->isDir($dir)) {
					return	$nApp->checkEmptyResponse(false,'Could not create directory');
				}
				
				if(!($is_up = is_uploaded_file($FILES['tmp_name'])) || !move_uploaded_file($FILES['tmp_name'],$dir.DS.$FILES['name'])) {
					return $nApp->checkEmptyResponse(false,'Could not move file: '.(!$is_up)? "Not an uploaded file.": "Failed to move to folder.");
				}
				else {
					$fileData	=	$this->updateSitePrefs($nApp->stripRoot($dir.DS.$FILES['name']),$nApp);
					if($nApp->isAjaxRequest()) {
						die(json_encode(array('path'=>$fileData)));
					}
					else
						return $fileData;
				}
			}
			
		protected function updateSitePrefs($file,\Nubersoft\nApp $nApp,$kind = 'site')
			{
				$prefs	=	$nApp->toArray($nApp->getPreferences('site'));
				$prefs['content']['companylogo']	=	$file;
				$prefs['content']['test']			=	true;
				$prefs['requestTable']				=	'system_settings';
				$nApp->removeDataNode('_FILES');
				$CoreDatabase	=	new CoreTables();
				$CoreDatabase->setTable('system_settings')->saveComponent($prefs,true);
				
				$update	=	$nApp->nQuery()->query("select * from `system_settings` where `ID` = :0",array($prefs['ID']))->getResults(true);
				
				if(isset($update['content']))
					$update['content']	=	$nApp->toArray(json_decode($nApp->safe()->decode($update['content'])));
				
				return (isset($update['content']['companylogo']))? $update['content']['companylogo'] : false;
			}
	}