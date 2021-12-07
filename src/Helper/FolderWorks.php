<?php
namespace Nubersoft\Helper;

use \Nubersoft\Dto\Helper\FolderWorks\IsDirRequest;

class FolderWorks
{
    /**
     *	@description	Checks if a folder exists, if not can create
     */
    public static function isDir(IsDirRequest $request): bool
    {
        $exists = is_dir($request->dir);

        if (!$exists) {
            if (!$request->create) {
                return $exists;
            } else {
                mkdir($request->dir, $request->perm, true);
            }
        }

        return is_dir($request->dir);
    }
}