<?php
namespace Nubersoft;

class nImage extends \Nubersoft\nApp
{
    public function toBase64(string $string, string $ext, bool $enc = false): string
    {
        $b64 = '';
        if(empty($string))
            return $b64;
        
        $b64 = base64_encode($string);
        
        return ($enc)? $enc.$b64 : 'data:image/'.$ext.';base64,'.$b64;
    }
    
    public function toBase64fromFile(string $file, bool $enc = false):? string
    {
        if(!is_file($file))
            return null;
        
        return $this->toBase64(file_get_contents($file), pathinfo($file, PATHINFO_EXTENSION), $enc);
    }
}