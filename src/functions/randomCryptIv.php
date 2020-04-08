<?php
function randomCryptIv($size = 'AES-256-CTR')
{
    if(empty($size))
        $size    =    'AES-256-CTR';

    return bin2hex(
            openssl_random_pseudo_bytes(
                openssl_cipher_iv_length($size)
            )
    );
}