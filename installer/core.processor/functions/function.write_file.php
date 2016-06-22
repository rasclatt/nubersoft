<?php
	function write_file($settings = false)
		{
			register_use(__FUNCTION__);
			
			$settings['content']	=	(!empty($settings['content']))? $settings['content']: false;
			$settings['save_to']	=	(!empty($settings['save_to']))? $settings['save_to']: false;
			$settings['type']		=	(!empty($settings['type']))? $settings['type']: 'a';
			
			if($settings['save_to'] == false)
				return;

			$fInfo	=	pathinfo($settings['save_to']);
			$dir	=	$fInfo['dirname'];

			// Set default write to false
			$write		=	false;
			// Create folder if not exists
			if(!is_dir($dir)) {
					if(mkdir($dir,0755,true))
						$write	=	true;
				}
			else
				$write	=	true;
				
			// If all is good, write file
			if($write != true)
				return;
			
			$fh	=	fopen($settings['save_to'], $settings['type']);
			fwrite($fh, $settings['content']);
			fclose($fh);
			
			return (is_file($settings['save_to']))? true:false;
		}