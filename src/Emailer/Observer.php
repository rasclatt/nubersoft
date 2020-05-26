<?php
namespace Nubersoft\Emailer;
/**
 *	@description	
 */
class Observer extends \Nubersoft\Emailer implements \Nubersoft\nObserver
{
    use \Nubersoft\nRender\enMasse;
	/**
	 *	@description	
	 */
	public	function listen()
	{
        if(count(array_filter([$this->getPost('subject'),$this->getPost('message')])) != 2) {
            $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('required'));
            return $this;
        }
        $success    =    $this->addTo(WEBMASTER)
            ->addFrom(WEBMASTER)
            ->addSubject($this->getPost('subject'))
            ->addMessage($this->getPost('message'), false)
            ->send();
        
        if(!$success) {
            $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('fail_email'));
            return $this;
        }
        else {
            $this->getHelper('nRouter')->redirect($this->localeUrl($this->getPage('full_path').'?msg=success_email'));
        }
	}
}