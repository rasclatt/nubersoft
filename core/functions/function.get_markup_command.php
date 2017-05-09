<?php
/*Title: get_markup_command()*/
/*Description: This is used in the markup system as a call-back function to render arrays to the page.*/
	function get_markup_command($command = false)
		{
			if($command == false)
				return $command;
				
			if($command == 'server')
				$array	=	$_SERVER;
			elseif($command == 'session')
				$array	=	$_SESSION;
			elseif($command == 'cookie')
				$array	=	$_COOKIE;
			elseif($command == 'post')
				$array	=	$_POST;
			elseif($command == 'get')
				$array	=	$_GET;
			elseif($command == 'request')
				$array	=	$_REQUEST;
			else
				$array	=	false;
				
			return $array;
		}