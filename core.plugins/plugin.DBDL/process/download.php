<?php
if(!function_exists('is_admin'))
	return;

if(!is_admin())
	return;

$action		=	(!empty(nApp::getPost('action')))? nApp::getPost()->action : false;
$nQuery	=	nQuery();

if($action == 'nbr_db_dl') {
	if(!isset(nApp::getPost()->table)) {
		header("Location: ".$_SERVER['HTTP_REFERER']);
		exit;
	}
	if(!empty(nApp::getPost()->table)) {
		$nEngine	=	(!empty(nApp::getDataNode('engine')->temp_folder))? nApp::getDataNode('engine')->temp_folder : NBR_ROOT_DIR.TEMP_DIR;
		if(!$nEngine)
			nApp::saveSetting('engine',array('temp_folder'=>NBR_ROOT_DIR.'/../temp/'));
			
		$csv	=	new ZipEngine($nEngine);
		
		foreach(nApp::getPost('table') as $name) {
			$query	=	$nQuery->describe($name)->fetch();
			if($query != 0) {
				foreach($query as $row) {
					$search[]	=	$row['Field'];
				}
			}
			
			$csv->FetchTable($name,$search,$name);
			unset($search);
		}
		
		if(!isset(nApp::getPost()->zip_name))
			$zipname	=	date("YmdHis").preg_replace('/[^0-9a-zA-Z]/',"",$_SESSION['username']);
		else
			$zipname	=	date("YmdHis")."-".preg_replace('/[^0-9a-zA-Z]/',"",nApp::getPost('zip_name'));

		$csv->Zipit($zipname.'.zip');
	}
	
	nApp::saveIncidental('plugin',array('error'=>'Table invalid'));
}