<?php
namespace Nubersoft\Helper;

use \Nubersoft\Dto\Helper\View\RenderRequest;

class View
{
    /**
     *	@description	
     *	@param	
     */
    public static function render(RenderRequest $request)
    {
        if (!is_file($request->include))
            return null;
        $data = $request->data;
        ob_start();
        include($request->include);
        $data = ob_get_contents();
        ob_end_clean();
        return $data;
    }
}