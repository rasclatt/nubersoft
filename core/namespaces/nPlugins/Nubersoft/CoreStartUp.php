<?php
namespace nPlugins\Nubersoft;

class CoreStartUp extends \Nubersoft\nApp
	{	
		public	function createDefine(\Nubersoft\GetSitePrefs $GetSitePrefs)
			{
				$path		=	$GetSitePrefs->getClientDefine();
				# Extract ondefine from either a registry array or registry file
				$defines	=	$GetSitePrefs->getClientDefinesFromRegistry($this->getDataNode('registry'));
				# If those fail stop
				if(empty($defines))
					throw new Exception('No defines to create.');
				# Get the path info for the save file
				$pathInfo	=	pathinfo($path);
				$dir		=	$pathInfo['dirname'].DS;
				# Makes sure the path is created
				if(!$this->isDir($dir))
					return false;
				# Start making some php scripting
				$wTxt[]	=	'<?php';
				$wTxt	=	array_merge($wTxt,$defines);
				# Remove the file first if one exists
				if(is_file($path))
					unlink($path);
				# Save the file to disk
				$this->saveFile(implode(PHP_EOL,$wTxt),$path,array('secure'=>false));
				# Return the success
				return (is_file($path))? filemtime($path) : false;
			}
	}