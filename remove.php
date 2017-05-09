<?php

function getContents($path)
	{
		$iter	=	new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
			RecursiveIteratorIterator::SELF_FIRST,
			RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
		);
		
		$paths	=	array('dir'=>array(),'file'=>array());
		
		foreach ($iter as $path => $dir) {
			if ($dir->isDir()) {
				$paths['dir'][] = (string) $path;
			}
			elseif ($dir->isFile()) {
				$paths['file'][] = (string) $path;
			}
		}
		
		return $paths;
	}

function iterator($paths,$dir)
	{
		if(!empty($paths['file'])) {
			foreach($paths['file'] as $file) {
				if(pathinfo($file,PATHINFO_FILENAME) != 'remove')
					unlink($file);
			}
		}
		if(!empty($paths['dir'])) {
			array_reverse($paths['dir']);
			foreach($paths['dir'] as $file) {
				rmdir($file);
			}
		}
		
		$content	=	getContents($dir);
		if(!empty($content['dir'])) {
			iterator($content,$dir);
		}
	}

$dir	=	__DIR__;
iterator(getContents($dir),$dir);