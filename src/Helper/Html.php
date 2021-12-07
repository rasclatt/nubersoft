<?php
namespace Nubersoft\Helper;

use \Nubersoft\Dto\Helper\Html\ {
    CreateMetaRequest,
    CreateScriptRequest,
    CreateLinkRelRequest
};

class Html
{
    public static function createMeta(CreateMetaRequest $request): string
    {
        return '<meta ' . (($request->trunc) ? $request->name . '="' . $request->content : 'name="' . $request->name . '" content="' . $request->content). '" />' . PHP_EOL;
    }

    public static function createScript(CreateScriptRequest $request): string
    {
        return '<script type="' . $request->type . '" src="' . $request->src . '"' . $request->id . ' ' . $request->attr . '></script>' . PHP_EOL;
    }

    public static function createLinkRel(CreateLinkRelRequest $request): string
    {
        return '<link type="' . $request->type . '" rel="' . $request->rel . '" href="' . $request->src . '" />' . PHP_EOL;
    }
}