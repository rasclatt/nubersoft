<?php
namespace Nubersoft\System\Observer;

class Tables extends \Nubersoft\System\Observer
{
	use \Nubersoft\nQuery\enMasse;
	use \Nubersoft\nDynamics;
	
	protected	$Router,
				$Token,
				$Settings;
	
	public	function __construct()
	{
		$this->Router	=	$this->getHelper('nRouter\Controller');
		$this->Token	=	$this->getHelper('nToken');
		$this->Settings	=	$this->getHelper('Settings\Controller');
	}
	
	public	function listen()
	{
		if(!$this->isAdmin()) {
			$this->toError('You must be an admin to make this change.');
			return false;
		}
		
		$Router		=	$this->Router;
		$Token		=	$this->Token;
		$DataNode	=	$this->getHelper('DataNode');
		$POST		=	(!empty($this->getPost('ID')))? $this->getPost() : array_filter($this->getPost());
		$action		=	(!empty($POST['action']))? $POST['action'] : false;
		$token		=	(!empty($this->getPost('token')['nProcessor']))? $this->getPost('token')['nProcessor'] : false;
		# Remove action k/v
		if(isset($POST['action']))
			unset($POST['action']);
		# Remove the token k/v
		if(isset($POST['token']))
			unset($POST['token']);
		#
		switch($action) {
			case('edit_table_rows_details'):
				$this->editTable($POST, $this->getRequest('table'), $token, $Token);
				return $this;
			case('edit_user_details'):
				$this->updateUserData($POST, $token, $Token, $this->getRequest('table'), $this->getRequest('ID'));
				return $this;
			case('edit_component'):
				$timestamp	=	date('Y-m-d H:i:s');
				if(!empty($POST['subaction'])) {
					$action	=	$POST['subaction'];
					unset($POST['subaction']);
					
					switch($action) {
						case('add_new'):
							$this->addNewRecord($POST);
							break;
						case('duplicate'):
							$this->duplicateRecord($POST);
					}
				}
				else {
					if($this->getPost('delete') == 'on') {
						$this->deleteRecord($POST['ID']);
					}
					else {
						$this->updateRecord($POST, $token, $Token);
					}
				}
				break;
			case('update_admin_url'):
				$this->updatePage($token, $Token, $POST);
				break;
			case('create_new_page'):
				# Match token for all
				if(empty($token) || !$Token->match('page', $token, false, false)) {
					$this->toError('Invalid request. Form token does not match.');
					return false;
				}
				
				if(empty($POST['full_path'])) {
					$dpath	=	implode('/',[date('Y'),date('m'),date('d'),date('s')]);
					$path	=	($this->getPage('is_admin') != 1)? $this->getPage('full_path').$dpath : '/'.$dpath;
				}
				else
					$path	=	$this->convertToStandardPath($POST['full_path']);
				
				@$this->nQuery()->insert("main_menus")
					->columns(['unique_id', 'full_path', 'page_live','menu_name','link'])
					->values([[$this->fetchUniqueId(), '/'.trim($path,'/').'/', 'off', 'Untitled', 'untitled'.date('YmdHis') ]])
					->write();
				$this->Router->redirect($this->localeUrl($path));
				break;
			case('update_page'):
				$this->updatePage($token, $Token, $POST);
		}
		
		return $this;
	}
	
	protected	function updatePage($token, $Token, $POST, $allow_delete = true)
	{
		# Match token for all
		if(empty($token) || !$Token->match('page', $token, false, false)) {
			$this->toError('Invalid request. Form token does not match.');
			return false;
		}

		$ID	=	$this->getPost('ID');
		
		if(empty($ID)){
			$this->toError("Invalid request. Page doesn't exist.");
			return false;
		}
		
		if(!empty($POST['delete'])) {
			if($allow_delete) {
				if($this->Settings->deletePage($ID)) {
					$this->Router->redirect('/?msg='.urlencode("Page deleted successfully. Components may have been disabled."));
				}
				return true;
			}
			else {
				$this->toError("This action doesn't allow deleting of pages.");
				return false;
			}
		}

		$POST['full_path']	=	$this->Router->convertToStandardPath($POST['full_path']);
		$existing	=	$this->Router->getPage($POST['full_path'], 'full_path');
		if(empty($POST['full_path'])) {
			$this->toError("Your slug/url can not be empty.");
			return false;
		}

		if(!empty($existing)) {
			if($existing['ID'] != $POST['ID']) {
				$this->toError("This slug/path already exists.");
				return false;
			}
		}

		unset($POST['ID']);

		if($POST['full_path'] == '/') {
			$POST['is_admin']	=	2;
			$POST['link']	=	'home';
		}
		else {
			$POST['link']	=	strtolower(pathinfo($POST['full_path'], PATHINFO_BASENAME));
		}

		$sql	=	[];
		foreach($POST as $key => $value) {
			$sql[]	=	"`{$key}` = ?";
		}

		$this->query("UPDATE `main_menus` SET ".implode(', ', $sql)." WHERE ID = ? ", array_values(array_merge(array_values($POST),[$ID])));

		if($this->getPage('full_path') != $POST['full_path']) {
			$this->Router->redirect($POST['full_path']);
		}
		else {
			$this->Router->redirect($POST['full_path'].'?msg='.urlencode("Page saved.").'&'.http_build_query($this->getGet()));
		}
	}
	
	protected	function updateData($POST, $table, $msg, $err = false)
	{
		$table	=	preg_replace('/[^0-9A-Z\_\.\`\-]/i', '', $table);
		$ID		=	(is_numeric($POST['ID']))? $POST['ID'] : false;
		
		if(empty($POST) || empty($ID)) {
			$this->toError((!empty($err))? $err : "Nothing saved.");
			return false;
		}
		# Process file and the fields associated with the file
		$this->setFileData($POST, $ID);
		
		foreach($POST as $keys => $values) {
			$bind[]	=	$values;
			$sql[]	=	"`{$keys}` = ?";
		}
		
		$this->query("UPDATE `{$table}` SET ".implode(', ', $sql)." WHERE ID = '{$ID}'",$bind);
		
		$this->toSuccess($msg);
		return true;
	}
	
	protected	function getCurrentFilePath($ID, $table = 'components')
	{
		$path	=	$this->query("SELECT CONCAT(`file_path`, `file_name`) as `image_url` FROM {$table} WHERE ID = ?", [$ID])->getResults(1);
		
		return (!empty($path['image_url']))? $path['image_url'] : false;
	}
	
	protected	function removeCurrentFilePath($ID, $table = 'components')
	{
		$file	=	$this->getCurrentFilePath($ID);
		if(!empty($file)) {
			if(is_file($img = NBR_ROOT_DIR.$file)) {
				unlink($img);
				$this->query("UPDATE {$table} SET `file_name` = '', `file_path` = '', `file_size` = '' WHERE ID = ?", [$ID]);
			}
		}
	}
	
	protected	function updateUserData($POST, $token, $Token, $table, $ID)
	{
		if(empty($token) || !$Token->match('page', $token)) {
			$this->toError('Invalid request.');
			return false;
		}

		if($this->getPost('delete') == 'on') {
			$this->query("DELETE FROM `".str_replace('`', '', $table)."` WHERE ID = ?", [$ID]);

			if(empty($this->getHelper('nUser')->getUser($ID,'ID'))) {
				$this->Router->redirect($this->localeUrl($this->getHelper('nRender')->getPage('full_path').'?table=users&msg=User+successfully+deleted'));
			}
			else {
				$this->toError("Unexpected error occurred.");
				return $this;
			}
		}
		else {
			if(!empty($POST['ID']) && is_numeric($POST['ID'])) {
				$required	=	array_filter([
					(isset($POST['username']) && filter_var($POST['username'], FILTER_VALIDATE_EMAIL))?? $POST['username'],
					(isset($POST['email']) && filter_var($POST['email'], FILTER_VALIDATE_EMAIL))?? $POST['email'],
					(isset($POST['first_name']))?? $POST['first_name'],
					(isset($POST['last_name']))?? $POST['last_name'],
					(isset($POST['usergroup']))?? $POST['usergroup'],
					(isset($POST['user_status']))?? $POST['user_status']
				]);

				if(!empty($POST['password'])) {
					$POST['password']	=	$this->getHelper('nUser')->hashPassword($this->getPost('password', false));
				}
				else {
					unset($POST['password']);
				}
				
				if(count($required) < 6) {
					$this->toError('All required fields must be filled out, or a field is not filled correctly.');
					return $this;
				}

				$this->updateData($POST, 'users', "User account saved.", "An error occurred saving user account.");
			}
			else {
				if(!empty($POST['ID'])) {
					$this->toError('Invalid request');
					return $this;
				}

				if($this->getHelper('nUser')->getUser($this->getRequest('username'))) {
					$this->toError("User already exists.");
					return $this;
				}

				$required	=	array_filter([
					(isset($POST['username']) && filter_var($POST['username'], FILTER_VALIDATE_EMAIL))?? $POST['username'],
					(isset($POST['email']) && filter_var($POST['email'], FILTER_VALIDATE_EMAIL))?? $POST['email'],
					(isset($POST['first_name']))?? $this->getPost('first_name', false),
					(isset($POST['last_name']))?? $this->getPost('last_name', false),
					(isset($POST['usergroup']))?? $POST['usergroup'],
					(isset($POST['user_status']))? $POST['user_status'] : 'on',
					(isset($POST['password']))?? trim($this->getPost('password', false))
				]);

				if(count($required) < 7) {
					$this->toError('All required fields must be filled out, or a field is not filled correctly.');
					return $this;
				}

				$POST['password']	=	trim($this->getPost('password', false));
				$POST['unique_id']	=	$this->fetchUniqueId();
				$POST['timestamp']	=	date('Y-m-d H:i:s');

				if($this->getHelper('nUser')->create($POST)) {
					$this->toSuccess('User successfully created.');
					return $this;
				}
				else {
					$this->toError("Unexpected error occurred.");
					return $this;
				}
			}
		}
	}
	
	protected	function addNewRecord($POST)
	{
		$type		=	(!empty($POST['parent_type']))? $POST['parent_type'] : 'code';
		$refpage	=	(!empty($POST['ref_page']))? $POST['ref_page'] : $this->getPage('unique_id');
		
		unset($POST['parent_type']);
		
		$this->getHelper("nQuery")
			->insert('components')
			->columns(['unique_id', 'ref_page', 'component_type', 'title', 'page_live'])
			->values([
				[$this->fetchUniqueId(), $refpage, 'code', 'Untitled ('.date('Y-m-d H:i:s').')', 'off']
			])
			->write();
			$this->toSuccess("Component added");
		
		return $this;
	}
	
	protected	function duplicateRecord($POST)
	{
		$ID	=	$POST['parent_dup'];
		unset($POST['parent_dup']);

		$duplicate	=	$this->getHelper("Settings")->getComponent($ID);
		if(empty($duplicate)) {
			$this->toError("Component doesn't exist.");
			return $this;
		}

		unset($duplicate['ID']);
		$duplicate['unique_id']	=	$this->fetchUniqueId();

		if(!empty($duplicate['file_path'])) {
			$from	=	NBR_ROOT_DIR.$duplicate['file_path'].$duplicate['file_name'];
			$finfo	=	pathinfo($from);
			$fname	=	$finfo['filename'].date('YmdHis').'.'.$finfo['extension'];
			$to		=	$finfo['dirname'].DS.$fname;
			if(!copy($from, $to)) {
				$duplicate['file_path'] =
				$duplicate['file_name']	=	'';
			}
			else {
				$duplicate['file_path']	=	str_replace(NBR_ROOT_DIR, '', $finfo['dirname'].DS);
				$duplicate['file_name']	=	$fname;
			}
		}
		$timestamp	=	date('Y-m-d H:i:s');
		if(!empty($duplicate['title']))
			$duplicate['title']	.=	"-Copy {$timestamp}";

		$duplicate['timestamp']	=	$timestamp;

		$duplicate	=	array_filter($duplicate);

		$this->getHelper("nQuery")->insert('components')
			->columns(array_keys($duplicate))
			->values([
				array_values($duplicate)
			])
			->write();
		
		$this->Router->redirect($this->localeUrl($this->getPage('full_path').'?msg='.urlencode("Component added")));
	}
	
	protected	function deleteRecord($ID)
	{
		$query	=	$this->query("SELECT unique_id FROM components WHERE ID = ?", [$ID])->getResults(1);
		$this->query("UPDATE components SET parent_id = '' WHERE parent_id = ?", [$query['unique_id']]);
		$this->query("DELETE FROM `components` WHERE ID = ?", [$ID]);
						
		if(empty($this->getHelper('Settings')->getComponent($ID))) {
			$this->toSuccess("Component Deleted");
		}
		else {
			$this->toError("Unexpected error occurred.");
		}
	}
	
	protected	function updateRecord($POST, $token, $Token)
	{
		$POST['timestamp']	=	date('Y-m-d H:i:s');
		if(empty(!$POST['ID'])) {
			if(empty($token) || !$Token->match('component_'.$POST['ID'], $token)) {
				$this->toError('Invalid request.');
				return false;
			}

			if(!empty($POST['file_name'])) {
				$fname = $this->query("SELECT `file_name` as `filename` FROM components WHERE ID = ?",[$POST['ID']])->getResults(1);
				$fname	=	(!empty($fname['filename']))? $fname['filename'] : false;

				if(!empty($fname)) {
					if($fname !== $POST['file_name']) {
						$ext	=	pathinfo($fname, PATHINFO_EXTENSION);
						$newFnm	=	trim(preg_replace('/[^A-Z0-9\-\_]/i','',pathinfo($POST['file_name'], PATHINFO_FILENAME)));

						if(!empty($newFnm)) {
							$old	=	NBR_ROOT_DIR.$POST['file_path'].$fname;
							$new	=	NBR_ROOT_DIR.$POST['file_path'].$newFnm.'.'.$ext;
							$thumb	=	NBR_ROOT_DIR.$POST['file_path'].'thumbs'.DS.$fname.'.'.$ext;

							if(is_file($thumb))
								unlink($thumb);

							rename($old, $new);

							$POST['file_name']	=	$newFnm.'.'.$ext;
						}
					}
				}
			}
			$page_match	=	($this->getPage('unique_id') == $POST['ref_page']);
			if(!$page_match) {
				$thisObj	=	$this;
				$uniques	=	[];
				$Page		=	$this->getHelper('Settings\Page\Controller');
				$struct		=	$Page->getContentStructure($this->getPage('unique_id'));
				$test		=	$this->getHelper('ArrayWorks')->recurseApply($struct, function($k, $v) use ($POST, $thisObj, &$uniques){

					if($k == $POST['unique_id']) {
						if(!empty($v)) {
							\Nubersoft\ArrayWorks::getRecursiveKeys($v, $uniques);
						}
					}
				});

				if(!empty($uniques)) {
					$c			=	count($uniques);
					$uniques	=	array_merge([$POST['ref_page']], $uniques);

					$this->query("UPDATE components SET `ref_page` = ? WHERE `unique_id` IN (".(implode(',', array_fill(0, $c,'?'))).")", $uniques);
				}
			}
			$this->updateData($POST, 'components', 'Component updated');

			if(!$page_match) {
				$newPage	=	$this->getHelper('nRouter')->getPage($POST['ref_page'], 'unique_id');

				$this->Router->redirect($newPage['full_path']);
			}
		}
	}
	
	public	function editTable($POST, $table, $token, $Token, $msg = 'Row saved')
	{
		if($table == 'users') {
			$this->updateUserData($POST, $token, $Token, $table, $this->getRequest('ID'));
			return false;
		}
		
		if(empty($token) || !$Token->match('page', $token)) {
			$this->toError('Invalid request.');
			return false;
		}
		
		if(!empty($POST['ID']) && is_numeric($POST['ID'])) {
			if(!empty($POST['delete'])) {
				$this->deleteFrom($table, $POST['ID']);
				$this->redirect($this->getPage('full_path')."?table=".$table."&msg=Row deleted");
			}
			else
				$this->updateData($POST, $table, $msg);
		}
		else {
			if(empty($POST['ID'])) {
				$POST['timestamp']	=	date('Y-m-d H:i:s');
				$POST['unique_id']	=	$this->fetchUniqueId();
				$POST	=	$this->getRowsInTable($table, $POST);
				$this->setFileData($POST);
				$sql	=	"INSERT INTO `".$table."` (`".implode('`, `', array_keys($POST))."`) VALUES(".implode(',',array_fill(0, count($POST), '?')).")";
				@$this->nQuery()->query($sql, array_values($POST));
				$this->redirect($this->getPage('full_path')."?table=".$table."&msg=Row added");
			}
			else {
				$this->toError("Invalid request.");
			}
		}
	}
	
	public function	getRowsInTable($table, $array = false)
	{
		$columns	=	$this->getColumnsInTable($table);
		
		if(is_bool($array))
			return $columns;
		
		foreach($array as $key => $value){
			if(!in_array($key, $columns)){
				unset($array[$key]);
			}
		}
		
		return $array;
	}
	
	protected	function setFileData(&$POST, $ID = false)
	{
		$FILES	=	$this->getDataNode('_FILES');
		
		if(!empty($FILES[0])) {
			if($FILES[0]['error'] == 0) {
				if(is_numeric($ID))
					$this->removeCurrentFilePath($ID);
				$POST['file_name']	=	$FILES[0]['name'];
				$POST['file_path']	=	pathinfo($FILES[0]['path_default'], PATHINFO_DIRNAME).DS;
				$POST['file_size']	=	$FILES[0]['size'];
				if(!move_uploaded_file($FILES[0]['tmp_name'], str_replace(DS.DS,DS,NBR_DOMAIN_ROOT.DS.$POST['file_path'].DS.$POST['file_name']))) {
					unset($POST['file_name'], $POST['file_path'], $POST['file_size']);
					$this->toError('File failed to upload. Check permissions.');
				}
			}
			else
				$this->toError('File failed to upload. Check permissions.');
		}
	}
	
	public	function deleteFrom($table, $value, $col = "ID")
	{
		@$this->nQuery()->query("DELETE FROM {$table} WHERE {$col} = ?", [$value]);
	}
}