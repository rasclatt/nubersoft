<?php
namespace Nubersoft;
/**
 *    @description    
 */
class Locale extends \Nubersoft\nApp
{
    private    $data,
            $country,
            $states;
    /**
     *    @description    
     */
    public function get($cou = false, $abbrCnt = 3)
    {
        $this->getLocaleData($abbrCnt);
        
        if(!empty($cou)) {
            return (isset($this->data[$cou]))? $this->data[$cou] : false;
        }
        
        return $this->data;
    }
    
    public function getLocaleData($abbrCnt)
    {    
        foreach([NBR_CLIENT_SETTINGS, NBR_SETTINGS] as $dir) {
            if(is_file($path = $dir.DS.'locale'.DS.'locale_list.xml')) {
                $reg    =    $this->toArray(simplexml_load_file($path));
                break;
            }
        }
        
        $this->data    =    ArrayWorks::organizeByKey($reg['locale'], 'abbr'.$abbrCnt,['unset'=>false]);
        return $this;
    }
    
    public function getCountry($cou, $abbrCnt = 3)
    {
        $this->country    =    $this->get($cou, $abbrCnt);
        return $this;
    }
    
    public function getStates($abbr = 'USA')
    {
        foreach([NBR_CLIENT_SETTINGS, NBR_SETTINGS] as $dir) {
            if(is_file($path = $dir.DS.'locale'.DS.'states.xml')) {
                $reg    =    $this->toArray(simplexml_load_file($path));
                break;
            }
        }
        
        $this->states    =    array_map(function($v){
            return array_change_key_case($v, CASE_UPPER);
        }, $reg);
        
        if(!empty($this->states[$abbr]))
            return $this->states[$abbr];
        
        return $this->states;
    }
    
    public function __call($method, $args = false)
    {
        $var    =    str_replace('get','',strtolower($method));
        
        if($var == 'all')
            return $this->country;
        elseif($var == 'data')
            return $this->data;
        
        return (!empty($this->country[$var]))? $this->country[$var] : false;
    }
}