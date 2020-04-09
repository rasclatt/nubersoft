<?php
namespace Nubersoft\Localization;
/**
 *	@description	
 */
class Observer extends \Nubersoft\nSession implements \Nubersoft\nObserver
{
    use \Nubersoft\Settings\enMasse;
	/**
	 *	@description	
	 */
	public	function actionListen()
	{
        $POST	=	$this->getPost();
		$action	=	(isset($POST['subaction']))? $POST['subaction'] : $POST['action'];
		$respto	=	(!empty($POST['sendto']))? \Nubersoft\Conversion\Data::getBoolVal($POST['sendto']) : false;
		$locale	=	$this->getSession('locale');
        
		switch($action) {
			case('wb_create_translator'):
                /*
				# Assign the form
				$form		=	$this->dec($POST['deliver']['formData']);
				# Convert the string to form data
				$q			=	[];
				parse_str($form, $q);
				
				$POST	=	$q;
				*/
				if(!$this->isAdmin())
					$this->ajaxResponse(['alert' => 'Permission Denied']);
                
                if(!empty($POST['deliver']['formData'])) {
                    $POST   =   $this->getHelper('Conversion\Data')->arrayFromQueryString($POST['deliver']['formData']);
                }
                
				$args		=	[
					'title' => $POST['title'],
					'category_id' => ($POST['category_id'])?? 'translator'
				];
				
                if(!empty($POST['ref_page']))
                    $args['ref_page']   =  $POST['ref_page']; 
                
				$component	=	$this->getComponentBy($args);
				
				if(!empty($component))
					$this->deleteComponentBy($args);
				
				$args['content']	=	$this->enc($POST['description']);
				$this->addComponent($args);
				$this->ajaxResponse([
					'alert' => (!empty($this->getComponentBy($args)))? "Saved" : "An error occurred saving."
				]);
        }
	}
	/**
	 *	@description	
	 */
	public	function listen()
	{
        $redirect   =   array_filter([
            $this->setLocalization('locale', 'country'),
            $this->setLocalization('locale_lang', 'language', 'en')
        ]);
        
        if(!empty($redirect)) {
            $this->redirect($this->getRedirectingPath());
        }
	}
	/**
	 *	@description	
	 */
	private	function setLocalization($sess_tag, $req_tag, $def = 'us')
	{
        $Session    =   new \Nubersoft\nSession();
        $req    =   $this->getGet($req_tag);
        # If empty or is being set
        if(empty($this->getSession($sess_tag)) || $req) {
            # If being set
            if($req) {
                $Session->destroy($sess_tag);
                $Session->set($sess_tag, substr(strtolower($req),0,2));
                $redirect   =   true;
            }
            # If not being set, set automatically
            else {
                 $Session->set($sess_tag, $def);
            }
        }
        
        return (isset($redirect));
	}
	/**
	 *	@description	
	 */
	private	function getRedirectingPath()
	{
        $parse   =   parse_url($this->getDataNode('_SERVER')['REQUEST_URI']);
        if(!isset($parse['query']))
            return $this->getDataNode('_SERVER')['REQUEST_URI'];

        $arr    =   [];
        parse_str($parse['query'], $arr);
        if(isset($arr['country']))
            unset($arr['country']);
        if(isset($arr['language']))
            unset($arr['language']);
        
        $query  =   http_build_query($arr);
        return $parse['path'].((empty($query))? '' : '?'.$query);
	}
	/**
	 *	@description	
	 */
	public	function toggleEditMode()
	{
		if(!$this->isAdmin())
			return false;
		
		$Session	=	$this->getHelper('nSession');
		
		$Session->destroy('translator_mode');
		if($this->getGet('subaction') == 'on')
			$Session->set('translator_mode', $this->getGet('subaction'));
		
		$this->redirect($this->getDataNode('_SERVER')['REDIRECT_URL']);
	}
}