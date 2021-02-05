<?php
namespace Nubersoft;

class nSession extends \Nubersoft\DataNode
{
    public function start()
    {
        if(empty($_SESSION))
            session_start();
    }
    
    public function destroy($key = false)
    {
        if(empty($key)) {
            $this->removeNode('_SESSION');
            $_SESSION    =    null;
            unset($_SESSION);
            session_destroy();
            return true;
        }
        else {
            if(isset($_SESSION[$key])) {
                $_SESSION[$key]    =    null;
                if(isset($_SESSION[$key]))
                    unset($_SESSION[$key]);
                
                $this->removeNode('_SESSION',$key);
            }
            elseif($this->keyExists('_SESSION', $key)) {
                $this->removeNode('_SESSION',$key);
            }
        }
        
        return $this;
    }
    
    public function get($key = false)
    {
        if(!empty($this->getDataNode('_SESSION')))
            $SESS    =    $this->getDataNode('_SESSION');
        elseif(!empty($_SESSION))
            $SESS    =    $_SESSION;
        
        if(empty($SESS))
            return [];
        
        if($key)
            return (isset($SESS[$key]))? $SESS[$key] : null;
        
        return $SESS;
    }
    
    public function set($key, $value)
    {
        $_SESSION[$key]    =    $value;
        $SESS            =    $this->getDataNode('_SESSION');
        $SESS[$key]        =    $value;
        $this->setNode('_SESSION', $SESS);
        
        return $this;
    }
	/**
	 *	@description	
	 */
	public function newSessionId()
	{
        session_regenerate_id();
        return $this;
	}
}