<?php
namespace Nubersoft;

class nErrors
	{
		public	static	function directory($inc,$e)
			{
				$dir		=	explode(DS,str_replace(NBR_ROOT_DIR,'',$inc));
				$report[]	=	'<div>';
				$str		=	'';
				foreach($dir as $folder) {
					$str		.=	$folder.DS;
					$isDir		=	(@is_dir(NBR_ROOT_DIR.DS.str_replace(DS.DS,DS,$str)))? '&#9989;' : '&#10071';
					$report[]	=	$isDir.'&nbsp;'.$str;
				}
				$report[]	=	'</div>';
				
				return printpre($e->getMessage().implode('<br />'.PHP_EOL,$report),'{backtrace}');
			}
			
		public	static	function installedError($active,$msg = 'nUberSoft not installed')
			{
				if(!$active) {
					trigger_error($msg,E_USER_WARNING);
					return false;
				}
				
				return true;
			}
	}