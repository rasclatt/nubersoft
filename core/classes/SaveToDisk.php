<?php
class	SaveToDisk
	{
		private	function __construct()
			{
			}
		
		public	static	function Write($settings = false)
			{
				return self::save($settings);
			}
		
		private	static function save($settings = false)
			{
				$dir		=	(!empty($settings['dir']))? trim($settings['dir']) : false;
				$filename	=	(!empty($settings['filename']))? trim($settings['filename']) : 'temt.txt';
				$payload	=	(!empty($settings['payload']))? $settings['payload'] : false;
				$write		=	(!empty($settings['write']))? $settings['write'] : 'w';
				$perms		=	(!empty($settings['permission']))? $settings['permission'] : 0755;
				
				if(!is_dir($dir))
					@mkdir($dir,$perms,true);

				$file		=	str_replace(DS.DS,DS,$dir.DS.$filename);
				$payload	=	(is_array($payload))? implode(PHP_EOL,$payload) : $payload;
				$fo			=	@fopen($file,$write);
				
				if($fo) {
						fwrite($fo,$payload);
						fclose($fo);
						return true;
					}
					
				return false;
			}
	}