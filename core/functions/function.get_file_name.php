<?php
	function get_file_name($file = false)
		{
			return pathinfo($file,PATHINFO_FILENAME);
		}