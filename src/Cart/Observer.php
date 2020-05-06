<?php
namespace Nubersoft\Cart;
/**
 *    @description    
 */
class Observer extends \Nubersoft\Cart implements \Nubersoft\nObserver
{
    /**
     *    @description    
     */
    public    function listen()
    {
        $POST    =    $this->getPost();
        switch($POST['action']) {
            case('add_to_cart'):
                $sku    =    (!empty($POST['sku']))? $POST['sku'] : false;
                $qty    =    (!empty($POST['qty']) && is_numeric($POST['qty']))? $POST['qty'] : 1;
                
                if(empty($sku)) {
                    $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('cart_nothing'));
                    break;
                }
                
                $this->addToCart($sku, $qty)->toSuccess($this->getHelper('ErrorMessaging')->getMessageAuto('cart_added'));
                break;
        }
    }
}