<?php

namespace Nubersoft\Helper;

use \Nubersoft\{
    Dto\File as FileDto,
    nApp
};

use \Nubersoft\Dto\File\Table as FileTableAttr;

class File
{
    /**
     *	@description	Returns files from the data node
     *	@returns    [null|array<Dto\File>]	
     */
    public static function getAll(): ?array
    {
        $files = (new nApp)->getDataNode('_FILES');

        if (empty($files))
            return null;

        return array_map(function ($v) {
            return new FileDto($v);
        }, $files);
    }
    /**
     *	@description	
     *	@param	
     */
    public static function get(): FileTableAttr
    {
        $FILES = self::getAll();

        if (empty($FILES))
            return new FileTableAttr();

        $file = new FileDto($FILES[0]->toArray());

        if (!empty($file->error))
            throw new \Exception((new \Nubersoft\ErrorMessaging)->getMessageAuto('fail_upload'), 500);

        if (empty($file->name))
            return new FileTableAttr();

        return new FileTableAttr($file->toArray());
    }
    /**
     *	@description	Remove a file
     */
    public static function remove(string $path)
    {
        if (!is_file($path))
            return true;
        unlink($path);
        return is_file($path);
    }
}
