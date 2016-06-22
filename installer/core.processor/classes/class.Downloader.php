<?php
	class Downloader
		{	
			public		$data;
			public		$file_info;
			public		$reply;
			public		$filename;
			public		$errors;
			public		$terms;
			public		$allow;
			public		$dlInfo;
			public		$fileData;

			protected	$nubquery;
			protected	$nuberdata;
			
			public	function __construct()
				{
				}
				
			public	function Initialize($usename = false)
				{
					$query					=	nQuery();
					$permission				=	true;
					$finfo					=	array();
					$this->dlInfo			=	array();
					$this->allow			=	false;
					$this->terms			=	false;
					$this->fileData['raw']	=	$usename;
					
					// If the db valid and filename is not empty
					if(empty($query) || empty($usename))
						return $this; //{
					AutoloadFunction("download_decode,TermsValidator");
					$finfo					=	explode("/", Safe::decOpenSSL(urlencode($usename)));
					$this->fileData['data']	=	$finfo;
					// Trim the value of empty spaces
					$finfo[1]				=	(!empty($finfo[1]))? trim($finfo[1]) : false;
					// If the string contains a table id and a path
					if(!empty($finfo[1]) && $finfo[1] != 'zip') { 
							// Get filename from database
							$settings["ID"]			=	trim($finfo[0]);
							// Assign table for validation lookup
							$settings["table_name"]	=	$finfo[1];
							// Tell validator to search the db for the table name
							// Get the results using the method(s) from this class
							$validate				=	TermsValidator($settings,$this);
							// Assign all the pertinant variables
							if(!$validate->errors) {
									$this->filename	=	$validate->filename;
									$this->terms	=	$validate->terms;
									$this->dlInfo	=	$validate->dlInfo;
									$this->allow	=	$validate->allow;
								}
							else
								$error['err']	=	true;
						}
					elseif($finfo[1] && $finfo[1] == 'zip') {
							if(is_admin()) {
									AutoloadFunction("zip_files,get_directory_list");
									$folder		=	urldecode($finfo[0]);
									$dir		=	get_directory_list(array("dir"=>$folder));
									$ZipEngine	=	zip_files(NubeData::$settings->site->temp_folder);
									
									foreach($dir['host'] as $docs) {
											if(is_file($docs))
												$ZipEngine->AddFiles($docs);
										}
									
									$ZipEngine->Zipit(date("YmdHis").basename($folder));
									exit;
								}
							else
								$error['err']	=	true;
						}
					else
						$error['err']	=	true;
					
					if(!$this->allow && !empty($error)) {
							$register	=	new RegisterSetting();
							$register	->UseData("download",$error)
										->SaveTo("errors");
						}

					return $this;
				}
			
			public	function ErrorPage($dlpage = false)
				{
					// Assign default downloader page
					$dlpage	=	($dlpage != false)? $dlpage : '/core.processor/template/default/site.download.php';
					// Assign error page layout
					$errpg	=	str_replace("//","/",NBR_ROOT_DIR."/{$dlpage}");
					// Assign default allow
					$this->allow	=	(!empty($this->allow))? $this->allow : false;
					// Assign default terms
					$this->terms	=	(!empty($this->terms))? $this->terms : false;
					// If not allow, then show error page
					if(!$this->allow) {
							// Inclu
							if(is_file($errpg)) {
									ob_start();
									include_once($errpg);
									$data	=	ob_get_contents();
									ob_end_clean();
									return $data;
								}
						}
				}
			
			public	function Download($usefile = false)
				{
					// If allow is already set, assign else false
					$this->allow	=	(!empty($this->allow))? $this->allow : false;
					// If the file is not empty, try to override the class-assigned value(s)
					if($usefile != false) {
							// If the file is a real file
							if(is_file($usefile)) {
									// Assign filename
									$this->filename	=	$usefile;
									// Allow to true
									$this->allow	=	true;
								}
						}
					// If allowed to download
					if($this->allow) {
							// Load the downloader header		
							AutoloadFunction("header_download");
							$settings["Content-type"]				=	"application/octet-stream";
							$settings["Cache-Control"]				=	array("must-revalidate", "post-check=0", "pre-check=0");
							$settings["Content-Transfer-Encoding"]	=	"binary";
							$settings["Connection"]					=	"Keep-Alive";
							$settings["Expires"]					=	'0';
							$settings["Pragma"]						=	"public";
							$settings["Content-length"]				=	filesize($this->filename);
							$settings["Content-disposition"]		=	'attachment; filename="'.basename($this->filename).'"';
							// Attempt to download the file
							header_download($this->filename,$settings);
							exit;
						}
				}
			
			public	function ValidateTerms($settings = false)
				{
					$this->terms	=	(!empty($settings['terms']))? $settings['terms']:false;
					$accepted		=	(!empty($settings['accepted']))? $settings['accepted']:false;
					$username		=	(!empty($settings['username']))? $settings['username']:false;
					// If there are no terms
					if(!$this->terms) {
							// Set to allow, download is unrestricted
							$this->allow	=	true;
							return $this;
						}
					// If terms are required
					if($accepted) {
							// Make the allow false because terms have not been accepted
							$this->allow		=	false;
							// If the username is not `true` or `false`
							if(!is_bool($username)) {
									AutoloadFunction("FetchUniqueId");
									// Get database query engine
									$nubquery	=	nQuery();
									// Settings for acceptance
									$saveInfo["unique_id"]				=	FetchUniqueId();
									$saveInfo["ref_anchor"]				=	$_SESSION['username'];
									$saveInfo["ref_spot"]				=	"terms_accept";
									$saveInfo["content"]["terms_id"]	=	$_POST['terms_id'];
									$saveInfo["content"]["timestamp"]	=	$_POST['timestamp'];
									$saveInfo["content"]["item"]		=	$_POST['item'];
									// Get the column names
									$set_cols	=	array_keys($saveInfo);
									// Insert into database the acceptance
									$nubquery	->insert("components")
												->setColumns($set_cols)
												->setValues(array($saveInfo))
												->write();
									// Download if valid file
									if(is_file($this->filename))
										$this->allow	=	true;
								}
							else {
									// Log failed
									$error['logged']	=	true;
									$register			=	new RegisterSetting();
									$register->UseData("download",$error)->SaveTo("errors");
								}
						}
					else {
							// Default to false
							$this->allow	=	false;
							// Assign terms toggle
							$this->terms	=	true;
						}
						
					return $this;
				}
				
			private	function FileById($id = false,$table = false)
				{
					return nQuery()	->select(array("file_path","file_name"))
										->from($table)
										->where(array("ID"=>$id))
										->fetch();
				}
		}