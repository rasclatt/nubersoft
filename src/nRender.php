<?php
namespace Nubersoft;

use \Nubersoft\ {
    nQuery,
    nSession\User
};
use \Nubersoft\Dto\Settings\Page\View\ConstructRequest as Helpers;
use \Nubersoft\Dto\DataNode\ {
    Routing\GetRequest as Routing,
    Templates\GetRequest as Templates,
    Header\GetRequest as Header
};

class nRender extends nQuery
{
    use nUser\enMasse,
        Plugin\enMasse,
        Conversion\enMasse,
        Settings\enMasse,
        Settings\Page\enMasse;
    
    protected $Helpers, $sUser, $routing, $templates;
    
    public function __construct(Helpers $Helpers)
    {
        $this->templates = new Templates();
        $this->Helpers = $Helpers;
        $this->routing = new Routing();
        $this->sUser = User::get();
        return parent::__construct();
    }
    /**
     *    @description    
     */
    public function userGet($key = false)
    {
        if(!empty($key))
            return ($this->sUser->{$key})?? null;
        
        return $this->sUser;
    }
    /**
     *    @description    
     */
    public function getHeader()
    {
        $data = $this->Helpers->Settings->getHeaderPrefs('html');
        if(!empty($data['toggle']) && $data['toggle'] == 'on') {
            return $this->Helpers->MarkDown->useMarkUp($this->dec($data['value'])).PHP_EOL;
        }
    }
    /**
     *    @description    
     */
    public function getFooter()
    {
        return $this->Helpers->MarkDown->useMarkUp(
            $this->dec($this->Helpers->Settings->getFooterPrefs())
        );
    }
    /**
     *    @description    
     */
    protected function setHeaderCode($code, $path = false, $msg = false)
    {
        # Stop if no page is set in permissions
        http_response_code($code);
        # If no path or message, don't die
        if(empty($path)) {
            if(empty($msg))
                return false;
        }
        if(!empty($path)) {
            # Loop through templates and render denied page
            foreach($this->templates->paths as $page_path) {
                if(is_file($login = str_replace(DS.DS,DS,$page_path.DS.$path))) {
                    # Render layout
                    echo parent::render($login, $this);
                    exit;
                }
            }
        }
        die($msg);
    }
    /**
     *    @description    
     */
    public function render()
    {
        $page = $this->getPage();
        $code = (new Header())->header_response_code;
        # Set the response code here
        $this->setHeaderCode($code);
        # If not admin, set to frontend template
        if($this->routing->page_live != 'on' && !$this->isAdmin()) {
            $code = 404;
            # Show error page
            $temp = 'errors';
        }
        else {
            # Check if this current page is an admin page
            $is_admin = ($this->routing->is_admin == 1);
            $temp = (!$is_admin)? 'frontend' : 'backend';
        }
        # Get the layout
        $layout = (!empty($this->templates->{$temp}))? $this->templates->{$temp} : 'false';
        # Redirect
        if(!empty($page->auto_fwd) && !$this->isAdmin()) {
            $page->auto_fwd = trim($page->auto_fwd);
            $Router = $this->Helpers->Router;
            $external = preg_match('/^http/i', $page->auto_fwd);
            $redirect = (!$external)? $this->localeUrl($page->auto_fwd) : $page->auto_fwd;
            
            if($page->auto_fwd_post == 'off') {
                $Router->redirect($redirect);
            }
            else {
                if($page->auto_fwd_post == 'on') {
                    if($this->isLoggedIn()) {
                        $Router->redirect($redirect);
                    }
                }
            }    
        }
        # Check for page permissions
        if($this->routing->session_status == 'on') {
            if(!$this->isLoggedIn()) {
                # Loop through templates and render login page
                foreach($this->templates->paths as $path) {
                    if(is_file($login = str_replace(DS.DS,DS,$path.DS.$temp.DS.'login.php'))) {
                        echo $this->renderBeforeHint($login);
                        # Render layout
                        echo parent::render($login, $this);
                        exit;
                    }
                }
            }
            else {
                # Fetch the user's permission level
                $usergroup = $this->userGet()->usergroup;
                # Make sure it's numeric
                if(!is_numeric($usergroup))
                    $usergroup = constant($usergroup);
                # Fetch the page group
                $pagegroup = $page->usergroup;
                # If not set, then defu
                if(empty($pagegroup))
                    $pagegroup = NBR_WEB;
                # Convert to numeric if not already
                if(!is_numeric($pagegroup))
                    $pagegroup = constant($pagegroup);
                # Check if usergroup good enough
                if($pagegroup < $usergroup) {
                    $this->setHeaderCode(403, DS.'errors'.DS.'permission.php', "Permission Denied.");
                }
            }
        }
        # If the page requires admin access
        if($this->routing->is_admin == 1 && !$this->isAdmin()) {
            # If not admin, redirect to home page
            $this->Helpers->Router->redirect($this->localeUrl());
        }
        # Stop processing if ajax.
        if($this->isAjaxRequest()) {
            # Fetch the uri of the current page
            $current = $this->Helpers->Cookie->get('nbr_current_page');
            # Redirect path
            $path     = (!empty($current['request']))? $current['request'] : '/';
            # If nothing has happened by now, it's not going to
            $this->ajaxResponse([
                "alert" => $this->getHelper('ErrorMessaging')->getMessageAuto('ajax_invalid'),
                "html" => [
                    "<script>window.location='".$path."';</script>"
                ],
                "sendto" => [
                    "body"
                ]
            ]);
        }
        else {
            # Check if the page is being cached
            if($this->routing->auto_cache == 'on' && !$this->isAdmin()) {
                # See if the user is logged in and set name
                $usergroup     = (!empty($this->getSession('user')['usergroup']))? $this->getSession('user')['usergroup'] : 'loggedout';
                # Convert a string to numeric
                if(!is_numeric($usergroup) && ($usergroup != 'loggedout'))
                    $usergroup = constant($usergroup);
                # See if locale is set
                $locale = (!empty($this->getSession('site')['locale']))? $this->getSession('site')['locale'] : 'USA';
                # Create the cache destination
                $destination = strtolower(NBR_CLIENT_CACHE.DS.'page'.DS.md5(json_encode($this->getGet())).DS.$locale.DS.$usergroup.DS.$page->ID.".html");
                # Create the cache path
                $this->isDir(pathinfo($destination, PATHINFO_DIRNAME), true);
                $Cache = $this->getHelper('nCache');
                $Cache->start($destination);
                echo $this->renderBeforeHint($layout);
                if(!$Cache->isCached()) {
                    echo parent::render($layout, $this);
                }
                echo $Cache->render();
            }
            else { 
                # Try last ditch effort to fetch a template
                if($layout == 'false') {
                    foreach($this->templates->paths as $path) {
                        if($layout != 'false')
                            continue;
                        if(is_dir($l = $path.DS.$temp))
                            $layout =   $l.DS.'index.php';
                    }
                }
                if($layout == 'false')
                    throw new \Exception('Templates are not working correctly.', 500);
                # Fetch the system settings
                $settings = ($this->Helpers->Settings->system)?? false;
                # If available, lets see status
                if($settings) {
                    $settings = \Nubersoft\ArrayWorks::organizeByKey($settings, 'category_id');
                    $live =   ((($settings['site_live']['option_attribute'])?? 'on') == 'on');
                    if(!$live && !($this->getPage('is_admin') == 1)) {
                        
                        if(!$this->isAdmin())
                            throw new \Nubersoft\HttpException('Site is not available at this time.', 10000);
                    }
                    $maintenance  = ((($settings['maintenance_mode']['option_attribute'])?? 'off') == 'on');
                    if($maintenance && !($this->getPage('is_admin') == 1)) {
                        if(!$this->isAdmin())
                            throw new \Nubersoft\HttpException('Site is not available at this time and is undergoing maintenance. We\'ll be back soon!', 10002);
                    }
                }
                echo $this->renderBeforeHint($layout);
                # Render layout
                echo parent::render($layout, $this);
                # Reset the warnings back to the system
                dns_get_record(gethostbyaddr($this->getServer('SERVER_ADDR')));
                # Restore errors after render
                restore_error_handler();
            }
        }
    }
    /**
     *    @description    
     */
    public function getContent()
    {
        $unique_id = (!empty($this->getDataNode('routing')['unique_id']))? $this->getDataNode('routing')['unique_id'] : false;
        
        if(empty($unique_id))
            return false;
        
        return $this->select()->from('components')->where([
            ['c' => 'ref_page', 'v'=> $unique_id, 'co' => 'AND'],
            ['c' => 'page_live', 'v' => 'on']
        ])->fetch();
    }
    /**
     *    @description    
     */
    public function getTitle($default = false, $tags = true)
    {
        $title = (!empty($this->getDataNode('routing')['menu_name']))? $this->getDataNode('routing')['menu_name'] : false;
        
        if(empty($title)) {
            $title = $default; 
        }
        
        $title = '<title>'.$title.'</title>'.PHP_EOL;
        
        return (!$tags)? trim(strip_tags($title)) : $title;
    }
    /**
     *    @description    
     */
    public function getMeta(array $add = null)
    {
        $meta = (!empty($this->getDataNode('routing')['page_options']['meta']))? $this->getDataNode('routing')['page_options']['meta'] : $this->getSitePreferences('header_meta');
        
        if(empty($meta) && empty($add))
            return false;
        
        $Html     = $this->getHelper('Html');
        $storage = [];    
        foreach($add as $name => $content) {
            $storage[] = $Html->createMeta($this->dec($name), $this->dec($content));
        }
        
        return $this->dec($meta).PHP_EOL.implode('', $storage);
    }
    /**
     *    @description    
     */
    protected function allowedAsset($type, $func)
    {
        if(!is_callable($func)) {
            trigger_error(__FUNCTION__.'($type, $func) requires $func to be a callable function.', E_USER_NOTICE);
            return false;
        }
        
        $data = (!empty($this->getDataNode('templates')['config'][$type]['include']))? $this->getDataNode('templates')['config'][$type]['include'] : false;
        
        if(empty($data))
            return false;
        
        foreach($data as $include) {
            $allow = false;
            $attr = (!empty($include['@attributes']))? $include['@attributes'] : false;
            $is_local = (!empty($attr['is_local']) && $attr['is_local'] == 'true');
            $is_admin = (!empty($attr['is_admin']) && $attr['is_admin'] == 'true');
            $is_frontend = (!empty($attr['is_frontend']) && $attr['is_frontend'] == 'true');
            $is_backend = (!empty($attr['is_backend']) && $attr['is_backend'] == 'true');
            $page_id = (!empty($attr['page_id']) && $attr['page_id'] == $this->getPage('ID'));
            $page_path = (!empty($attr['page_path']) && (strtolower($attr['page_path']) == strtolower($this->getPage('full_path'))));
            $is_loggedin = (!empty($attr['logged_in']) && $attr['logged_in'] == 'true');
            $get_key = (!empty($attr['get_key']) && isset($this->getDataNode('_GET')[$attr['get_key']]));
            $post_key = (!empty($attr['post_key']) && isset($this->getDataNode('_POST')[$attr['post_key']]));
            $path = str_replace(str_replace(DS,'/', NBR_DOMAIN_ROOT),'', $include['path']);
            
            if(empty($attr))
                $allow = true;
            else {
                if($this->isFrontEnd() && $is_frontend)
                    $allow = true;
                
                if($this->isBackEnd() && $is_backend)
                    $allow = true;
                
                if(empty($is_frontend) && empty($is_backend)) {
                    $allow = (!isset($attr['page_id']) && !isset($attr['page_path']) && !isset($attr['is_admin']) && !isset($attr['get_key']) && !isset($attr['post_key']));
                }
                
                if($page_id || $page_path)
                    $allow = true;
                
                if($is_loggedin && !$this->isLoggedIn())
                    $allow = false;
                
                if($is_admin){
                    # ONLY MODE – No back end, no front end, only editor view
                    $allow = ((empty($is_frontend) && empty($is_backend)) && !$this->isAdmin())? false : true;
                }
                
                if($get_key) {
                    if(isset($attr['get_value'])) {
                        $allow = ($attr['get_value'] == $this->getGet($attr['get_key']));
                    }
                }
                
                if($post_key) {
                    if(isset($attr['post_value'])) {
                        $allow = ($attr['post_value'] == $this->getPost($attr['post_key']));
                    }
                }
            }
            
            
            if($allow)
                $storage[] = $func($this->Helpers->Html, $path, $is_local);
        }
        
        return (!empty($storage))? implode('', $storage) : false;
    }
    /**
     *    @description    
     */
    public function headerJavaScript()
    {
        $html = $this->dec($this->getSitePreferences('header_javascript'));
        return    (!empty($html))? '<script>'.PHP_EOL.$html.PHP_EOL.'</script>'.PHP_EOL : false;
    }
    
    public function headerStyleSheets()
    {
        $html = $this->dec($this->getSitePreferences('header_styles'));
        return    (!empty($html))? '<style>'.PHP_EOL.$html.PHP_EOL.'</style>'.PHP_EOL : false;
    }
    /**
     *    @description    
     */
    public function javaScript()
    {
        return $this->allowedAsset('javascript', function($Html, $path, $is_local){            
            return $Html->createScript($path, $is_local);
        });
    }
    /**
     *    @description    
     */
    public function styleSheets()
    {
        return $this->allowedAsset('stylesheet', function($Html, $path, $is_local){            
            return $Html->createLinkRel($path, $is_local);
        });
    }
    /**
     *    @description    
     */
    public function getMastHead()
    {
        $html = $this->dec($this->getSitePreferences('header_html'));
        if(empty($html))
            return false;
        
        return ($this->getSitePreferences('header_html_toggle') == 'on')? $html : '';
    }
    /**
     *    @description    
     */
    public function isFrontEnd()
    {
        $route     = $this->getDataNode('routing');
        if(!isset($route['is_admin']))
            return true;
        $page_type = (!empty($this->getDataNode('routing')['is_admin']))? $this->getDataNode('routing')['is_admin'] : false;
        
        return ($page_type !== 1);
    }
    /**
     *    @description    
     */
    public function isBackEnd()
    {
        return (empty($this->isFrontEnd()));
    }
    /**
     *    @description    
     */
    public function getPage($key = false)
    {
        if(empty($this->routing->ID))
            return null;
        
        if($key)
            return $this->routing->{$key}?? null;
        
        return $this->routing;
    }
    /**
     *    @description    
     */
    public function getTemplateFile(string $file = 'index.php', string $type = 'frontend', bool $path = false)
    {
        if(!is_array($this->getDataNode('templates')['paths']))
            return false;
        
        foreach($this->getDataNode('templates')['paths'] as $dir) {
            if(is_file($inc = $this->toSingleDs($dir.DS.$type.DS.$file))) {
                $s = (!$path)? $this->renderBeforeHint($inc) : false;
                    
                return ($path)? $inc : $s.parent::render($inc);
            }
        }
        
        return false;
    }
    /**
     *    @description    
     */
    public function getFrontEndFrom($file = 'index.php', $path)
    {
        $path = NBR_CLIENT_TEMPLATES.DS.$path.DS.'frontend'.DS.$file;
        $s = $this->renderBeforeHint($path);
        return $s.parent::render($path);
    }
    /**
     *    @description    
     */
    public function getBackEndFrom($file = 'index.php', $path)
    {
        $path = NBR_CLIENT_TEMPLATES.DS.$path.DS.'backend'.DS.$file;
        $s = $this->renderBeforeHint($path);
        return $s.parent::render($path);
    }
    /**
     *    @description    
     */
    public function getFrontEnd($file = 'index.php', $path = false)
    {
        return $this->getTemplateFile($file, 'frontend', $path);
    }
    /**
     *    @description    
     */
    public function getBackEnd($file = 'index.php', $path = false)
    {
        return $this->getTemplateFile($file, 'backend', $path);
    }
    /**
     *    @description    
     */
    public function getSitePreferences($key = false)
    {
        $prefs = $this->Helpers->Settings->getSettingContent('system');
        
        if(!empty($key)) {
            if(!is_array($prefs))
                return false;
            
            $val = array_values(array_filter(array_map(function($v) use ($key){
                if($v['category_id'] != $key)
                    return false;
                else
                    return $v['option_attribute'];
                    
            }, $prefs)));
            
            return (!empty($val[0]))? $val[0] : false;
        }
        return $prefs;
    }
    /**
     *    @description    
     */
    public function signUpAllowed()
    {
        return ($this->getSitePreferences('sign_up') == 'on');
    }
    /**
     *    @description    
     */
    public function user($key = false)
    {
        $SESSION = $this->getDataNode('_SESSION');
        
        if(empty($SESSION))
            return false;
        
        $user = (!empty($SESSION['user']))? $SESSION['user'] : false;
        
        if(!empty($key))
            return (!empty($user[$key]))? $user[$key] : false;
        
        return $user;
    }
    /**
     *    @description    
     */
    public function useAuth2()
    {
        $auth2     = false;
        $authType = $this->getSystemOption('two_factor_auth');
        if($authType == 'off')
            return $auth2;
        $adminPg = ($this->getPage('is_admin') == 1);

        if($authType == 'both')
            $auth2 = true;
        elseif($authType == 'admin' && $adminPg)
            $auth2 = true;
        elseif($authType == 'frontend' && !$adminPg)
            $auth2 = true;
        
        return $auth2;
    }
	/**
	 *	@description	
	 */
	public function getPageByType(string $name, $key = false)
	{
        $page = $this->query("SELECT ".((!empty($key))? "`{$key}`" : '*')." FROM main_menus WHERE page_type = ?", [$name])->getResults(1);
        
        if(empty($page))
            return null;
        
        if(!empty($key))
            return ($page[$key])?? null;
        
        return $page['full_path'];
	}
	/**
	 *	@description	
	 */
	public function renderBeforeHint($file)
	{
        if(($this->getSystemOption('fileid') != 'on'))
            return false;
        
        $dev = ($this->getSystemOption('devmode') == 'dev');
        
        if(!$this->isAjaxRequest() && $this->getPage('is_admin') != 1 && $dev)
            return '<div class="file-id-backtrace">'.$file.'</div>';
        
	}
}