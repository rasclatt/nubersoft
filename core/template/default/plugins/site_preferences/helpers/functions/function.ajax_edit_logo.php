<?php
use \Nubersoft\nApp as nApp;
use \Nubersoft\nApp as Safe;

function ajax_edit_logo()
	{
		autoload_function("is_admin");
		if(!is_admin())
			return;
		
		$data					=	array();
		$fInfo					=	false;
		$data['nbr_dropspot']	=	(!empty(nApp::getPost('nbr_dropspot')))? nApp::getPost('nbr_dropspot') : 'errors';
		$dir					=	NBR_CLIENT_DIR.'/images/logo';
		$filter					=	array("jpg","jpeg","png","gif");
		
		if(!is_dir($dir))
			mkdir($dir,0755,true);
		
		if(isset($_FILES['upload'])) {
			$data['name']		=	$_FILES['upload']['name'];
			$data['name_tmp']	=	$_FILES['upload']['tmp_name'];
			$data['type']		=	$_FILES['upload']['type'];
			$data['size']		=	$_FILES['upload']['size'];
			autoload_function("get_file_extension");
			$fPath				=	$dir.'/_default.'.get_file_extension($data['name']);
			$valid				=	false;	
			if(move_uploaded_file($data['name_tmp'],$fPath)) {		
				$fEngine		=	new FileMaster();
				$fInfo			=	$fEngine	->Initialize()
												->GetInfo($fPath);
													
				$data['fInfo']	=	$fEngine->fileinfo;
				
				if(!empty($data['fInfo']) && !empty($data['fInfo']->type)) {
					$valid	=	($data['fInfo']->type == 'image' && in_array($data['fInfo']->id,$filter));
				}
			}
			
			if(!$valid && is_file($fPath)) {
				unlink($fPath);
			}
			else {
				if(is_file($fPath)) {
					$rLPath	=	str_replace("/_default","/default",$fPath);
					rename($fPath,$rLPath);
					
					$sitePrefs	=	nApp::getHead();
					if(!empty($sitePrefs->site)) {
						$cont		=	Safe::to_array($sitePrefs->site);
						$deflogo	=	$cont['content']['companylogo']	=	str_replace(NBR_ROOT_DIR,"",$rLPath);
						nquery()	->update("system_settings")
									->set(array("content"=>json_encode($cont['content'],JSON_FORCE_OBJECT)))
									->where(array("ID"=>$cont['ID']))
									->write();
					}
					
					return json_encode(array("valid"=>true,"error"=>"Successful upload","nbr_dropspot"=>$data['nbr_dropspot'],"path"=>$deflogo));
				}
			}
			
			return json_encode(array("valid"=>false,"error"=>"Invalid file type","nbr_dropspot"=>$data['nbr_dropspot'],"path"=>false));
		}
	}

echo ajax_edit_logo();