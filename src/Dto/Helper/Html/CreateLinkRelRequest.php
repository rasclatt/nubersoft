<?php
namespace Nubersoft\Dto\Helper\Html;

class CreateLinkRelRequest extends CreateScriptRequest
{
    public string $type = 'text/css';
    public string $rel = 'stylesheet';
}