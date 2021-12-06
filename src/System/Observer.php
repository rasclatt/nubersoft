<?php
namespace Nubersoft\System;

use \Nubersoft\{
    nApp,
    nQuery\enMasse as nQueryTrait,
    nRender\enMasse as nRenderTrait,
    nRouter\Controller as Router,
    Settings\Controller as Settings,
    nSession,
    nObserver,
    System
};

class Observer extends System implements nObserver
{
    use nQueryTrait,
        nRenderTrait;

    private $Router, $Settings, $Session, $JWT;

    public function __construct(
        Router $Router,
        Settings $Settings,
        nSession $Session,
        nApp $nApp
    ) {
        $this->nApp = $nApp;
        $this->Router = $Router;
        $this->Settings = $Settings;
        $this->Session = $Session;
        //return parent::__construct(...func_get_args());
    }

    public function listen()
    {
        switch ($this->nApp->getRequest('action')) {
            case ('logout'):
                $this->Session->destroy();
                $this->Router->redirect('?msg=success_logout');
                break;
            case ('download_file'):
                $token = $this->nApp->getSession('token_page');
                if (empty($token)) {
                    $this->nApp->toError($this->nApp->getHelper('ErrorMessaging')->getMessageAuto('invalid_request'));
                    return false;
                }
                $msg = '';
                $dec = json_decode($this->nApp->getHelper('nCrypt')->decOpenSSLUrl($this->nApp->getGet('id', false)), 1);

                if (empty($dec)) {
                    $this->nApp->toError($this->nApp->getHelper('ErrorMessaging')->getMessageAuto('invalid_file'));
                    return false;
                }

                if (!empty($dec['table'])) {
                    $image = $this->query("SELECT CONCAT('" . NBR_DOMAIN_ROOT . "', `file_path`, `file_name`) as `file` FROM {$dec['table']} WHERE ID = ?", [$dec['ID']])->getResults(1);
                    $msg = 'No image found.';
                    if (empty($image['file'])) {
                        $this->nApp->toError($msg);
                        return false;
                    } elseif (!is_file($image['file'])) {
                        $this->nApp->toError($msg);
                        return false;
                    } else {
                        $this->downloadFile($image['file']);
                    }
                } else {
                    if (empty($dec['file'])) {
                        $this->nApp->toError($msg);
                        return false;
                    } elseif (!is_file($dec['file'])) {
                        $this->nApp->toError($msg);
                        return false;
                    } else {
                        $this->downloadFile($dec['file']);
                    }
                }
                break;
            case ('delete_file'):
                $token = $this->nApp->getSession('token_page');
                if (empty($token)) {
                    $this->nApp->toError($this->nApp->getHelper('ErrorMessaging')->getMessageAuto('invalid_request'));
                    return false;
                }

                $dec = json_decode($this->nApp->getHelper('nCrypt')->decOpenSSLUrl($this->nApp->getGet('id', false)), 1);

                if (empty($dec)) {
                    $this->nApp->toError($this->nApp->getHelper('ErrorMessaging')->getMessageAuto('download_invalid'));
                    return false;
                }

                if (!empty($dec['table'])) {
                    $image = $this->query("SELECT CONCAT('" . NBR_DOMAIN_ROOT . "', `file_path`, `file_name`) as `file` FROM {$dec['table']} WHERE ID = ?", [$dec['ID']])->getResults(1);

                    $this->deleteFile($image['file'], $dec['ID'], $dec['table']);
                }
                //  else {
                //     $this->deleteFile($image['file']);
                // }

                $this->Router->redirect($this->getPage('full_path'));
            case ('clear_cache'):
                foreach ($this->getFilesFolders(NBR_CLIENT_CACHE, ['dbcreds.php']) as $key => $path) {
                    $path = $path->__toString();
                    if (is_dir($path) && ($path !== NBR_CLIENT_CACHE))
                        $remove['dir'][] = $path;
                    else {
                        unlink($path);
                    }
                }

                if (!empty($remove['dir'])) {
                    arsort($remove['dir']);
                    foreach ($remove['dir'] as $dir) {
                        if (is_dir($dir)) {
                            rmdir($dir);
                        }
                    }
                }
                $this->nApp->redirect('?msg=success_cachedeleted');
                //$this->toSuccess($this->nApp->getHelper('ErrorMessaging')->getMessageAuto('success_cachedeleted'));
        }

        switch ($this->nApp->getPost('action')) {
            case ('login'):
                $data = $this->validate($this->nApp->getPost('username', true), $this->nApp->getPost('password', true), true);
                $auth = $this->nApp->getHelper('Settings')->getSystemOption('frontend_admin');

                if ((!empty($data['is_admin']) && $auth == 'off') && ($this->getPage('is_admin') != 1)) {
                    $this->nApp->toError($this->nApp->getHelper('ErrorMessaging')->getMessageAuto('access_admin'));
                    return false;
                }
                $this->loginUser();
                break;
            case ('two_factor_auth'):
                $this->adminLogin();
                break;
            case ('nbr_get_form_token'):
                $this->getFormToken();
                break;
            case ('sign_up'):
                $this->nApp->getHelper('nUser')->create($this->nApp->getPost());
        }
    }

    public function getFilesFolders($dir, $skip = false)
    {
        return new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
    }

    public function loginUser()
    {
        $token = (!empty($this->nApp->getPost('token')['login'])) ? $this->nApp->getPost('token')['login'] : false;

        if (empty($token)) {
            $this->nApp->toError($this->nApp->getHelper('ErrorMessaging')->getMessageAuto('invalid_request'), false, false);
            return false;
        }
        $Token = $this->nApp->getHelper('nToken');

        $matched = $Token->match('login', $token);

        if (!$matched) {
            $this->nApp->toError($this->nApp->getHelper('ErrorMessaging')->getMessageAuto('invalid_token'), false, false);
            return false;
        }

        $this->login($this->nApp->getPost('username', false), $this->nApp->getPost('password', false));

        if ($this->nApp->isLoggedIn()) {
            $Token->set('login', true);
            $this->Router->redirect($this->getPage('full_path'));
        }
    }

    protected function adminLogin()
    {
        $token = (!empty($this->nApp->getPost('token')['login'])) ? $this->nApp->getPost('token')['login'] : false;
        if (empty($token)) {
            $this->nApp->toError($this->nApp->getHelper('ErrorMessaging')->getMessageAuto('invalid_request'), false, false);
            return false;
        }
        $Token = $this->nApp->getHelper('nToken');
        $matched = $Token->match('login', $token);

        if (!$matched) {
            $this->nApp->toError($this->nApp->getHelper('ErrorMessaging')->getMessageAuto('invalid_token'), false, false);
            return false;
        }

        if (empty($this->nApp->getPost('code_validate'))) {

            if (!empty($this->getDataNode('_SESSION')['code_validate'])) {
                return false;
            }

            $validation = $this->validate($this->nApp->getPost('username', false), $this->nApp->getPost('password', false), true);

            if ($validation['allowed']) {
                $code = substr(md5(rand()), 0, 10);
                $success = $this->nApp->getHelper('Emailer')
                    ->addTo($validation['user']['email'])
                    ->addFrom(WEBMASTER)
                    ->addSubject('Login validation code')
                    ->addMessage('Your validation code to log in is: ' . $code, false)
                    ->send();
                if ($success)
                    $Token->set('code_validate', ['code' => $code, 'user' => $validation['user']['ID']]);
                else
                    $this->nApp->toError($this->nApp->getHelper('ErrorMessaging')->getMessageAuto('failed'));
            } else {
                $exists = (empty($validation['user']));

                $this->nApp->toError((!$exists) ? $this->nApp->getHelper('ErrorMessaging')->getMessageAuto('account_disabled') : $this->nApp->getHelper('ErrorMessaging')->getMessageAuto('invalid_user'));
            }
        } else {

            $code = (!empty($this->getDataNode('_SESSION')['code_validate']['code'])) ? $this->getDataNode('_SESSION')['code_validate']['code'] : false;

            $valid = (!empty($this->nApp->getPost('code_validate'))) ? $this->nApp->getPost('code_validate') : false;

            if (empty($code) || empty($valid)) {
                if (empty($code))
                    $Token->destroy('code_validate');

                $this->nApp->toError($this->nApp->getHelper('ErrorMessaging')->getMessageAuto('invalid_code'));
                return false;
            }

            if ($valid != $code) {
                $Token->destroy('code_validate');
                $this->nApp->toError($this->nApp->getHelper('ErrorMessaging')->getMessageAuto('invalid_code'));
                return false;
            }

            $user = $this->nApp->getHelper('nUser')->getUser($this->getDataNode('_SESSION')['code_validate']['user'], 'ID');
            $this->toUserSession($user);

            if ($this->nApp->isLoggedIn())
                $this->Router->redirect($this->getPage('full_path'));
            else
                $this->nApp->toError($this->nApp->getHelper('ErrorMessaging')->getMessageAuto('fail_login'));
        }
    }

    public function getFormToken()
    {
        if (!$this->nApp->isAjaxRequest()) {
            $this->nApp->toError($this->nApp->getHelper('ErrorMessaging')->getMessageAuto('invalid_request'), false, false);
            return false;
        }
        $Token = $this->nApp->getHelper('nToken');

        if (!$Token->tokenExists('login'))
            $Token->setToken('login');

        if (!$Token->tokenExists('page'))
            $Token->setToken('page');

        $this->nApp->ajaxResponse([
            'login' => $Token->getToken('login', false),
            'nProcessor' => $Token->getToken('page', false)
        ]);
    }

    public function setEditMode()
    {
        $this->Session->set('editor', ($this->nApp->getRequest('active') == 'on'));
    }
    /**
     * @description 
     */
    public function sendEmail()
    {
        $Token = $this->nApp->getHelper('nToken');
        $token = (!empty($this->nApp->getPost('token')['nProcessor'])) ? $this->nApp->getPost('token')['nProcessor'] : false;
        if (empty($token)) {
            $this->nApp->toError($this->nApp->getHelper('ErrorMessaging')->getMessageAuto('invalid_request'));
            return $this;
        }
        if (!$Token->match('page', $token, false, false)) {
            $this->nApp->toError($this->nApp->getHelper('ErrorMessaging')->getMessageAuto('invalid_request'));
            return $this;
        }
        # Set automator
        $nAutomator = new \Nubersoft\nAutomator();
        # See if there is an email automation
        $emwf = $nAutomator->getClientWorkflow('email');
        # See if there is an email automation
        $emwfs = $nAutomator->getSystemWorkflow('email');
        # If email automation, run it.
        if ($emwf || $emwfs) {
            $arr = $nAutomator->normalizeWorkflowArray(($emwf) ? $emwf : $emwfs);
            $nAutomator->doWorkflow($arr);
            return $this;
        }
        # Send back for chainging
        return $this;
    }
    /**
     *	@description	Allows content to be ajax based on a component key
     */
    public function componentToPage()
    {
        $err = [
            'alert' => 'Invalid request'
        ];
        # Check background token
        if (empty($this->nApp->getSession('token_page')))
            $this->nApp->ajaxResponse($err);
        # Stop if empty component request
        if (empty($this->nApp->getPost('deliver')['ID']))
            $this->nApp->ajaxResponse($err);
        # Fetch component
        $comp = $this->nApp->getHelper('Settings')->getComponent($this->nApp->getPost('deliver')['ID'], 'ID', false);
        # Check authentication
        if (empty($comp) || !$this->nApp->getHelper('Settings\Admin')->authComponent($comp)) {
            $resp = (empty($comp)) ? ['alert' => 'Invalid request'] : [];
            $this->nApp->ajaxResponse($resp);
        }
        # Respond
        $this->nApp->ajaxResponse([
            'html' => [
                $this->nApp->setPluginContent('layout_code', $comp)->getPlugin('layout', basename($comp['component_type']) . '.php')
            ],
            'sendto' => [
                (!empty($this->nApp->getPost('deliver')['sendto'])) ? $this->nApp->getPost('deliver')['sendto'] : ""
            ]
        ]);
    }
    /**
     *	@description	
     */
    public function componentSentinel()
    {
        switch ($this->nApp->getPost('action')) {
            case ('viewblocklayout'):

                $this->nApp->ajaxResponse([
                    'html' => [
                        $this->nApp->getHelper('nMarkUp')->useMarkUp($this->nApp->dec($this->Settings->getComponentBy(['ID' => $this->nApp->getPost('component')])[0]['content'])) . '<script>(new AnimatorFX()).applynFx();</script>'
                    ],
                    'sendto' => [
                        '#loadspot-modal'
                    ]
                ], 1);
            case ('updatecomporder'):
                foreach ($this->nApp->getPost('data') as $component) {
                    $this->query("UPDATE components SET page_order = ? WHERE ID = ?", [$component['page_order'], $component['component']]);
                }
                $this->nApp->ajaxResponse(["updated" => true]);
            case ('updatecompactive'):

                $status = $this->query("SELECT `page_live` FROM components WHERE ID = ?", [$this->nApp->getPost('data')['component']])->getResults(1);
                $mode = ($status['page_live'] == 'on') ? 'off' : 'on';
                $this->query("UPDATE components SET page_live = ? WHERE ID = ?", [$mode, $this->nApp->getPost('data')['component']]);
                $this->nApp->ajaxResponse([
                    "updated" => true,
                    "mode" => $mode
                ]);
        }
    }
    /**
     *	@description	
     */
    public function editComponent()
    {
        $comp = $this->Settings->getComponentBy([
            'ID' => $this->nApp->getPost('deliver')['ID']
        ])[0];

        $this->nApp->ajaxResponse([
            'html' => [
                $this->nApp->setPluginContent('component_content', $comp)->getPlugin('component', 'modal.php')
            ],
            'sendto' => [
                '#loadspot-modal'
            ],
            'title' => 'Editing Component ID# ' . $comp['ID']
        ], 1);
    }
}
