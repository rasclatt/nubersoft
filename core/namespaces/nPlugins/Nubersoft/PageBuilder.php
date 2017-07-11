<?php
namespace nPlugins\Nubersoft;

class PageBuilder extends \Nubersoft\nApp
	{
		public		$_query;
		
		protected	$_results,
					$_unique_id,
					$_info,
					$_payload,
					$payload,
					$_key,
					$_value,
					$_sql,
					$_array_key,
					$recurseDir,
					$returnVal,
					$_loop_array,
					$retain_update;

		private		$sql,
					$outputVar,
					$_include,
					$doc_root,
					$error_handle,
					$parent_by;
		
		public	function __construct()
			{
				$this->nubquery	=	$this->nQuery();
				
				return parent::__construct();
			}

		public	function execute($payload = false)
			{
				if(!is_admin())
					return;
				
				$this->payload		=	(is_array($payload))? $payload:$_POST;
				$this->parent_by	=	(!empty($this->payload['parent_by']))? $this->payload['parent_by'] : "UID";
				
				$_filterNames[]		=	'admintools';
				$_filterNames[]		=	'';
				$important_links	=	(!in_array(strtolower($this->payload['link']),$_filterNames))? true:false;

				if(isset($this->payload['link'])) {
					if((check_empty($this->payload,'command','page_builder')) && is_admin()) {
						if(isset($this->payload['update']) || isset($this->payload['delete'])) {
							// If meant to delete, do so
							if(check_empty($this->payload,'delete','on')) {
								//$allMenus->returnVar
								if(check_empty($this->payload,'rebuild','on')) {
									$menus			=	nquery()	->select()
																	->from("main_menus")
																	->getResults();

									$this->_results	=	($menus != 0)? $menus[0]:array();
									
									foreach($this->_results as $keys => $values) {
										$this->Rebuild($values);
									}
								}
								else
									$this->Update();	
							}
							else
								$this->Update();
						}
						elseif(isset($this->payload['add']))
							$this->Add();

						$_isPage	=	(!empty($_SERVER['HTTP_REFERER']))? $_SERVER['HTTP_REFERER'] : site_url();
						# Set  a redirect
						$this->getHelper('nRouter')->addRedirect($_isPage);
					}
				}
				else {
					global $_error;
					$_error['createpage'][]	=	'Link name forbidden ('.$this->payload['link'].')';
				}
			}
			
		public	function Update($payload = false)
			{
				register_use(__METHOD__);
				
				$this->payload	=	(!isset($this->payload) && is_array($payload))? $this->payload : $_POST;
				$query			=	nquery();
				// Get the menu on file
				$results		=	$query	->select()
											->from('main_menus')
											->where(array('unique_id'=>$this->payload['unique_id']))
											->getResults();

				// Use the straight POST if the insert is new
				// Combine the old with the POST to get an updated POST
				$updated		=	($results != 0)? array_merge($results[0],$_POST):$_POST;
				// If the names don't match, rename the folder to the new folder name and
				$delete			=	(check_empty($this->payload,'delete','on'));
				// Delete the page out
				if($delete)
					$this->Delete($updated['unique_id']);
				// Update the page
				else {
						$this->payload	=	$updated;
						$this->CleanupMenu();
					}
				
				return $this;
			}

		public	function Delete($_unique_id)
			{
				register_use(__METHOD__);
				
				$this->_unique_id		=	$_unique_id;
				$this->retain_update	=	(isset($this->retain_update) && $this->retain_update);
				
				// Delete the instance from the database
				$this->nubquery	->delete()
								->from("main_menus")
								->where(array("unique_id"=>$this->_unique_id))
								->write();
				
				$this->nubquery	->addCustom("update `components` set `ref_page` = 'del', `parent_id` = 'del' where ref_page = '".Safe::encode($this->_unique_id)."'")
								->write();
				
				if($this->retain_update != true) {
						//$parent	=	($this->parent_by == 'UID')? $this->_unique_id : $this->payload['ID'];
						// Remove references to parent/child relationship
						$this->nubquery	->update("main_menus")
										->set(array("parent_id"=>''))
										->where(array("parent_id"=> $this->_unique_id))
										->write();
						
						// Remove references to parent/child relationship
						$this->nubquery	->update("main_menus")
										->set(array("parent_id"=>''))
										->where(array("parent_id"=> $this->_unique_id))
										->write();
					}
				
				$this->retain_update	=	NULL;
				
				return $this;
			}
		
		
		public	function CleanupMenu()
			{
				register_use(__METHOD__);
				
				$this->payload	=	(isset($this->payload) && is_array($this->payload))? $this->payload:$_POST;
				
				$query			=	nquery();
				
				/*
				**	INSTRUCTIONS:
				**	The sql must take place before the folder generation process.
				**	It must first retreive the info from the database in order to check what is current.
				**	All actions must happen to clear out any changing folder data before the final
				**	generation of the root/recursive directories
				*/
				
				$is_home	=	false;
				if(!empty($this->payload['link']))
					$this->payload['link']	=	Safe::encode(Safe::PrettyURL($this->payload['link']));
				else {
					$home		=	nquery()	->select("COUNT(*) as count")
												->from("main_menus")
												->where(array("full_path"=>"/"))
												->getResults();
					
					// Set up default folder name
					if($home[0]['count'] == 0) {
						$this->payload['link']	=	"";
						$is_home				=	true;
					}
					else {
						if(!empty($this->payload['menu_name']))
							$this->payload['menu_name']	=	trim(preg_replace("/[^0-0a-zA-Z\_\-]/","",$this->payload['menu_name']));
					}
				}
								
				if(!check_empty($this->payload,'menu_name')) {
					if(!empty($this->payload['link']))
						$this->payload['menu_name']	=	trim(preg_replace("/[^0-0a-zA-Z\_\-]/","",$this->payload['link']));
					else
						$this->payload['menu_name']	=	"name".$this->payload['unique_id'];
				}
				
				if(empty($this->payload['link']) && !$is_home)
					$this->payload['link']		=	Safe::PrettyURL($this->payload['menu_name']);
									
				// Set up default label 
				$this->payload['menu_name']	=	(!empty($this->payload['menu_name']))? $this->payload['menu_name']: ucwords(str_replace(array("_","-")," ",$this->payload['link']));
				// Set up default template
				$this->payload['template']	=	(!empty($this->payload['template']) && $this->payload['template'])? $this->payload['template']: \Nubersoft\NubeData::$settings->site->template;
				$run	=	$query	->update("main_menus")
									->setAlt($this->filter_array_to_json($this->payload))
									->where(array("ID"=>$this->payload['ID']))
									->write();
				
				if(empty($query->err['sql']) && is_admin()) {
					autoload_function('render_generic_page');
					render_generic_page(array("content"=>'<h2 class="nbshclass">SQL Error:</h2><div class="nbsgenlayer">'.printpre($query->err).PHP_EOL.'</div>',"exit"=>true,"back"=>$_SERVER['HTTP_REFERER']));
				}
											
				$ptrap	=	$query	->select(array("parent_id","unique_id"))
									->from("main_menus")
									->where(array("parent_id"=>$this->payload['unique_id'],"unique_id"=>$this->payload['unique_id']),false,false,"or")
									->getResults();

				autoload_function("menu_get_all,menu_organize_id,menu_create_dirlist");

				if($ptrap != 0) {
					foreach($ptrap as $id) {
						natsort($id);
						$id		=	array_values($id);
						
						$key	=	$id[0]."|".$id[1];
						if(!isset($check[$key]))
							$check[$key]	=	true;
						else {
							$query	->update("main_menus")
									->setAlt(array("parent_id"=>""))
									->where(array("unique_id"=>$this->payload['parent_id'],"parent_id"=>$this->payload['unique_id']))
									->write();
						}
					}
				}
				
				foreach(\Nubersoft\NubeData::$settings->menu_data as $menu_arr) {										
					$menus	=	menu_create_dirlist(menu_organize_id(Safe::to_array(menu_get_all())),$menu_arr->unique_id);
					$query	->update("main_menus")
							->set(array("full_path"=>$menus))
							->where(array("unique_id"=>$menu_arr->unique_id))
							->write();
				}
					
				return $this;
			}
		
		public	function Add($update = false)
			{
				register_use(__METHOD__);
				/*
				**	INSTRUCTIONS:
				**	The sql must take place before the folder generation process.
				**	It must first retreive the info from the database in order to check what is current.
				**	All actions must happen to clear out any changing folder data before the final
				**	generation of the root/recursive directories
				*/
				$this->payload	=	(is_array($update))? $update : array_filter($_POST);
				$query			=	nquery();
				// Columns from table
				$aKeys			=	array_keys(organize($query->describe("main_menus")->getResults(),"Field"));
				// Payload keys/values
				$bKeys			=	array_keys($this->payload);
				// Filtered to find valid
				$valid			=	array_map(
										function($v) use($aKeys)
											{
												if(in_array($v,$aKeys))
													return $v;
											},$bKeys);
				// Filter empties
				$valid			=	array_filter($valid);
				// Remove any unwanted values
				foreach($this->payload as $aKey => $aVal) {
					if(!in_array($aKey,$valid))
						unset($this->payload[$aKey]);
				}
				// Done with the valid table	
				unset($valid);
				
				$valid_ui		=	(isset($this->payload['unique_id']) && !empty($this->payload['unique_id']));
				// Assign unique_id
				if($valid_ui != true) {
					autoload_function("fetch_unique_id");
					$this->payload['unique_id']	=	fetch_unique_id();
				}
					
				if(!empty($this->payload['link']))
					$this->payload['link']	=	Safe::PrettyURL($this->payload['link']);
				else {
					$home	=	nquery()	->select("COUNT(*) as count")
											->from("main_menus")
											->where(array("full_path"=>"/"))
											->getResults();

					// Set up default folder name
					if($home[0]['count'] == 0) {
						$this->payload['link']	=	"";
						$is_home				=	true;
					}
					else {
						if(!empty($this->payload['menu_name']))
							$this->payload['menu_name']	=	trim(preg_replace("/[^0-0a-zA-Z\_\-]/","",$this->payload['menu_name']));
					}
				}
								
				if(empty($this->payload['menu_name'])) {
					if(!empty($this->payload['link']))
						$this->payload['menu_name']	=	trim(preg_replace("/[^0-0a-zA-Z\_\-\s]/","",$this->payload['link']));
					else
						$this->payload['menu_name']	=	"name".$this->payload['unique_id'];
				}
				
				if(empty($this->payload['link']) && !isset($is_home))
					$this->payload['link']	=	Safe::PrettyURL($this->payload['menu_name']);
									
				// Set up default label 
				$this->payload['menu_name']	=	(!empty($this->payload['menu_name']))? $this->payload['menu_name']: ucwords(str_replace(array("_","-")," ",$this->payload['link']));
				// Set up default template
				$this->payload['template']	=	(!empty($this->payload['template']) && $this->payload['template'])? $this->payload['template']: \Nubersoft\NubeData::$settings->site->template;

				autoload_function("menu_get_all,menu_organize_id,menu_create_dirlist");
				$run	=	$query	->insert("main_menus")
									->setColumns(array_keys($this->payload))
									->setValues(array($this->filter_array_to_json($this->payload)))
									->write();
				
				$menus	=	menu_create_dirlist(menu_organize_id(menu_get_all()),$this->payload['unique_id']);

				$query	->update("main_menus")
						->set(array("full_path"=>$menus))
						->where(array("unique_id"=>$this->payload['unique_id']))
						->write();
					
				return $this;
			}

		public	function RecurseInclude($sql, $outputVar, $doc_root, $error_handle)
			{
				autoload_function('organize');
				$this->sql			=	$sql;
				$this->outputVar	=	$outputVar;
				$this->error_handle	=	$error_handle;
				$this->doc_root		=	$doc_root;
				global $unique_id;
				// Find the root menu ID
				$this->_info	=	organize(nquery()->query($this->sql)->getResults(),'unique_id');
				// Save to object for output to normal array
				autoload_function('tree_structure');
				$struc			=	new ArrayObject(tree_structure($this->_info, $parent = 0));
				// Save to an easily recursable array
				foreach($struc as $keys => $values) {
					$struct[$keys]	=	$values;
				}
				// Build the recursive components unique_id tree
				$array = new RecursiveArrayIterator($struct);
				// Build the site
				foreach($array as $key => $value) {
					$this->BuildSiteFiles($this->_info,$key,$value);	
				}
			}

		public	function BuildSiteFiles($_payload,$_key,$_value)
			{
				$this->_payload	=	$_payload;
				$this->_key		=	$_key;
				$this->_value	=	$_value;
				
				// <-- Foreach Value in main tree stucture array -->
				// If the passed value is an array, process the children
				if(is_array($this->_value)) {
					// Get root IDs for arrays: Requires the data, the key type (like unique_id), and the array
					$recursePages	=	new siteBuild($this->_payload[$this->_key], $this->outputVar, $this->_value);
					$recurse		=	new recurseBuildDir;
					$recurse->generate($this->_value, $this->_payload, $recursePages->directory, $this->outputVar);
					if(isset($recurse->returnDir) && is_array($recurse->returnDir)) {
						// If there are directories for a particluar id, split them out. Currently they are in strings separated by "/"
						foreach($recurse->returnDir as $key => $value) {
							// Separate the sub arrays into their own arrays by exploding the "/" separator
							$recurseStr[]	=	explode("/", $value);
						}
					}
				}
				else {
					// Apply the single keys to the same array as the multiple arrays in order
					// to process all equally.
					$recurseStr[]	=	$this->_key;
				}
				
				// Run through the array to build strings
				// The default pass values are "$keys" and "$values" so those need to be passed to the include code
				foreach($recurseStr as $setKeys => $setValues) {
					if(is_array($setValues)) {
						// If the append directory is true, add the NBR_ROOT_DIR to the link
						$recurseDir		=	($this->doc_root == true)? NBR_ROOT_DIR."/": '/';
						
						// If there are submenus, run through the directory compiling
						foreach($setValues as $subKeys => $subValues) {	
							// For each unique_id, attach it to the last one to build the full directory
							$slash		=	(!empty($this->_payload[$subValues]['link']))? "/": "";
							$recurseDir	.=	(isset($this->_payload[$subValues]['link']))? $this->_payload[$subValues]['link']: '';
							$recurseDir	.=	$slash;
							
							// Build a temporary array to retreive the last value which will be the unique ID if the last folder
							if(!empty($subValues))
								$arrayVal[] = $subValues;
						}
						// If an array is present, pop off the last unique_id to present to the include code
						if(is_array($arrayVal))
							$returnVal	= array_pop($arrayVal);
						
						// Send the recursive string to the include code (remove double forward slashes)
						$recurseDir	=	str_replace("//", "/", $recurseDir);
					}
					else {
						// If all is straight forward, send recursive string to include code and remove double slashes, if they arise
						$doc_root	=	($this->doc_root == true)? NBR_ROOT_DIR."/": '/';
						$recurseDir	=	str_replace("//", "/", $doc_root . $this->_payload[$setValues]['link'] . "/");
						
						// Send the unique_id to the include code 
						$returnVal	=	$setValues;
					}
					
					// Include the final coding
					$this->PageBuilderCore($recurseDir,$returnVal,$this->sql,$setKeys);
				}
				
				unset($recurseStr);
			}

		public	function PageBuilderCore($recurseDir,$returnVal,$_sql = '',$_array_key = '')
			{
				$this->recurseDir	=	$recurseDir;
				$this->returnVal	=	$returnVal;
				$this->_sql			=	$_sql;
				$this->_array_key	=	$_array_key;
				
				// The foreach returns either value depending on sub levels
				$finValues			=	$this->returnVal;
				$_isRootDir			=	(str_replace(NBR_ROOT_DIR,"",$this->recurseDir) == '/')? true: false;
				$_build_dir			=	(isset($this->_payload[$this->returnVal]['create_folder']))? $this->_payload[$this->returnVal]['create_folder']:'';
				
				// Write the delete / create script (don't delete the root folder!!)
				if(is_dir($this->recurseDir) && $_isRootDir == false) {
					// If the key is the first one (root) recursive delete so the folder can rebuild itself
					if($this->_array_key == 0) {
						$deletePath	=	new recursiveDelete;
						$deletePath->delete($this->recurseDir);
					//	echo '<br />RECURSIVE DELETE DIRECTORY: ' . $this->recurseDir;
					}
					// Remake the folder
					if($_build_dir == 'on') {
						if(!$this->isDir($this->recurseDir))
							die('Folder Build Failed: Level 1.:' . $this->recurseDir);
					}
				//	echo '<br />Remake Folder' . $this->recurseDir;
				}
				else {
					// Run the make directory script unless the folder is the root folder
					if($_isRootDir !== true) {
						if($_build_dir == 'on') {
						//	echo '<br />MAKE DIRECTORY: ' . $this->recurseDir;
							if(!$this->isDir($this->recurseDir))
								die('Folder Build Failed: Level 2.:' . $this->recurseDir);
						}
					}
				}
				
				if(is_dir($this->recurseDir))
					// Now that the folder is made in each instance, drop the correct template into it (or set it to the default template)
					@copy(NBR_ROOT_DIR.'/core/template/index/index.php', $this->recurseDir . 'index.php');
				
				// Update the full_path
				$_full_path		=	str_replace(array(NBR_ROOT_DIR, "index.php"), "", $this->recurseDir);
				$string			=	"update main_menus set full_path = '".Safe::encode($_full_path)."' where unique_id = '" . $this->_payload[$finValues]['unique_id'] . "'";
				$this->_query->Write($string);
			}

		public	function Rebuild($_loop_array)
			{
				$this->_loop_array	=	$_loop_array;
				
				/*
					INSTRUCTIONS:
					The sql must take place before the folder generation process.
					It must first retreive the info from the database in order to check what is current.
					All actions must happen to clear out any changing folder data before the final generation of the root/recursive directories
				*/ 
				
				// Find the root document via loop in order to search out all the children
				$FindBase				=	new	findBase($this->nuber);
				// Adding a new directory requires that all parent and child unique_ids be<br>
				// check or else last level subs will be missed
				// Remove all the duplicates (it's a messy return)
				$getDirRoot				=	$FindBase->execute($this->_loop_array['link'], 'link')->returnVarArr;
				// Filter unique
				$getDirRoot	=	array_unique($getDirRoot);
				// Filter empties
				$getDirRoot	=	array_filter($getDirRoot);
				
				// Build where sql clauses from array
				foreach($getDirRoot->returnVarArr as $sqlKey => $sqlVal) {
					$buildSQL[]	=	" unique_id = '" . $sqlVal . "' or parent_id = '" . $sqlVal . "' ";
				}
				
				// Chain the sqls together to get an sql string to insert into the statment
				$checkAll		=	implode("or", $buildSQL);
				
				// Destroy array
				unset($buildSQL);
				
				// Retreive all the instances of the root page from DB
				$statement		=	"select * from main_menus where $checkAll";
				
				// Build recursive directory build
				$buildDirs		=	new recurseInclude($this->nuber);
				$response		=	$buildDirs->getResults($statement, 'unique_id', true, true)->response;
				
				foreach($response['info'] as $infoUnique_id => $infoObject) {
					$this->PageBuilderCore(NBR_ROOT_DIR.$infoObject['full_path'],$infoUnique_id);
				}
			}
		
		protected	function filter_array_to_json($array = false)
			{
				if(!is_array($array))
					return $array;
				// This will loop throught the array to htmlspecialchar recursively
				if(!function_exists("recurseit")) {
					function recurseit($array = false)
						{
							foreach($array as $sKey => $sVal) {
								if(is_array($sVal))
									$return[$sKey]	=	recurseit($sVal);
								else
									$return[$sKey]	=	Safe::encodeSingle($sVal);
							}
							return $return;
						}
				}
				
				foreach($array as $key => $value) {
					// Sanitize any values that are array so they don't mess up the json
					if(is_array($value))
						$value	=	recurseit($value);
					// Apply value as string
					$new[$key]	=	(is_array($value))? Safe::encodeSingle(json_encode($value)) : $value;
				}
				// Return the new array
				return (isset($new))? $new : $array;
			}
	}