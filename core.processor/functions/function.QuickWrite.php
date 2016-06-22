<?php
function QuickWrite($settings = false)
	{
		
		$write		=	(!empty($settings["data"]))? $settings["data"]:false;
		$dir		=	(!empty($settings["dir"]))? $settings["dir"]:NBR_ROOT_DIR."/../logs/";
		$filename	=	(!empty($settings["filename"]))? $dir.$settings["filename"]:"file".time().".txt";
		$skip		=	(!empty($settings["skip_post"]));
		$max		=	(!empty($settings["max"]))? $settings["max"] : 5*pow(1024, 2);
		$mode		=	(!empty($settings["mode"]))? $settings["mode"] : "a";
		
		if(!$write)
			return;
			
		if(!$skip) {
			if(empty(\nApp::getPost()))
				return;
		}
			
		ob_start();
?>
//-----------------------------------------------------------------------------------//
<?php echo date("Y-m-d H:i:s")." (".date_default_timezone_get().")".PHP_EOL; ?>
<?php echo (is_array($write))? printpre($write) : $write;

		echo PHP_EOL;
		$data	=	ob_get_contents();
		ob_end_clean();
		
		$data	=	strip_tags($data);
		
		if(!is_dir($dir))
			mkdir($dir,0755,true);
		else {
			if(!is_file(str_replace(_DS_._DS_,_DS_,$dir._DS_.".htaccess"))) {
				\nApp::nFunc()->autoload('CreateHTACCESS',NBR_FUNCTIONS);
				CreateHTACCESS(array("rule"=>'server_rw','dir'=>$dir));
			}
			//echo '<br />'.$filename."=>".filesize($filename).">".$max;
			
			if(is_file($filename)) {
				if(filesize($filename) >= $max) {
					rename($filename,$filename.time().".ARCH");
				}
			}
			
			$append		=	'';
			if(strpos($mode,'r') !== false) {
				if(file_exists($filename)) {
					$append	=	file_get_contents($filename);
				}
				else {
					file_put_contents($filename,'');
				}
			}
			$fopen	=	fopen($filename,$mode);
			fwrite($fopen,$data);
			fwrite($fopen,$append);
			fclose($fopen);
		}
	}