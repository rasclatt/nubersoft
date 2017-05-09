<?php
	function TermsValidator($settings = false,$Obj = false)
		{
			$valid[] = $table_name	=	(!empty($settings['table_name']))? $settings['table_name']: false;
			$valid[] = $ID 			=	(!empty($settings['ID']))? $settings['ID']: false;
			$permission				=	true;
			
			if(in_array(false,$valid,true))
				return $Obj;
			
			$query	=	nQuery();
			
			// Get the directory info for this id
			$dir	=	$query	->select(array("file_path","terms_req","usergroup"))
								->from("upload_directory")
								->where(array("assoc_table"=>$table_name))
								->fetch();

			// Check if I am logged in
			$myUsergroup		=	(!empty($_SESSION['usergroup']));

			// If the directory exists in db and I am logged in
			if(($dir != 0) && $myUsergroup) {
				AutoloadFunction("allow_if");
				
				$fdata	=	$query	->select()
									->from($table_name)
									->where(array("ID"=>$ID))
									->fetch();
				
				if($fdata == 0)
					$validFile	=	false;

				if(!isset($validFile)) {
					$filename		=	$fdata[0]['file_name'];
					// Assign the required id
					$terms_id		=	$dir[0]['terms_req'];
					// Assign the usergroup (3 if none is provided)
					$usergroup		=	(empty($dir[0]['usergroup']))?  3 : $dir[0]['usergroup'];
					// Assign path for downloaded files
					$path			=	$dir[0]['file_path'];
					// Create persistant object array for this data
					$Obj->dlInfo	=	(object) array("terms_id"=>$terms_id,"usergroup"=>$usergroup,"file_path"=>$path);
					// If there is a terms id
					if(!empty($terms_id)) {
						// Turn on terms feature
						$Obj->terms	=	true;
						// Check if the usergroup assigned to the folder is as good or less than user
						$permission	=	allow_if($usergroup);
					}
					// Create file path
					$Obj->filename 	=	str_replace(DS.DS,DS,NBR_ROOT_DIR.DS.$path.DS.$filename);
					// Check that the file is valid
					$validFile		=	is_file($Obj->filename);
					// file is valid
					if($validFile) {
						// If there are terms
						if($Obj->terms) {
							// If permissions are good
							if($permission) {
								// Check if the user has accepted the terms
								$settings['accepted']	=	(!empty($_REQUEST['accepted']))? $_REQUEST['accepted']:false;
								// Persist the terms
								$settings['terms']		=	true;
								// Do another check on the username
								if(!empty($_SESSION['username']))
									$settings['username']	=	$_SESSION['username'];
								// Validate the terms/agreements
								$Obj->ValidateTerms($settings);
							}
						}
						// If permissions are good
						else {
							if($permission)
								$Obj->allow	=	true;
						}
					}
				}
					
				if(!$permission)
					$Obj->errors['usergroup']	=	true;
				
				if(!$validFile)
					$Obj->errors['invalid']	=	true;
			}
			
			return $Obj;
		}