<?php
namespace Nubersoft\System\Admin;

use \Nubersoft\ {
    nApp,
    Helper\File,
    System\Observer as System,
    Settings\enMasse as Settings,
    nFileHandler,
    ErrorMessaging
};
use \Nubersoft\Dto\System\Admin\Observer\SaveSiteLogoRequest;

/**
 * @description 
 */
class Observer extends System
{
    use Settings;

    protected $nApp, $Errors, $FileHandler, $request;

    /**
     *	@description	
     */
    public function __construct(
        nApp $nApp,
        ErrorMessaging $Errors,
        nFileHandler $FileHandler
    )
    {
        $this->FileHandler = $FileHandler;
        $this->Errors = $Errors;
        $this->nApp = $nApp;
        $this->request = $this->nApp->getPost();
    }
    /**
     * @description
     */
    public function listen()
    {
        $subaction = $this->nApp->getPost('deliver')['subaction'];
        $layout = $this->getSettingsLayout($subaction);
        $modal = (!empty($this->nApp->getPost('deliver')['modal']));

        if (empty($layout)) {
            $this->nApp->ajaxResponse([
                "alert" => "Layout for this settings page is not set."
            ]);
        }
        $response = [
            'html' => [
                $layout
            ],
            'title' => 'Editing ' . ucwords($subaction) . ' Settings.',
            'sendto' => [
                (!empty($this->nApp->getPost('deliver')['sendto'])) ? $this->nApp->getPost('deliver')['sendto'] : '#admin-content'
            ]
        ];

        $this->nApp->ajaxResponse($response, $modal);
    }
    /**
     * @description 
     */
    public function widgetManager()
    {
        if (!$this->nApp->isAdmin())
            return false;

        $plugin = $this->nApp->getRequest('slug');

        if (empty($plugin)) {
            $this->nApp->toError($this->Errors->getMessageAuto('fail_widget'));
            return false;
        }

        $Widget = new \Nubersoft\Widget($this->nApp->getRequest('slug'));

        switch ($this->nApp->getRequest('action')) {
            case ('activate_widget'):
                if ($Widget->isActive()) {
                    $this->nApp->toSuccess($this->Errors->getMessageAuto('success_pluginactive'));
                    return false;
                }
                $this->installWidget($Widget);

                break;
            case ('deactivate_widget'):
                if (!$Widget->isActive()) {
                    $this->nApp->toSuccess($this->Errors->getMessageAuto('success_plugininactive'));
                    return false;
                }

                $this->deleteWidget($Widget);
                break;
        }
    }
    /**
     * @description 
     */
    private function installWidget(\Nubersoft\Widget $Widget, $preflight = false)
    {
        $act = $Widget->getActions();
        $blf = $Widget->getBlockflows();
        $actions = (!empty($act)) ? str_replace(["\t", PHP_EOL], ["", ""], rtrim(ltrim($act->default->asXML(), '<default>'), '</default>')) : false;
        $blockflows = (!empty($blf)) ? str_replace(["\t", PHP_EOL], ["", ""], rtrim(ltrim($Widget->getBlockflows()->default->asXML(), '<default>'), '</default>')) : false;

        $path = NBR_CLIENT_DIR;
        $plugin_name = $Widget->getSlug();
        $base = $path . DS . 'plugins' . DS . $plugin_name;

        if (!empty($actions)) {
            if ($this->getComponentBy(["category_id" => $plugin_name, 'component_type' => 'plugin_action'], '=', 'AND', 'COUNT(*) as count')[0]['count'] == 0) {
                if (!$preflight) {
                    $this->addComponent([
                        "category_id" => 'widget_' . $plugin_name,
                        'component_type' => 'plugin_action',
                        'content' => $this->nApp->enc($actions)
                    ]);
                }
            }
        }

        if (!empty($blockflows)) {
            if ($this->getComponentBy(["category_id" => $plugin_name, 'component_type' => 'plugin_blockflow'], '=', 'AND', 'COUNT(*) as count')[0]['count'] == 0) {
                if (!$preflight) {
                    $this->addComponent([
                        "category_id" => 'widget_' . $plugin_name,
                        'component_type' => 'plugin_blockflow',
                        'content' => $this->nApp->enc($blockflows)
                    ]);
                }
            }
        }
        # Convert the xml to settings
        $widget = $this->nApp->toArray($Widget->getConfig());
        # Loop each router
        if (isset($widget['router'])) {
            if (!$preflight)
                $this->createRouters($widget);
        }
        if (!empty($widget['vendor'])) {
            if (isset($widget['vendor']['name'])) {
                if (!is_array($widget['vendor']['name'][0]))
                    $widget['vendor']['name'] = [$widget['vendor']['name']];

                $to = NBR_VENDOR;
                $from = $base . DS . 'vendor';
                $Files = ( new \Nubersoft\nFileHandler);
                foreach ($widget['vendor']['name'] as $vendor) {

                    $this->nApp->isDir($to . DS . $vendor, 1);
                    $this->nApp->isDir($from . DS . $vendor, 1);

                    $Files->recurseClone($from . DS . $vendor, $to . DS . $vendor, ['composer.json'], $preflight);
                }
            }
        }

        if (!empty($widget['template']['name'])) {
            if (!is_array($widget['template']['name']))
                $widget['template']['name'] = [$widget['template']['name']];

            if (!isset($Files))
                $Files = ( new \Nubersoft\nFileHandler);

            foreach ($widget['template']['name'] as $template) {

                if (!is_dir($base . DS . 'template' . DS . $template))
                    continue;

                $Files->recurseClone($base . DS . 'template' . DS . $template, NBR_CLIENT_DIR . DS . 'template' . DS . $template, false, $preflight);
            }
        }

        if (!empty($widget['plugins']['name'])) {
            if (!is_array($widget['plugins']['name']))
                $widget['plugins']['name'] = [$widget['plugins']['name']];

            if (!isset($Files))
                $Files = ( new \Nubersoft\nFileHandler);

            foreach ($widget['plugins']['name'] as $plugin) {
                $fromPlug = $base . DS . $plugin . DS . 'plugins' . DS . $plugin;
                if (!is_dir($fromPlug))
                    continue;

                $Files->recurseClone($fromPlug, NBR_CLIENT_DIR . DS . 'template' . DS . 'plugins' . DS . $plugin, false, $preflight);
            }
        }

        $Widget->activate();
    }
    /**
     * @description 
     */
    private function deleteWidget(\Nubersoft\Widget $Widget, $preflight = false)
    {
        $act = $Widget->getActions();
        $blf = $Widget->getBlockflows();
        $actions = (!empty($act)) ? str_replace(["\t", PHP_EOL], ["", ""], rtrim(ltrim($act->default->asXML(), '<default>'), '</default>')) : false;
        $blockflows = (!empty($blf)) ? str_replace(["\t", PHP_EOL], ["", ""], rtrim(ltrim($Widget->getBlockflows()->default->asXML(), '<default>'), '</default>')) : false;

        $path = NBR_CLIENT_DIR;
        $plugin_name = $Widget->getSlug();
        $base = $path . DS . $plugin_name;

        if (!empty($actions)) {
            $this->deleteComponentBy([
                "category_id" => 'widget_' . $plugin_name,
                'component_type' => 'plugin_action'
            ]);
        }

        if (!empty($blockflows)) {
            $this->deleteComponentBy([
                "category_id" => 'widget_' . $plugin_name,
                'component_type' => 'plugin_blockflow'
            ]);
        }
        # Convert the xml to settings
        $widget = $this->nApp->toArray($Widget->getConfig());
        # Loop each router
        if (isset($widget['router'])) {
            $this->query("DELETE FROM main_menus WHERE parent_id = ?", 'widget_' . $plugin_name);
        }
        if (!empty($widget['vendor'])) {
            if (isset($widget['vendor']['name'])) {
                if (!is_array($widget['vendor']['name'][0]))
                    $widget['vendor']['name'] = [$widget['vendor']['name']];

                $to = NBR_VENDOR;
                $from = $base . DS . 'vendor';
                $Files = ( new \Nubersoft\nFileHandler);
                foreach ($widget['vendor']['name'] as $vendor) {
                    $Files->recurseDelete($to . DS . $vendor);
                    if (is_dir($vendor))
                        rmdir($vendor);
                }
            }
        }

        if (!empty($widget['template']['name'])) {
            if (!is_array($widget['template']['name']))
                $widget['template']['name'] = [$widget['template']['name']];

            if (!isset($Files))
                $Files = ( new \Nubersoft\nFileHandler);

            foreach ($widget['template']['name'] as $template) {
                $Files->recurseDelete(NBR_CLIENT_DIR . DS . 'template' . DS . $template);
                if (is_dir($vendor))
                    rmdir($vendor);
            }
        }

        if (!empty($widget['plugins']['name'])) {
            if (!is_array($widget['plugins']['name']))
                $widget['plugins']['name'] = [$widget['plugins']['name']];

            if (!isset($Files))
                $Files = ( new \Nubersoft\nFileHandler);

            foreach ($widget['plugins']['name'] as $plugin) {
                $Files->recurseDelete(NBR_CLIENT_DIR . DS . 'template' . DS . 'plugins' . DS . $plugin);
            }
        }

        $Widget->deactivate();
    }

    private function createRouters($widget)
    {
        $Page = new \Nubersoft\Settings\Page();
        if (!isset($widget['router'][0]))
            $widget['router'] = [$widget['router']];

        foreach ($widget['router'] as $router) {
            if (stripos($router['parent_id'], 'widget_') === false)
                $router['parent_id'] = 'widget_' . $router['parent_id'];

            $Page->createPage($router);
        }
    }

    private function getSettingsLayout($type)
    {
        return $this->nApp->getPlugin('settings', DS . $type . '.php');
    }
    /**
     * @description Saves settings from the admin area(s)
     */
    public function saveSettings()
    {
        # Go throught the post
        foreach ($this->nApp->getPost('setting') as $name => $value) {
            # If the value is an array, save the array to json
            if (is_array($value))
                $value = json_encode($value);
            # Create the htaccess by default
            if ($name == 'htaccess') {
                file_put_contents(NBR_DOMAIN_ROOT . DS . '.htaccess', $this->nApp->dec($value));
            }
            # Remove the option so it can be resaved
            $this->deleteSystemOption($name);
            # Resave
            $this->setSystemOption($name, $value);
            if ($name == 'composer') {
                # Turn up the execution time for the script
                ini_set('max_execution_time', 600);
                # Save the composer file
                file_put_contents(NBR_ROOT_DIR . DS . 'composer.json', $this->nApp->dec($value));
                # See if shell is allowed
                if (defined("SHELL_ALLOWED") && SHELL_ALLOWED) {
                    # See if the composer is ready
                    if (defined("SHELL_COMPOSER") && SHELL_COMPOSER) {
                        $result = shell_exec(SHELL_COMPOSER . ' update --working-dir=' . NBR_ROOT_DIR);
                    }
                }
            }
        }
        # After saving the prerences, reload them to the data node so they are updated.
        ( new \Nubersoft\DataNode)->setNode('settings', [
            'system' => (new \Nubersoft\Settings\Controller)->getSettings(false, 'system')
        ]);
        # Create a success message
        $this->nApp->toSuccess(( new \Nubersoft\ErrorMessaging)->getMessageAuto('success_settingssaved'));
    }
    /**
     * @description Saves the site logo from admin settings
     */
    public function saveSiteLogo(SaveSiteLogoRequest $request)
    {
        $FILES = File::get();
        $toggle = $request->setting->header_company_logo_toggle;
        $this->deleteSystemOption('header_company_logo_toggle');
        $this->setSystemOption('header_company_logo_toggle', $toggle);
        $companyFile = $this->getSystemOption('header_company_logo');
        $defmimes = $request->allowed_mimes;

        if($request->delete == 'on' && (!empty($companyFile) && is_file($f = NBR_DOMAIN_ROOT.$companyFile))) {
            $this->deleteSystemOption('header_company_logo');
            unlink($f);
        }
        if (empty($FILES->file))
            return false;

        if (!in_array($FILES->type, $defmimes)) {
            $this->nApp->toError(( new \Nubersoft\ErrorMessaging)
                ->getMessageAuto('required_filetype') . ': ' . implode(', ', $defmimes));
            return false;
        }

        $destination = NBR_DOMAIN_CLIENT_DIR . DS . 'media' . DS . 'images' . DS . 'default' . DS . 'company_logo.' . pathinfo($FILES->name, PATHINFO_EXTENSION);

        $this->nApp->isDir(pathinfo($destination, PATHINFO_DIRNAME), true);

        if (move_uploaded_file($FILES->tmp_name, $destination)) {
            $this->deleteSystemOption('header_company_logo');
            $this->setSystemOption('header_company_logo', str_replace(NBR_DOMAIN_ROOT, '', $destination));
            $this->nApp->toSuccess(( new \Nubersoft\ErrorMessaging)->getMessageAuto('success_upload'));
        } else
            $this->nApp->toError(( new \Nubersoft\ErrorMessaging)->getMessageAuto('fail_upload'));
    }
    /**
     *	@description	
     */
    public function saveJWT()
    {
        $path = \Nubersoft\JWT\Controller::getJwtPath();
        $jwtNew = preg_replace('/[^\dA-Z_\-]/i', '', ($this->request['token_name']) ?? false);

        if (!$jwtNew) {
            if (is_file($path)) {
                unlink($path);
            }
        } else {
            file_put_contents($path, $jwtNew);
        }

        $this->nApp->ajaxResponse([
            'alert' => (is_file($path)) ? "JWT secret created." : "JWT secret does not exist.",
            'input' => [
                $jwtNew
            ],
            'sendto' => [
                'input[name="token_name"]'
            ]
        ]);
    }
    /**
     *	@description	
     */
    public function decodeBlock()
    {
        $nQuery = ( new \Nubersoft\nQuery);
        $table = $nQuery->stripTableName($this->request['deliver']['table']);
        $column = $nQuery->stripTableName($this->request['deliver']['column']);
        $ID = $this->request['deliver']['ID'];
        $content = $this->nApp->enc($this->nApp->dec($this->nApp->dec($this->query("SELECT {$column} FROM `{$table}` WHERE ID = ?", [$ID])->getResults(1)[$column])));

        $this->query("UPDATE {$table} SET `{$column}` = ? WHERE ID = ?", [$content, $ID]);

        $this->nApp->ajaxResponse([
            'html' => [
                $content
            ],
            'sendto' => [
                $this->request['deliver']['sendto']
            ]
        ]);
    }
}
