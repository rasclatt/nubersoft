<?php
namespace Nubersoft;

class nDownloader extends \Nubersoft\nRender
	{
		public	function encode($settings = false, $salt = false)
			{
				# ID, not unique_id
				$file_id	=	(!empty($settings['ID']))? $settings['ID'] : false;
				# Table name, not ID (numeric): ie. "image_bucket"
				$table_id	=	(!empty($settings['table']))? $settings['table'] : false;
				
				if(empty($file_id) || empty($table_id))
					return false;
				
				return urlencode(base64_encode($this->safe()->encOpenSSL($file_id."/".$table_id)));
			}
		
		public	function decode($filename = false)
			{
				if(empty($filename))
					return false;
				
				return $this->safe()->decOpenSSL($this->safe()->decode(base64_decode(urldecode($filename))));
			}
	}