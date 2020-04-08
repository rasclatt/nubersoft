<?php
namespace Nubersoft;
/**
 *    @description    
 */
class ErrorMessaging extends \Nubersoft\nApp
{
    use \Nubersoft\Settings\enMasse;
    /**
     *    @description    
     */
    public    static    function getMessage($code)
    {
        switch($code) {
            case(1):
                return 'Login successful.';
            case(2):
                return 'Invalid username or password.';
            default: 
                $code    =    (new ErrorMessaging())->getComponentBy(['category_id' => 'error_code', 'ref_anchor' => $code]);
                
                return (empty($code))? 'An unknown error occurred.' : $code[0]['content'];
        }
    }
}