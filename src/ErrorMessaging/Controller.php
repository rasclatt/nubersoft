<?php
namespace Nubersoft\ErrorMessaging;
/**
 *	@description	
 */
class Controller extends \Nubersoft\ErrorMessaging
{
    private $locale =   'us';
    private $lang =   'en';
	/**
	 *	@description	
	 */
	public	function __construct($locale = 'us', $lang = 'en')
	{
        $this->locale   =   $locale;
        $this->lang =   $lang;
	}
    /**
	 *	@description	
	 */
	public	function installDefaultCodes()
	{
        if(empty($this->locale))
            $this->locale   =   'us';
        
        if(empty($this->lang))
            $this->lang   =   'en';
        
        foreach(self::DEFAULT_CODES as $key => $value) {
            $this->createCode($key, $value, $this->locale, $this->lang);
        }
	}
}