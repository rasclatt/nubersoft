<?php
namespace Nubersoft\Dto\Helper\Html;

class CreateMetaRequest extends \SmartDto\Dto
{
    public string $name;
    public string $content;
    public bool $trunc = false;
}