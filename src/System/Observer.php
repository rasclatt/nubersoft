<?php
namespace Nubersoft\System;

use \Nubersoft\ {
    nQuery\enMasse as nQueryTrait,
    nRender\enMasse as nRenderTrait,
    nRouter\Controller as Router,
    Settings\Controller as Settings,
    nSession
};

class Observer extends \Nubersoft\System implements \Nubersoft\nObserver
{
    use nQueryTrait,
        nRenderTrait;
    
    private    $Router, $Settings, $Session;
    
    public    function __construct(
        Router $Router,
        Settings $Settings,
        nSession $Session
    )
    {
        $this->Router   =   $Router;
        $this->Settings    =    $Settings;
        $this->Session    =    $Session;
        
        return parent::__construct(...func_get_args());
    }
    
    public function listen()
    {
        switch($this->getRequest('action')) {
            case('logout'):
                $this->Session->destroy();
                $this->Router->redirect('?msg=success_logout');
                break;
            case('download_file'):
                $token    =    $this->getSession('token_page');
                if(empty($token)) {
                    $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('invalid_request'));
                    return false;
                }
                
                $dec    =    json_decode($this->getHelper('nCrypt')->decOpenSSLUrl($this->getGet('id',false)), 1);
                
                if(empty($dec)) {
                    $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('invalid_file'));
                    return false;
                }
                
                if(!empty($dec['table'])) {
                    $image    = $this->query("SELECT CONCAT('".NBR_DOMAIN_ROOT."', `file_path`, `file_name`) as `file` FROM {$dec['table']} WHERE ID = ?", [$dec['ID']])->getResults(1);
                    $msg    =    'No image found.';
                    if(empty($image['file'])) {
                        $this->toError($msg);
                        return false;
                    }
                    elseif(!is_file($image['file'])) {
                        $this->toError($msg);
                        return false;
                    }
                    else {
                        $this->downloadFile($image['file']);
                    }
                }
                else {
                    if(empty($dec['file'])) {
                        $this->toError($msg);
                        return false;
                    }
                    elseif(!is_file($dec['file'])) {
                        $this->toError($msg);
                        return false;
                    }
                    else {
                        $this->downloadFile($dec['file']);
                    }
                }
                break;
            case('delete_file'):
                $token    =    $this->getSession('token_page');
                if(empty($token)) {
                    $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('invalid_request'));
                    return false;
                }
                
                $dec    =    json_decode($this->getHelper('nCrypt')->decOpenSSLUrl($this->getGet('id',false)), 1);
                
                if(empty($dec)) {
                    $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('download_invalid'));
                    return false;
                }
                    
                if(!empty($dec['table'])) {
                    $image    = $this->query("SELECT CONCAT('".NBR_DOMAIN_ROOT."', `file_path`, `file_name`) as `file` FROM {$dec['table']} WHERE ID = ?", [$dec['ID']])->getResults(1);
                    
                    $this->deleteFile($image['file'], $dec['ID'], $dec['table']);
                }
                else {
                    $this->deleteFile($image['file']);
                }
                
                $this->Router->redirect($this->getPage('full_path'));
            case('clear_cache'):
                foreach($this->getFilesFolders(NBR_CLIENT_CACHE, ['dbcreds.php']) as $key => $path) {
                    $path    =    $path->__toString();
                    if(is_dir($path) && ($path !== NBR_CLIENT_CACHE))
                        $remove['dir'][]    =    $path;
                    else {
                        unlink($path);
                    }
                }
                
                if(!empty($remove['dir'])) {
                    arsort($remove['dir']);
                    foreach($remove['dir'] as $dir) {
                        if(is_dir($dir)) {
                            rmdir($dir);
                        }
                    }
                }
                $this->redirect('?msg=success_cachedeleted');
                //$this->toSuccess($this->getHelper('ErrorMessaging')->getMessageAuto('success_cachedeleted'));
        }
        
        switch($this->getPost('action')) {
            case('login'):
                $data    =    $this->validate($this->getPost('username', true), $this->getPost('password', true), true);
                
                $auth    =    $this->getHelper('Settings')->getSystemOption('frontend_admin');
                
                if(($data['is_admin'] && $auth == 'off') && ($this->getPage('is_admin') != 1)) {
                    $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('access_admin'));
                    return false;
                }
                $this->loginUser();               
                break;
            case('two_factor_auth'):
                $this->adminLogin();
                break;
            case('nbr_get_form_token'):
                $this->getFormToken();
                break;
            case('sign_up'):
                $this->getHelper('nUser')->create($this->getPost());
        }
    }
    
    public    function getFilesFolders($dir, $skip = false)
    {
        return new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),\RecursiveIteratorIterator::SELF_FIRST);
    }
    
    public    function loginUser()
    {
        $token  =   (!empty($this->getPost('token')['login']))? $this->getPost('token')['login'] : false;
        
        if(empty($token)) {
            $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('invalid_request'), false, false);
            return false;
        }
        $Token      =   $this->getHelper('nToken');

        $matched    =   $Token->match('login', $token);

        if(!$matched) {
            $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('invalid_token'), false, false);
            return false;
        }
        
        $this->login($this->getPost('username', false), $this->getPost('password', false));

        if($this->isLoggedIn()){
            $Token->set('login', true);
            $this->Router->redirect($this->getPage('full_path'));
        }
    }
    
    protected    function adminLogin()
    {
        $token  =   (!empty($this->getPost('token')['login']))? $this->getPost('token')['login'] : false;
        if(empty($token)) {
            $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('invalid_request'), false, false);
            return false;
        }
        $Token      =   $this->getHelper('nToken');
        $matched    =   $Token->match('login', $token);

        if(!$matched) {
            $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('invalid_token'), false, false);
            return false;
        }

        if(empty($this->getPost('code_validate'))) {

            if(!empty($this->getDataNode('_SESSION')['code_validate'])) {
                return false;
            }

            $validation    =    $this->validate($this->getPost('username', false), $this->getPost('password', false), true);
            
            if($validation['allowed']) {
                $code        =    substr(md5(rand()),0,10);
                $success    =    $this->getHelper('Emailer')
                    ->addTo($validation['user']['email'])
                    ->addFrom(WEBMASTER)
                    ->addSubject('Login validation code')
                    ->addMessage('Your validation code to log in is: '.$code, false)
                    ->send();
                if($success)
                    $Token->set('code_validate', ['code' => $code, 'user' => $validation['user']['ID']]);
                else
                    $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('failed'));
            }
            else {
                $exists    =    (empty($validation['user']));
                
                $this->toError((!$exists)? $this->getHelper('ErrorMessaging')->getMessageAuto('account_disabled') : $this->getHelper('ErrorMessaging')->getMessageAuto('invalid_user'));
            }
        }
        else {

            $code    =    (!empty($this->getDataNode('_SESSION')['code_validate']['code']))? $this->getDataNode('_SESSION')['code_validate']['code'] : false;

            $valid    =    (!empty($this->getPost('code_validate')))? $this->getPost('code_validate') : false;

            if(empty($code) || empty($valid)) {
                if(empty($code))
                    $Token->destroy('code_validate');

                $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('invalid_code'));
                return false;
            }

            if($valid != $code) {
                $Token->destroy('code_validate');
                $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('invalid_code'));
                return false;
            }

            $user    =    $this->getHelper('nUser')->getUser($this->getDataNode('_SESSION')['code_validate']['user'],'ID');
            $this->toUserSession($user);

            if($this->isLoggedIn())
                $this->Router->redirect($this->getPage('full_path'));
            else
                $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('fail_login'));
        }
    }
    
    public    function getFormToken()
    {
        if(!$this->isAjaxRequest()) {
            $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('invalid_request'), false, false);
            return false;
        }
        $Token  =   $this->getHelper('nToken');

        if(!$Token->tokenExists('login'))
            $Token->setToken('login');

        if(!$Token->tokenExists('page'))
            $Token->setToken('page');

        $this->ajaxResponse([
            'login' => $Token->getToken('login', false),
            'nProcessor' => $Token->getToken('page', false)
        ]);
    }
    
    public    function setEditMode()
    {
        $this->Session->set('editor', ($this->getRequest('active') == 'on'));
    }
    /**
     *    @description    
     */
    public    function sendEmail()
    {
        $Token  =   $this->getHelper('nToken');
        $token    =    (!empty($this->getPost('token')['nProcessor']))? $this->getPost('token')['nProcessor'] : false;
        if(empty($token)) {
            $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('invalid_request'));
            return $this;
        }
        if(!$Token->match('page', $token, false, false)) {
            $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('invalid_request'));
            return $this;
        }
        # Set automator
        $nAutomator =   new \Nubersoft\nAutomator();
        # See if there is an email automation
        $emwf   =   $nAutomator->getClientWorkflow('email');
        # See if there is an email automation
        $emwfs   =   $nAutomator->getSystemWorkflow('email');
        # If email automation, run it.
        if($emwf || $emwfs) {
            $arr    =   $nAutomator->normalizeWorkflowArray(($emwf)? $emwf : $emwfs);
            $nAutomator->doWorkflow($arr);
            return $this;
        }
        # Send back for chainging
        return $this;
    }
	/**
	 *	@description	Allows content to be ajax based on a component key
	 */
	public	function componentToPage()
	{
        $err    =   [
            'alert' => 'Invalid request'
        ];
        # Check background token
        if(empty($this->getSession('token_page')))
            $this->ajaxResponse($err);
        # Stop if empty component request
        if(empty($this->getPost('deliver')['ID']))
            $this->ajaxResponse($err);
        # Fetch component
        $comp   =   $this->getHelper('Settings')->getComponent($this->getPost('deliver')['ID'], 'ID', false);
        # Check authentication
        if(empty($comp) || !$this->getHelper('Settings\Admin')->authComponent($comp)) {
            $resp   =   (empty($comp))? ['alert' => 'Invalid request'] : [];
            $this->ajaxResponse($resp);
        }
        # Respond
        $this->ajaxResponse([
            'html' => [
                $this->setPluginContent('layout_code', $comp)->getPlugin('layout', basename($comp['component_type']).'.php')
            ],
            'sendto' => [
                (!empty($this->getPost('deliver')['sendto']))? $this->getPost('deliver')['sendto'] : ""
            ]
        ]);
	}
	/**
	 *	@description	
	 */
	public	function componentSentinel()
	{
        switch($this->getPost('action')) {
            case('viewblocklayout'):
                
                $this->ajaxResponse([
                    'html' => [
                         $this->getHelper('nMarkUp')->useMarkUp($this->dec($this->Settings->getComponentBy(['ID' => $this->getPost('component')])[0]['content'])).'<script>(new AnimatorFX()).applynFx();</script>'
                    ],
                    'sendto' => [
                        '#loadspot-modal'
                    ]
                ], 1);
            case('updatecomporder'):
                foreach($this->getPost('data') as $component) {
                    $this->query("UPDATE components SET page_order = ? WHERE ID = ?", [$component['page_order'],$component['component']]);
                }
                $this->ajaxResponse(["updated" => true]);
            case('updatecompactive'):
                
                $status =   $this->query("SELECT `page_live` FROM components WHERE ID = ?", [ $this->getPost('data')['component']])->getResults(1);
                $mode   =   ($status['page_live'] == 'on')? 'off' : 'on';
                $this->query("UPDATE components SET page_live = ? WHERE ID = ?", [$mode, $this->getPost('data')['component']]);
                $this->ajaxResponse([
                    "updated" => true,
                    "mode" => $mode
                ]);
        }
	}
	/**
	 *	@description	
	 */
	public	function editComponent()
	{
        $comp   =   $this->Settings->getComponentBy([
            'ID' => $this->getPost('deliver')['ID']
        ])[0];
        
        $this->ajaxResponse([
            'html' => [
                $this->setPluginContent('component_content', $comp)->getPlugin('component', 'modal.php')
            ],
            'sendto' => [
                '#loadspot-modal'
            ],
            'title' => 'Editing Component ID# '.$comp['ID']
        ], 1);
	}
}