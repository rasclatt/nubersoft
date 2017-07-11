<?php
function randomCryptIv($size = 'aes-256-ctr')
	{
		return bin2hex(openssl_random_pseudo_bytes(openssl_cipher_iv_length($size)));
	}