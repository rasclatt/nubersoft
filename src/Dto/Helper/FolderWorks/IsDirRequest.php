<?php
namespace Nubersoft\Dto\Helper\FolderWorks;

class IsDirRequest extends \SmartDto\Dto
{
    public string $dir = '';
    public bool $create = true;
    public int $perm = 0775;
}