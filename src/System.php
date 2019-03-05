<?php
namespace Nubersoft;

class System extends nSession\Controller
{
	public	function login($username, $password)
	{
		$validate	=	$this->validate($username, $password, true);
		
		if(empty($validate)) {
			$this->toError('Invalid username or password', false, false);
			return false;
		}
		else {
			$user	=	$validate['user'];
			
			if($user['user_status'] != 'on') {
				$this->toError('Account has been disabled.');
				return false;
			}
			elseif(!$validate['allowed']) {
				$this->toError('Invalid username or password.');
				return false;
			}
			
			$this->toUserSession($user);
			
			$this->toSuccess('User successfully logged in.');
			return true;
		}
	}
	
	public	function validate($username, $password, $return = false)
	{
		$user	=	$this->getHelper('nUser')->getUser($username);
		
		if(empty($user['ID']))
			return false;
		
		$valid		=	password_verify($password, $user['password']);
		$allowed	=	($valid && ($user['user_status'] == 'on'));
		
		if(empty($return))
			return $allowed;
		
		if(!is_numeric($user['usergroup']))
			$user['usergroup']	=	(int) constant($user['usergroup']);
		
		return [
			'valid' => $valid,
			'status' => $user['user_status'],
			'allowed' => $allowed,
			'is_admin' => ($user['usergroup'] <= NBR_ADMIN),
			'user' => $user
		];
	}
	
	public	function logout($redirect = false)
	{
		$this->destroy();
		
		if($redirect) {
			$this->getHelper('nRouter\Controller')->redirect($redirect);
		}
	}
	
	public	function toUserSession($user)
	{
		$this->set('user', $user);
	}
	
	public	function downloadFile($file)
	{
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.basename($file).'"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: '.filesize($file));
		readfile($file);
		exit;
	}
	
	public	function deleteFile($file, $ID = false, $table = false)
	{
		$update	=	false;
		$err	=	'File resource could not be deleted.';
		$succ	=	'File resource deleted.';
		if(!is_file($file)) {
			if(!empty($table) && !empty($ID)) {
				$update	=	true;
				$this->toSuccess($succ);
			}
			else {
				$this->toError($err);
				return false;
			}
		}
		else {
			if(unlink($file)) {
				$thumb	=	$this->deleteThumbnail($file);
			
				if(is_file($thumb)) {
					if(unlink($thumb)) {
						$this->toSuccess("Thumbnail removed.");
					}
					else {
						$this->toError("Thumbnail failed to be removed.");
					}
				}
				
				if(!empty($table) && !empty($ID))
					$update	=	true;
				
				$this->toSuccess($succ);
			}
			else {
				$this->toError($err);
				return false;
			}
		}
		
		if($update)
			$this->query("UPDATE {$table} SET file_path = '', file_name = '', file_size = '' WHERE ID = ?",[$ID]);
	}
	
	public	function deleteThumbnail($filename)
	{
		$info	=	pathinfo($filename);
		
		$thumb	=	$info['dirname'].DS.'thumbs'.DS.$info['filename'].'.'.$info['extension'];
		
		return $thumb;
	}
	
	public	function createReWrite($content, $path, $ext = '.htaccess')
	{
		$file	=	$path.DS.$ext;
		file_put_contents($file, $content);
		return is_file($file);
	}
}