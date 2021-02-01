<?php
namespace Nubersoft\Localization;

use \Nubersoft\ {
    nRouter as Router,
    Localization as Locale
};
/**
 *	@description	
 */
class Observer extends \Nubersoft\nSession implements \Nubersoft\nObserver
{
    use \Nubersoft\Settings\enMasse;
    use \Nubersoft\nQuery\enMasse;
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
					$this->ajaxResponse([
                        'alert' => $this->getHelper('ErrorMessaging')->getMessageAuto(403)
                    ]);
                
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
					'alert' => (!empty($this->getComponentBy($args)))? $this->getHelper('ErrorMessaging')->getMessageAuto('success_saved') : $this->getHelper('ErrorMessaging')->getMessageAuto('fail_saved')
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
        $path   =   $this->getDataNode('routing_info')['path'];
        $path   =   (empty($path))? '/' : "/{$path}/";
		$this->redirect($path);
	}
	/**
	 *	@description	
	 */
	public function apiListener()
	{
        # Hide all errors
        $this->reportErrors(0);
        if($this->getPost('service') != 'translation')
            return $this;
        elseif(!$this->isAjaxRequest())
            return $this;
        elseif(empty($this->getPost('keys'))) {
            throw new \Nubersoft\Exception\Ajax('Keys are required for translating.', 200);
        }
        
        $filter  =   $this->getSystemOption('transhost');
        $host   =   "{$this->getHost('domain')}.{$this->getHost('tld')}";
        
        if(empty($filter))
            return $this;
        
        if(!is_array($filter))
            $filter =   [$filter];
        $referrer    =   $this->getServer('HTTP_REFERER');
        $refHost    =   explode('.', parse_url($referrer)['host']);
        $refHostComb =   array_pop($refHost);
        $refHostComb =   strtolower(array_pop($refHost).'.'.$refHostComb);
        $allow  =   false;
        foreach($filter as $h) {
            if($refHostComb == strtolower($h))
                $allow  =   true;
        }
        if(!$allow)
            return $this;
        try {
            $referrer   =   Router::createRoutingData($this->getServer('HTTP_REFERER'));
            $domain =   "{$referrer['domain']}.{$referrer['tld']}";
            # Create keys
            if($this->getPost('generate') && $this->isAdmin()) {
                $Locale =   new Locale($this->getPost('lang'), 'us');
                foreach($this->getPost('generate') as $key => $value) {
                    if(!$Locale->transKeyExists($key)) {
                        $Locale->saveTransKey($key, $this->dec($value), 'auto');
                    }
                }
                $this->ajaxResponse([
                    'msg' => 'Keys run',
                    'success' => 1,
                    'referrer' => $domain,
                    'keys' => $this->getPost('generate')
                ]);
            }
            # Store all the keys to find translations for
            $keys   =   array_map(function($v){
                return "{$v}us{$this->getPost('lang')}";
            }, $this->getPost('keys'));
            # Combine language-specific and general keys
            $keys   =   array_merge($keys, $this->getPost('keys'));
            # Search
            $translations   =   $this->query("SELECT `title`, `content`, `component_type` FROM components WHERE title IN (".implode(',', array_fill(0, count($keys), '?')).")", $keys)->getResults();
            # Assemble translation response
            if(!empty($translations)) {
                foreach($translations as $row) {
                    $language   =   ($row['component_type'] == 'transkey')? 'en' : $this->getPost('lang');
                    $tk =   ($language != 'en')? substr($row['title'], 0, -4) : $row['title'];
                    $new[$language]['trans-cls'][$tk] =   $this->dec($row['content']);
                }
            }
            # Save a default reply
            if(empty($new))
                $new[$this->getPost('lang')]['trans-cls']   =   [];

            $this->ajaxResponse($new);
        }
        catch (\PDOException $e) {
            $this->ajaxResponse([
                'message' =>  $e->getMessage().'Invalid request',
                'success' => false
            ]);
        }
        catch (\Exception $e) {
            $this->ajaxResponse([
                'message' =>  $e->getMessage(),
                'success' => false
            ]);
        }
	}
}