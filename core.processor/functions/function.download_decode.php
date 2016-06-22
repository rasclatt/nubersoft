<?php
	function download_decode($filename = false)
		{
			if(empty($filename))
				return false;
			
			return Safe::decOpenSSL(Safe::decode($filename));
		}