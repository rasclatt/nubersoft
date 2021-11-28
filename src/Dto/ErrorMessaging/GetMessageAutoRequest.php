<?php
namespace Nubersoft\Dto\ErrorMessaging;

class GetMessageAutoRequest extends \SmartDto\Dto
{
    public $locale;
    public $locale_lang;
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        return \Nubersoft\nApp::call()->getSession();
    }
}