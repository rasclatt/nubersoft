<?php
namespace Nubersoft;
/**
 * @description Alias to the helper version
 */
use \Nubersoft\Dto\Helper\Html\ {
    CreateMetaRequest,
    CreateScriptRequest,
    CreateLinkRelRequest
};

class Html
{
    public function createMeta(
        string $name,
        string $content,
        bool $trunc = false
    ): string
    {
        return Helper\Html::createMeta(new CreateMetaRequest([
            'name' => $name,
            'content' => $content,
            'trunc' => $trunc
        ]));
    }

    public function createScript(
        string $src,
        bool $is_local = null,
        string $type = null,
        string $id = null,
        string $attr = null
    ): string
    {
        return Helper\Html::createScript(new CreateScriptRequest([
            'src' => $src,
            'is_local' => $is_local,
            'type' => $type,
            'id' => $id,
            'attr' => $attr
        ]));
    }

    public function createLinkRel(
        string $src,
        bool $is_local = null,
        string $type = null,
        string $rel = null,
        string $id = null
    ): string
    {
        return Helper\Html::createLinkRel(new CreateLinkRelRequest([
            'src' => $src,
            'is_local' => $is_local,
            'type' => $type,
            'rel' => $rel,
            'id' => $id
        ]));
    }
}
