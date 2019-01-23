<?php
function randomCryptKey()
	{
		return bin2hex(openssl_random_pseudo_bytes(8));
	}