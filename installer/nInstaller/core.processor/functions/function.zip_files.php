<?php
	function zip_files($rootdir = false)
		{
			return new ZipEngine($rootdir);
		}
?>