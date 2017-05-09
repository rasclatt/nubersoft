<?php
	function get_file_extension($file = false)
		{
			return pathinfo($file,PATHINFO_EXTENSION);
		}