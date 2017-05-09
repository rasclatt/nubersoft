<?php
/*Title: create_define_file()*/
/*Description: This function writes a `config.php` file based on the registry.xml file located in the /client_assets/settings/ directory. You can create your own by using <ondefine><mydefine>value for define</mydefine></ondefine>
*/

	function create_define_file($settings = false)
		{
			$filename	=	(!empty($settings['filename']))? $settings['filename'] : date('YmdHis').'_config.php';
			$dir		=	(!empty($settings['dir']))? rtrim($settings['dir'],'/') : NBR_CLIENT_DIR.'/settings';
			$type		=	(!empty($settings['type']))? $settings['type'] : 'a+';
			
			$arr		=	NuberEngine::getRegFile();
			$defineArr	=	(!empty($arr['ondefine']))? $arr['ondefine'] : false;

			if(!$defineArr)
				return false;

			AutoloadFunction('directory_exists');
			
			if(!directory_exists($dir,array("make"=>true)))
				return false;
				
			$wTxt[]	=	'<?php';
			foreach($defineArr as $defName => $defVal) {
				if(is_array($defVal))
					continue;
					
				$defVal	=	($defVal !== 'true' && $defVal !== 'false' && !is_numeric($defVal))? "'{$defVal}'" : $defVal;
				$wTxt[]	=	"define('".strtoupper($defName)."', {$defVal});";
			}
			
			if(empty($wTxt))
				return false;
			
			if(is_file($file = $dir.'/'.$filename))
				unlink($file);
			$Writer	=	new WriteToFile();
			// Write to file
			$Writer	->AddInput(array("content"=>implode(PHP_EOL,$wTxt),"save_to"=>$file, "type"=>$type))
					->SaveDocument();

			return (is_file($file))? filemtime($file) : false;
		}