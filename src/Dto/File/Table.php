<?php
namespace Nubersoft\Dto\File;

class Table extends \SmartDto\Dto
{
    public string $file = '';
    public string $file_path = '';
    public string $file_name = '';
    public ?int $file_size = 0;
    public string $tmp_name = '';
    public string $type = '';
    public string $name = '';
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        $new['file_name'] = ($array['name']?? false != false)? preg_replace('/[^A-Z0-9_-]/i', '', pathinfo($array['name'], PATHINFO_FILENAME)) . '.' . pathinfo($array['name'], PATHINFO_EXTENSION) : '';
        $new['file_path'] = ($array['path_default']?? false != false)? pathinfo($array['path_default'], PATHINFO_DIRNAME) . DS : '';
        $new['file_size'] = $array['size']?? 0;
        $new['file'] = $new['file_path'] . $new['file_name'];
        $new['tmp_name'] = ($array['tmp_name'])?? '';
        $new['type'] = ($array['type'])?? '';
        $new['name'] = ($array['name'])?? '';
        return $new;
    }
}