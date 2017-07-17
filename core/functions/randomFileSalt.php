<?php
function randomFileSalt()
	{
		$key	=	md5(mt_rand(1000000,9999999));
		return $key;
	}