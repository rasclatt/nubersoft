<?php
namespace Nubersoft\Dto\Currency;

class ToMoneyRequest extends \SmartDto\Dto
{
    public $country = '';
    public $language = '';
    public $number = 0;
    public $to = '';
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        $array['country'] = strtoupper(($array['country'])?? 'US');
        $array['language'] = strtolower(($array['langauge'])?? 'en');
        $array['number'] = (float) (($array['number'])?? 0);
        $array['to'] = ($array['to'])?? 'USD';
        return $array;
    }
}