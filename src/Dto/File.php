<?php
namespace Nubersoft\Dto;

class File extends \SmartDto\Dto
{
    public $name = '';
    public $file_key_name = '';
    public $name_date = '';
    public $path_default = '';
    public $path_alt = '';
    public $type = '';
    public $tmp_name = '';
    public $error = 0;
    public $size = 0;
    public $size_attr;
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        $array['size_attr'] = new File\SizeAttr(($array['size_attr'])?? []);
        return $array;
    }
}