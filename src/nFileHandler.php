<?php
namespace Nubersoft;

/**
 * @description 
 */
class nFileHandler extends \Nubersoft\nApp
{
    private $target = [];
    /**
     * @description 
     */
    public function addTarget($path)
    {
        $this->target[] = $path;
        return $this;
    }
    /**
     * @description 
     */
    public function deleteAll($path = false)
    {
        if (!empty($path))
            $this->addTarget($path);

        foreach ($this->target as $target) {
            $this->recurseDelete($target);
        }
    }
    /**
     * @description 
     */
    public function recurseDelete($path)
    {
        if (!is_dir($path) && !is_file($path))
            return false;

        $isDir  = is_dir($path);
        $recurse = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::KEY_AS_PATHNAME | \RecursiveDirectoryIterator::SKIP_DOTS));

        foreach ($recurse as $filepath => $it) {

            if (is_file($filepath))
                unlink($filepath);

            $dir = pathinfo($filepath, PATHINFO_DIRNAME);
            if (is_dir($dir)) {
                if (count(scandir($dir)) == 2) {
                    rmdir($dir);
                }
            }
        }

        if ($isDir)
            $this->isDir($path, 1);
    }

    public function recurseClone($from, $to, $skip = ['composer.json'], $preflight = false)
    {
        # Recursively loop extracted zip folder
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($from, \RecursiveDirectoryIterator::KEY_AS_PATHNAME | \RecursiveDirectoryIterator::SKIP_DOTS)) as $key => $value) {
            # Get file/folder info so things can be put in their rightful spots
            $path  = pathinfo($key);
            $copy_from = $key;
            # Strip extract path, append to local directory path
            $copy_to = $this->toSingleDs($to . DS . str_replace($from, '', $path['dirname']));
            # If this folder is not created, do so
            if (!$preflight)
                $this->isDir($copy_to, true);
            # Append the file name
            $copy_dest = str_replace(DS . DS, DS, $copy_to . DS . $path['basename']);
            # Copy from the extraction to the destination
            if (empty($skip) || (!empty($skip) && !in_array(basename($copy_from), $skip))) {

                if ($preflight) {
                    $err[] = [
                        'from' => $copy_from,
                        'to' => $copy_dest
                    ];
                } else {
                    if (!copy($copy_from, $copy_dest))
                        $err[] = $copy_dest;
                }
            }
        }
        # Return errors
        return (!empty($err)) ? $err : false;
    }
}
