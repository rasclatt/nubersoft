<?php
/*
**	Copyright (c) 2017 Nubersoft.com
**	Permission is hereby granted, free of charge *(see acception below in reference to
**	base CMS software)*, to any person obtaining a copy of this software (nUberSoft Framework)
**	and associated documentation files (the "Software"), to deal in the Software without
**	restriction, including without limitation the rights to use, copy, modify, merge, publish,
**	or distribute copies of the Software, and to permit persons to whom the Software is
**	furnished to do so, subject to the following conditions:
**	
**	The base CMS software* is not used for commercial sales except with expressed permission.
**	A licensing fee or waiver is required to run software in a commercial setting using
**	the base CMS software.
**	
**	*Base CMS software is defined as running the default software package as found in this
**	repository in the index.php page. This includes use of any of the nAutomator with the
**	default/modified/exended xml versions workflow/blockflows/actions.
**	
**	The above copyright notice and this permission notice shall be included in all
**	copies or substantial portions of the Software.
**
**	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
**	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
**	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
**	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
**	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
**	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
**	SOFTWARE.

**SNIPPETS:**
**	ANY SNIPPETS BORROWED SHOULD BE SITED IN THE PAGE IT IS USED. THERE MAY BE SOME
**	THIRD-PARTY PHP OR JS STILL PRESENT, HOWEVER IT WILL NOT BE IN USE. IT JUST HAS
**	NOT BEEN LOCATED AND DELETED.
*/
namespace nPlugins\Nubersoft;

class CoreDatabase extends \Nubersoft\ConstructMySQL
	{
		protected	$ai;
		
		protected	$table;
		
		public	function setTable($table)
			{
				$this->table	=	$table;
				return $this;
			}
			
		public	function getTable()
			{
				return (!empty($this->table))? $this->table : 'components';
			}
		
		public	function saveComponent($POST = false, $skip = false)
			{
				# Get post values
				$POST	=	(empty($POST))? $this->toArray($this->getPost()) : $POST;
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
				/*
				# Allows for skipping form authentication
				if(!$skip) {
					# If token doesn't match, stop
					if($match != $POST['token']['nProcessor']) {
						$this->saveError($POST['action'],'Invalid token',true);
						return;
					}
				}
				*/
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
						/*
						$parent_id	=	(isset($POST['parent_id']))? $POST['parent_id'] : false;
						$ref_page	=	(isset($POST['ref_page']))? $POST['ref_page'] : false;
						$this->addComponent($ref_page,$parent_id);
						*/
						$this->addComponent(array_filter($base));
					}
				}
			}
		
		public	function duplicateComponent($POST=false)
			{
				if(!$this->isAdmin())
					return;
				
				$ID		=	$this->getPost('deliver')->ID;
				$comp	=	$this->nQuery()
								->query("select * from `components` where `ID` = :0",array($ID))
								->getResults(true);
				
				$comp['ref_anchor']	=	date('Y-m-d H:i:s').' '.ucwords($comp['component_type']).' (copy)';
				$comp['category_id']	=	'duplicate';
				$comp['ID']			=
				$comp['ref_page']	=
				$comp['parent_id']	=
				$comp['unique_id']	=	'';
				
				$this->saveComponent($comp);
	
				if($this->isAjaxRequest())
					$this->ajaxResponse(array('html'=>array('Component Duplicated. Reload component.'),'sendto'=>array('.duplicates')));
			}
		
		public	function saveDuplicateComponent($POST = false)
			{
				if(!$this->isAdmin())
					return;
			
				$POST	=	(empty($POST))? $this->toArray($this->getPost()) : $POST;
				$ID		=	$POST['ID'];
				$table	=	$POST['table'];
				$action	=	(isset($POST['delete']))? 'delete' : 'add';
				
				if($action == 'delete') {
					$this->deleteComponent($table,$ID);
					return;
				}
				
				$comp	=	$this->nQuery()
								->query("select * from `{$table	}` where `ID` = :0",array($ID))
								->getResults(true);
				
				$comp['page_live']	=	'off';
				$comp['category_id']	=	'nbr_layout';
				$comp['ref_page']	=	$POST['ref_page'];
				$comp['ref_anchor']	=	
				$comp['page_order']	=
				$comp['ID']			=	'';
				$comp['unique_id']	=	'';
				$comp				=	array_filter($comp);
				
				$this->saveComponent($comp);
			}
		
		public	function renameComponent()
			{
				if(!$this->isAdmin())
					return;
				
				$POST	=	array();
				$form	=	$this->safe()->decode(urldecode($this->getPost('deliver')->form));
				parse_str($form,$POST);
				
				if(empty($POST['dup_comp_name']) || empty($POST['ID']))
					return;
				
				$table	=	$POST['table'];
				
				$this->nQuery()->query("update `{$table}` set `ref_anchor` = :0 where `ID` = :1",array($POST['dup_comp_name'],$POST['ID']));
			}
		
		/*
		**	@description	Takes a filterd post and updates in the database
		*/
		public	function updateComponent($UPDATE,$ID)
			{
				$sql	=	$this->update($this->getTable())
								->createUpdate($UPDATE)
								->where(array('ID'=>$ID))
								->getStatement();
				
				try {
					$query	=	$this->getConnection()->prepare($sql);
					$query->execute($this->getBind());
				}
				catch(\PDOException $e) {
					die($e->getMessage());
				}
			}
		
		public	function deleteComponent($table = 'components',$ID = false)
			{
				if(!$this->isAdmin())
					return;
				
				$table		=	(!empty($table))? $table : $this->getTable();
				$ID			=	(!empty($ID))? $ID : $this->getPost()->ID;
				$fetch		=	$this->getCompById($ID);
				$orphans	=	(!empty($fetch['orphans']['children']))? $fetch['orphans']['children'] : false;
				
				
				if($orphans) {
					$sql	=	"UPDATE `{$table}`
									SET `parent_id` = ''
									WHERE `ID` = ".implode(' AND `ID` = ',$orphans);
					
					$this->query($sql);
				}
				# Delete File
				$this->deleteCompFile($this->getFilePath($ID,$table));
				# Delete component
				$this->query("delete from `{$table}` WHERE `ID` = :0",array($ID));
			}
		
		public	function getFilePath($ID,$table)
			{
				try {
					# Get the columns in the table
					$cols	=	$this->getColumns($table);
					# If file paths don't exist, abort
					if(is_array($cols)) {
						if(!in_array('file_path',$cols))
							return false;
					}
					
					$query	=	$this->query("select CONCAT(`file_path`,`file_name`) as filename from `{$table}` WHERE `ID` = :0",array($ID))->getResults(true);
				
					return $query['filename'];
				}
				catch (\PDOException $e) {
					return false;
				}
			}
		
		public	function deleteCompFile($filename)
			{
				$path 		=	$this->toSingleDs(NBR_ROOT_DIR.DS.$filename);
				$file_path	=	pathinfo($path,PATHINFO_DIRNAME);
				$file_name	=	pathinfo($path,PATHINFO_FILENAME);
				$file_ext	=	pathinfo($path,PATHINFO_EXTENSION);
				$thumb_path	=	$file_path.DS.'thumbs';
				
				if(is_file($path)) {
					if(is_file($thumb = $thumb_path.DS.$file_name.'.'.$file_ext))
						unlink($thumb);
					
					return	unlink($path);
				}
			}
		
		public	function addComponent($POST)
			{
				$POST['unique_id']	=	$this->fetchUniqueId();
				$POST				=	array_filter($POST);
				$ColsVals			=	$this->createInsertBind($POST);
				$columns			=	$ColsVals['cols'];
				$values				=	$ColsVals['vals'];
				$bind				=	$ColsVals['bind'];
				
				try {
					$query	=	$this->getConnection()
									->prepare("insert into `".$this->getTable()."` ({$columns}) VALUES ({$values})");
					$query->execute($bind);
				}
				catch(\PDOException $e) {
					echo printpre();
					die($e->getMessage());
				}
			}
			
		public	function createInsertBind($array)
			{
				$vals	=	
				$cols	=
				$bind	=	array();
				$i = 0;
				foreach($array as $key => $value) {
					$rKey			=	':'.$i;
					$cols[]			=	'`'.$key.'`';
					$vals[]			=	$rKey;
					$bind[$rKey]	=	$value;
					$i++;
				}
				
				return array(
					'cols' => implode(',',$cols),
					'vals' => implode(',',$vals),
					'bind' => $bind
				);
			}
		
		public	function getBind($type = false, $raw = false)
			{
				return $this->bind;
			}
		
		public	function where($array, $operand = '=',$comb = 'AND',$isolate = false)
			{
				$tick			=	$this->getTicks();
				$whereSql		=	array();
				$this->sql[]	=	'WHERE';
				$this->ai		=	(isset($this->ai))? $this->ai : 0;
				foreach($array as $where => $value) {
					$wKey				=	":".$this->ai;
					$this->bind[$wKey]	=	$value;
					$whereSql[]			=	"{$tick}{$where}{$tick} {$operand} {$wKey}";
					$this->ai++;
				}
				
				$brackets['f']	=	($isolate)? '(' : '';
				$brackets['e']	=	($isolate)? ')' : '';
				
				$this->sql[]	=	$brackets['f'].implode(" {$comb} ",$whereSql).$brackets['e'];
				
				return $this;
			}
		
		protected	function createUpdate($array)
			{
				$this->sql[]	=	'SET';
				$tick			=	$this->getTicks();
				$update			=	array();
				$this->ai		=	(isset($this->ai))? $this->ai : 0;
				foreach($array as $column => $value) {
					$sKey				=	":".$this->ai;
					$this->bind[$sKey]	=	(is_array($value))? $this->safe()->encodeSingle(json_encode($value)) : $value;
					$update[]			=	$tick.$column.$tick.' = '.$sKey;
					$this->ai++;
				}
				
				$this->sql[]	=	implode(', ',$update);
				return $this;
			}
		
		protected	function filterAvailableColumns($POST)
			{
				$table		=	$this->getTable();
				# Get all the keys from the post
				$current	=	array_keys($POST);
				# Geta all column layouts from the table
				$columns	=	$this->getAvailableColumns();
				# Get all column names
				$colKeys	=	array_keys($columns);
				# Extract the difference between available columns and post
				$diff		=	array_diff($current,$colKeys);
				# Get the final good column list
				$keys		=	array_diff($current,$diff);
				# Loop through the values
				foreach($keys as $key) {
					if(!isset($POST[$key]))
						continue;
					
					if(is_array($POST[$key])) {
						$POST[$key]	=	array_filter($POST[$key]);
						if(is_array($POST[$key]))
							$POST[$key]	=	(empty($POST[$key]))? '' : $this->safe()->encodeSingle(json_encode($POST[$key]));
					}
						
					$new[$key]	=	$POST[$key];
				}
				# Remove problematic values
				$this->removeErrorProne($columns,$new);
				return $new;
			}
		
		public	function removeErrorProne(&$array,&$POST)
			{
				foreach($array as $key => $attr) {
					if(!isset($POST[$key]))
						continue;
						
					if(!empty($attr['Extra'])) {
						if(strpos('auto_increment',$attr['Extra']) !== false) {
							unset($array[$key]);
							unset($POST[$key]);
	
							continue;
						}
					}
				
					if(empty($attr['Null'])) {
						if(strtolower($attr['Null']) == 'no') {
							if(empty($POST[$key]))
								unset($POST[$key]);
						}
					}
					
					if(strpos('int ',strtolower($attr['Type'])) !== false) {
						if(!is_numeric($POST[$key]) && !is_bool($POST[$key])) {
							unset($POST[$key]);
						}
						else {
							if(is_bool($POST[$key])) {
								$POST[$key]	=	($POST[$key] == true)? (int) 1 : (int) 0;
							}
						}
					}
				}
			}
		
		protected	function getAvailableColumns()
			{
				return $this->organizeByKey($this->query("describe `".$this->getTable()."`")->getResults(),'Field');
			}
		
		public	function getCompById($ID, $table = 'components', $organize = 'ref_page')
			{
				$table		=	$this->getTable();
				$comp		=	$this->query("select * from `{$table}` where `ID` = :0",array($ID))->getResults(true);
				$count		=	0;
				$page		=	'';
				$children	=	false;
				if(!empty($comp[$organize])) {
					# The page unique_id
					$pageId		=	$comp[$organize];
					# Get the menu data
					$getMenu	=	$this->query('select * from `main_menus` where `unique_id` = :0',array($pageId))->getResults(true);
					$page		=	(!empty($getMenu['full_path']))? $getMenu['full_path'] : '/';
				}
				
				if(!empty($comp['unique_id']) && isset($comp['parent_id'])) {
					$uniqueId	=	$comp['unique_id'];
					$children	=	$this->organizeByKey($this->query("select `ID` from `{$table}` where `parent_id` = :0",array($uniqueId))->getResults(),'ID');
					if(!empty($children))
						$children	=	array_keys($children);
					$count		=	($children != 0)? count($children) : 0;
				}
				
				return	array(
							'orphans'=>array(
								'count'=>$count,
								'children'=>$children
							),
							'component'=>$comp,
							'page'=>$page
						);
			}
		
		public	function uploadFile($table = 'components')
			{
				$table 		=	$this->getTable();
				$files		=	$this->toArray($this->getDataNode('_FILES'));
				$allowed	=	$this->query("select * from `file_types` where `page_live` = 'on'")->getResults();
				$custDir	=	$this->query("select * from `upload_directory` where `assoc_table` = '{$table}' and `page_live` = 'on'")->getResults(true);
				$fileTypes	=	array();
				if(!empty($allowed)) {
					foreach($allowed as $row) {
						$fileTypes[$row['file_type']][]	=	$row['file_extension'];
					}
				}
			
				if($custDir == 0)
					$custDir	=	$this->query("select * from upload_directory where `assoc_table` = 'media'")->getResults(true);
				
				$upPath		=	(!empty($custDir['file_path']))? NBR_ROOT_DIR.str_replace('/',DS,$custDir['file_path']) : NBR_CLIENT_DIR.DS.'images'.DS.$table.DS;
				$upPath		=	$this->toSingleDs($upPath);
				$usergroup	=	(!empty($custDir['usergroup']))? $this->convertUserGroup($custDir['usergroup']) : NBR_WEB;
				$active		=	(!empty($custDir['page_live']))? $custDir['page_live'] : 'off';
				
				if($active == 'off')
					return;
				elseif(!$this->getHelper('UserEngine')->allowIf($usergroup))
					return;
				elseif(!$this->isDir($upPath))
					return;
				elseif(empty($fileTypes))
					return;
				elseif(empty($files))
					return;
				
				foreach($files as $name => $rows) {
					$i = 0;
					foreach($rows as $payload) {
						$full	=	$this->toSingleDs($upPath.DS.$payload['name']);
						if(!($is_up = is_uploaded_file($payload['tmp_name'])) || !move_uploaded_file($payload['tmp_name'],$full))
							die('Could not move file: '.(!$is_up)? "Not an uploaded file.": "Failed to move to folder.");
							
						$columns[$i]['file_path']	=	'/'.trim(str_replace(array(DS,NBR_ROOT_DIR),array('/',''),$upPath),'/').'/';
						$columns[$i]['file_name']	=	$payload['name'];
						$columns[$i]['file_size']	=	$payload['size'];
						# Record upload transaction
						$this->getHelper('nUploader')
							->setData(array(
								array_merge(array('file_type'=>$payload['type']),$columns[$i])
							))
							->recordTransaction();
						
						$i++;
					}
				}
				
				return $columns;
			}
			
		public	function deleteComponentImage($ID = false)
			{
				$ID		=	(!empty($ID) && is_numeric($ID))? $ID : $this->getPost('ID');
				$query	=	$this->nQuery()
					->query("select CONCAT(`file_path`,`file_name`) as filename from `".$this->getTable()."` where `ID` = :0",array($ID))
					->getResults(true);
				
				if(is_file($filename = $this->toSingleDs(NBR_ROOT_DIR.DS.$query['filename']))) {
					$thumbDir	=	$this->toSingleDs(pathinfo($filename,PATHINFO_DIRNAME).DS.'thumbs'.DS.pathinfo($filename,PATHINFO_BASENAME));
					if(is_file($thumbDir)) {
						if(!unlink($thumbDir))
							$this->saveIncidental($this->getRequest('action'),array('msg'=>'Could not delete thumbnail image'));
					}
					
					if(!unlink($filename))
						$this->saveIncidental($this->getRequest('action'),array('msg'=>'Could not delete image'));
				}
				
				if(!$this->getIncidental($this->getRequest('action'))) {
					$this->nQuery()->query("UPDATE `".$this->getTable()."` set `file_name` = '', `file_path` = '', `file_size` = '' where ID = :0",array($ID));
				}
			}
		
		public	function updateComponentImage($ID = false,$table = false, $filename = false)
			{
				$table		=	(!empty($table))? $table : $this->getPost('table');
				$this->setTable($table);
				# Component database ID
				$ID			=	(!empty($ID) && is_numeric($ID))? $ID : $this->getPost('ID');
				# New file name from post
				$filename	=	(!empty($filename))? $filename : $this->getPost('filename');
				# Fetch the component from the database
				$comp		=	$this->getCompById($ID);
				# Get the extension
				$extension	=	pathinfo($comp['component']['file_name'],PATHINFO_EXTENSION);
				# Set the old file path
				$oldFile	=	$this->toSingleDs(NBR_ROOT_DIR.DS.$comp['component']['file_path'].$comp['component']['file_name']);
				# Set the old file name to var
				$oFilename	=	$comp['component']['file_name'];
				# Create new file var
				$nFilename	=	$filename.'.'.$extension;
				# Get the base folder
				$basedir	=	pathinfo($oldFile,PATHINFO_DIRNAME);
				# Current path for renaming function - File
				$oldPath	=	$oldFile;
				# Current path for renaming function - Thumb
				$oldThumb	=	$basedir.DS.'thumb'.DS.$oFilename;
				# NEW path for renaming - File
				$newPath	=	$basedir.DS.$nFilename;
				# NEW path for renaming function - Thumb
				$newThumb	=	$basedir.DS.'thumb'.DS.$nFilename;
				# Rename
				if(is_file($oldPath)) {
					if(!rename($oldPath,$newPath))
						$this->saveIncidental($this->getPost('action'),array('msg'=>'Could not rename file.'));
					else {
						if(is_file($oldThumb)) {
							if(!rename($oldThumb,$newThumb))
								$this->saveIncidental($this->getPost('action'),array('msg'=>'Could not rename thumbnail file.'));
						}
						
						$this->nQuery()->query("update `".$this->getTable()."` set `file_name` = :0 where ID = :1",array($nFilename,$ID));
					}
				}
			}
		
		public	function saveRequestData($data = false)
			{
				if(!$this->isAdmin())
					return;
				# Fetch the post array
				$POST	=	$this->toArray($this->getPost());
				# Get the table
				$rTable	=	(!empty($POST['requestTable']))? $POST['requestTable'] : 'components';
				# Get the request table
				$table	=	$this->safe()->sanitize($rTable);
				# Make the default table the request table
				$this->setTable($table);
				# If there is an action to delete, just do so
				if(isset($POST['delete'])) {
					if(is_array($POST['ID'])) {
						foreach($POST['ID'] as $ID)
							$this->deleteComponent($table,$ID);
					}
					else
						$this->deleteComponent($table,$POST['ID']);
				}
				else {
					# If there is a password field being submitted
					if(isset($POST['password'])) {
						# Trim the password to determine if empty
						$password	=	trim($POST['password']);
						# If updating current
						if(!empty($POST['ID'])) {
							# If the password is empty, update the rest of the feilds
							if(empty($password))
								unset($POST['password']);
							else {
								# If not empty, hash the password
								$POST['password']	=	$this->hashUserPassword($password);
							}
						}
						# If new component
						else {
							# If the password is empty, just stop. Don't add to table with empty password
							if(empty($password)) {
								$this->saveError('table_edit',array('msg'=>'Password cannot be empty'));
								return;
							}
							# Hash password
							else {
								$POST['password']	=	$this->hashUserPassword($password);
							}
						}
					}
					# Save component
					$this->saveComponent($POST,true);
				}
			}
			
		protected	function hashUserPassword($password)
			{
				$PasswordEngine	=	$this->getHelper('PasswordVerify');
				return $PasswordEngine->hash($this->safe()->decode($password))->getHash();
			}
		
		public	function getHash($password)
			{
				return $this->hashUserPassword($password);
			}
		
		public	function runRawQuery($input = false,$type = false)
			{
				if(!$this->isAdmin())
					return;
				
				try {
					$type	=	(!empty($type))? $type : $this->getPost('type');
					$input	=	(!empty($input))? $input : $this->safe()->decode($this->getPost('sql'));
					$query	=	$this->query($input);
					
					if($type == 'select')
						$this->saveSetting('core_database_raw_query',$query->getResults());
				}
				catch(\PDOException $e) {
					$this->saveSetting('core_database_raw_query',$e->getMessage());
				}
			}
		
		public	function createInsert($array,$returnCols = true)
			{
				$columns	=	array_keys($array);
				$cols		=	'`'.implode('`, `',$columns).'`';
				$vals		=	':'.implode(', :',$columns);
				
				if($returnCols)
					return "({$cols}) VALUES({$vals})";
				else
					return "({$vals})";
			}
			
		public	function saveToComponentLocale()
			{
				if(!$this->isAdmin())
					return;
				$nQuery		=	$this->nQuery();
				# Get the component id
				$ID			=	$this->getPost('ID');
				# Get locales list
				$locales	=	$this->toArray($this->getPost('component_locales'));
				# Remove all sets
				$nQuery->query('delete from `component_locales` WHERE comp_id = :0',array($ID));
				
				if(!empty($locales)) {
					$bindKeys	=	array_map(function($v) {
						return ":{$v}";
					},array_keys($locales));
					$thisObj	=	$this;
					$vals		=	array_map(function($v) use ($ID,$thisObj) {
						$id	=	str_replace(array('.',' '),'',$thisObj->fetchUniqueId().microtime());
						return "({$ID},{$v},'on','".$id."')";
					},$bindKeys);
					
					$sql	=	"INSERT INTO `component_locales` (`comp_id`,`locale_abbr`,`page_live`,`unique_id`) VALUES".implode(',',$vals);
					$nQuery->query($sql,$locales);
				}
				
				$this->getHelper('nRouter')->addRedirect($this->localeUrl($this->getPageURI('full_path')));
			}
	}