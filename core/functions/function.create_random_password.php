<?php

	function create_random_password($settings = false)
		{
			$set_range		=	(!empty($settings['range']) && is_array($settings['range']))? $settings['range'] : range('a','z');
			$set_chars		=	(!empty($settings['special_chars']))? $settings['special_chars'] : '#?!@$%^&*-';
			
			$lowerrng		=	$set_range;
			$lowstr			=	implode("",$lowerrng);
			$upstr			=	strtoupper(implode("",$lowerrng));
			$lower			=	str_split($lowstr);
			$upper			=	str_split($upstr);
			$sybols			=	str_split($set_chars);
			shuffle($sybols);
			shuffle($upper);
			shuffle($lower);
			$chunkSym		=	array_slice($sybols,0,4);
			$chunkUp		=	array_slice($upper,0,6);
			$chunkLow		=	array_slice($lower,0,6);
			$new			=	array_merge($chunkSym,$chunkUp,$chunkLow);
			shuffle($new);
			return implode("",$new);
		}