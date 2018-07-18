<?php
namespace Nubersoft\nDownloader;

class Model extends \Nubersoft\nApp
{
	public	function download()
	{
		$args	=	func_get_args();

		if(empty($args)) {
			$this->toErrorMsg($this->__('This function requires arguments.'));
			return false;
		}
		
		$file	=	$args[0];
		$type	=	(!empty($args[1]))? $args[1] : 'application/octet-stream';
		
		if(!is_file($file)) {
			$this->toErrorMsg($this->__('File not found.'));
			return false;
		}
		
		$fileSz		=	filesize($file);
		$fileData	=	pathinfo($this->stripRoot($file));
		$docs		=	[
			'unique_id' => $this->fetchUniqueId(),
			'ip_address'=>$this->getClientIp(),
			'action' => $this->getRequest('action'),
			'username' => $this->getSession('username'),
			'file_path' => $fileData['dirname'],
			'full_path' => $this->stripRoot($file),
			'file_name' => $fileData['basename'],
			'file_size' => $fileSz,
			'file_mime' => $fileData['extension'],
			'download_count' => 1,
			'timestamp' => date('Y-m-d H:i:s')
		];
		$c	=	count($docs);
		for($i =0; $i < $c; $i++)
			$bind[]	=	"?";

		$con	=	$this->nQuery()->getConnection();
		$query	=	$con->prepare("INSERT INTO file_activity (`".implode('`,`',array_keys($docs))."`) VALUES(".implode(', ',$bind).")");
		$query->execute(array_values($docs));
		
		$this->downloadFile($file,$type);
	}
	
	public	function downloadFile()
	{
		$args	=	func_get_args();
		$file	=	(!empty($args[0]) && is_string($args[0]))? $args[0] : false;
		$type	=	(!empty($args[1]) && is_string($args[1]))? $args[1] : false;
		
		if(count(array_filter([$file,$type])) != 2) {
			trigger_error($this->__('File name and/or file type are not valid.'));
			return false;
		}
		
		if(!is_file($file)) {
			$this->toMsgAlert($this->__('File does not exist.'));
			return false;
		}
		$fileData						=	pathinfo($file);
		$fileSz							=	filesize($file);
		$headers['Content-Type']		=	$type;
		$headers['Content-Disposition']	=	'attachment; filename='.$fileData['basename'];
		$headers['Cache-Control']		=	'no-cache, no-store, must-revalidate';
		$headers['Pragma']				=	'no-cache';
		$headers['Expires']				=	'0';
		$headers['Content-Length']		=	$fileSz;
		
		if(!empty($args[2])) {
			foreach($args[2] as $header) {
				$key			=	key($header);
				$headers[$key]	=	$header[$key];
			}
		}
		
		$headers	=	array_filter($headers);
		
		foreach($headers as $key => $value)
			header($key.': '.$value);
		
		readfile($file);
		exit;
	}
}