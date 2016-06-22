<?php
function ajax_edit_favicon()
	{
		AutoloadFunction("is_admin");
		if(!is_admin())
			return;
		
		$data					=	array();
		$fInfo					=	false;
		$data['nbr_dropspot']	=	(!empty(nApp::getPost('nbr_dropspot')))? nApp::getPost('nbr_dropspot') : 'errors';
		
		if(isset($_FILES['upload'])) {
			$data['name']		=	$_FILES['upload']['name'];
			$data['name_tmp']	=	$_FILES['upload']['tmp_name'];
			$data['type']		=	$_FILES['upload']['type'];
			$data['size']		=	$_FILES['upload']['size'];
			AutoloadFunction("get_file_extension");
			$fPath				=	ROOT_DIR.'/_favicon.'.get_file_extension($data['name']);
			$valid				=	false;	
			if(move_uploaded_file($data['name_tmp'],$fPath)) {		
				$fEngine		=	new FileMaster();
				$fInfo			=	$fEngine	->Initialize()
												->GetInfo($fPath);
													
				$data['fInfo']	=	$fEngine->fileinfo;
				
				if(!empty($data['fInfo']) && !empty($data['fInfo']->type)) {
					$valid	=	($data['fInfo']->type == 'image' && $data['fInfo']->id == 'png');
				}
			}
			
			if(!$valid && is_file($fPath)) {
				unlink($fPath);
			}
			else {
				if(is_file($fPath)) {
					$rLPath	=	str_replace("/_favicon","/favicon",$fPath);
					if(rename($fPath,$rLPath)) {
						AutoloadFunction("get_site_prefs");
						$prefs	=	nApp::getSitePrefs();
						
						$cont		=	Safe::to_array($prefs->header);
						AutoloadFunction("version_from_file");
						$deflogo	=	$cont['content']['favicons']	=	'<link rel="icon" type="image/png" href="'.str_replace(ROOT_DIR,"",$rLPath).version_from_file($rLPath).'">';
						nQuery()	->update("system_settings")
									->set(array("content"=>json_encode($cont['content'],JSON_FORCE_OBJECT)))
									->where(array("ID"=>$cont['ID']))
									->write();
									
						return json_encode(array("valid"=>true,"error"=>"Successful upload","nbr_dropspot"=>$data['nbr_dropspot'],"path"=>$deflogo));
					}
				}
			}
			
			return json_encode(array("valid"=>false,"error"=>"Invalid file type","nbr_dropspot"=>$data['nbr_dropspot'],"path"=>false));
		}
	}

echo ajax_edit_favicon();