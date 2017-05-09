<?php
function get_file_dir($table = false,$default = false)
	{
		if(empty($table))
			return false;
			
		return \nApp::getUploadDir($table,$default);
	}