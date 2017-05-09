<?php
function delete_contents($filename = false)
	{
		$dFiles		=	(!is_array($filename))? array($filename) : $filename;
		$dEngine	=	new recursiveDelete();
		
		foreach($dFiles as $destination) {
			$dEngine->addTarget($destination);
		}
		
		return	$dEngine->deleteAll(30);
	}