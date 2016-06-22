<?php
namespace Nubersoft;

class nErrors
	{
		public	static	function directory($inc,$e)
			{
				$dir		=	explode(_DS_,str_replace(NBR_ROOT_DIR,'',$inc));
				$report[]	=	'<div>';
				$str		=	'';
				foreach($dir as $folder) {
					$str		.=	$folder._DS_;
					$isDir		=	(@is_dir(NBR_ROOT_DIR._DS_.str_replace(_DS_._DS_,_DS_,$str)))? '&#9989;' : '&#10071';
					$report[]	=	$isDir.'&nbsp;'.$str;
				}
				$report[]	=	'</div>';
				
				return printpre($e->getMessage().implode('<br />'.PHP_EOL,$report),'{backtrace}');
			}
	}