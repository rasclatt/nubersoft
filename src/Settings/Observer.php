<?php
namespace Nubersoft\Settings;

use \Nubersoft\ {
    nObserver,
    JWTFactory as JWT,
    nSession,
    nRouter\Controller as nRender,
    DataNode,
    nToken
};

class Observer extends Controller implements nObserver
{
    private $Session, $JWT, $nRender, $DataNode, $nToken;
	/**
	 *	@description	
	 */
	public function __construct(
        nSession $Session,
        nRender $nRender,
        DataNode $DataNode,
        nToken $nToken
    )
	{
        $this->nRender = $nRender;
        $this->Session  =   $Session;
        $this->JWT  = JWT::get();
        $this->DataNode = $DataNode;
        $this->nToken = $nToken;
	}
	/**
	 *	@description	
	 */
	public function createJWT()
	{
        $this->Session->set('jwtToken', $this->JWT->create([
            "user" => (!empty($this->userGet('ID')))? $this->userGet('ID') : 'anon',
            "action" => $this->getRequest('action')
        ]));
        
        return $this;
	}
    
    public function listen()
    {
        $Router = $this->nRender;
        $DataNode = $this->DataNode;
        $Token = $this->nToken;
        $SERVER = $this->getServer();
        
        if(!isset($SERVER['SCRIPT_URL']) && !isset($SERVER['REQUEST_URI']))
            $scrurl = '/';
        else {
            $scrurl = (isset($SERVER['SCRIPT_URL']))? $SERVER['SCRIPT_URL'] : parse_url($SERVER['REQUEST_URI'])['path'];
        }
        
        $path = (strpos(strtolower($scrurl), '.') !== false)? str_replace('//', '/', '/'.implode('/',array_filter(array_map(function($v){
            return (strpos(strtolower($v), '.') !== false)? false : $v;
        },explode('/',$scrurl)))).'/') : $scrurl;
        
        $query = (empty($path) || $path == '/')? $Router->getPage('2', 'is_admin') : $Router->getPage($path);
        $settings = $this->getSettings(false, 'system');
        $DataNode->setNode('cache_folder', (!defined('CLIENT_CACHE'))? NBR_CLIENT_CACHE : CLIENT_CACHE);
        $DataNode->setNode('routing', $query);
        $DataNode->setNode('settings', ['system' => $settings]);
        # Fetch any preferences that have the action
        if(!empty($this->getRequest('action'))) {
            $actions = $this->getSettingsByAction($this->getRequest('action'));
            if(!empty($actions)) {
                $DataNode->addNode('settings', $actions, 'actions');
            }
        }
        # Check for a rewrite file
        $this->setReWrite($this->getReWrite());
        # Save default paths for templating
        $DataNode->setNode('templates', ['paths' => $this->getTemplatePaths()]);
        # Save default paths for plugins
        $DataNode->setNode('plugins', ['paths' => $this->getPluginPaths()]);
        # Create a 404 if routing is missing
        $DataNode->setNode('header', [
            'header_response_code' => (empty($this->getDataNode('routing')->ID))? 404 : 200
        ]);
        # Checks if request is likely ajax in nature
        $DataNode->setNode('request', (($Router->isAjaxRequest())? 'ajax' : 'http'), 'type');
        # Creates a page token
        if(!$Token->tokenExists('page'))
            $Token->setToken('page');
        # Set the status of the site (on or off)
        $DataNode->setNode('site_status', $this->getSiteStatus());
        return $this;
    }
    
    public function checkUserSettings()
    {
        $registry = NBR_CLIENT_SETTINGS.DS.'registry.xml';
        
        if(is_file($definc = $this->getClientDefines())) {
            @include_once($definc);
            return $this;
        }
        
        if(!is_file($this->getClientDefines())) {
            if(!is_file($registry))
                throw new \Nubersoft\HttpException('Registry file to create important settings is missing. Reinstall required.', 100);
            
            if($this->createDefines($registry)) {
                $msg = ($this->isAdmin())? "?msg=".urlencode('Cache has been removed.') : '';
                $this->nRender->redirect($this->getPage('full_path'));
            }
            
        }
        
        return $this;
    }
    
    public function setReWrite($content = false)
    {
        $system    =    (defined('NBR_SERVER_TYPE'))? NBR_SERVER_TYPE : 'linux';
        $file    =    NBR_DOMAIN_ROOT.DS.(($system == 'linux')? '.htaccess' : 'web.config');
        
        if(is_file($file))
            return false;
        
        if(empty($content)) {
            $content    =    file_get_contents(NBR_SETTINGS.DS.'rewrite'.DS.$system.DS.'root.txt');
        }
        
        file_put_contents($file, $content);
        
        return is_file($file);
    }
    
    public function setTimezone()
    { 
        date_default_timezone_set($this->getTimezone());
        return $this;
    }
    /**
     *    @description    
     */
    public function formatFileUploads()
    {
        $FILES = $this->getDataNode('_FILES');
        
        if(empty($FILES))
            return $this;
        
        $this->isDir(NBR_DOMAIN_CLIENT_DIR.DS.'media'.DS.'images', true);
        
        $files = [];
        $Conversion = $this->getHelper('Conversion\Data');
        
        if(!isset($FILES['file'])) {
            $this->assembleStandardFiles($FILES, false, $Conversion);
            return $this;
        }
        
        foreach($FILES as $keyname => $rows) {
            foreach($rows as $key => $value) {
                foreach($value as $i => $val) {
                    # Stop if there is nothing uploaded
                    if($key == 'name' && empty($val)) {
                        continue;
                    }
                    $files[$i][$key]    =    $val;
                    if($key == 'size') {
                        $files[$i]['size_attr'] = [
                            'kb' => round($Conversion->getByteSize((int) $val,['from' => 'b', 'to'=>'kb']), 2),
                            'mb' => round($Conversion->getByteSize((int) $val,['from' => 'b', 'to'=>'mb']), 2),
                            'gb' => round($Conversion->getByteSize((int) $val,['from' => 'b', 'to'=>'gb']), 2)
                        ];
                    }
                    elseif($key == 'name') {
                        $files[$i]['file_key_name'] = $keyname;
                        $files[$i]['name_date'] = date('YmdHis').'.'.strtolower(pathinfo($val, PATHINFO_EXTENSION));
                        $files[$i]['path_default'] = DS.'client'.DS.'media'.DS.'images'.DS.$val;
                        $files[$i]['path_alt'] = DS.'client'.DS.'media'.DS.'images'.DS.$files[$i]['name_date'];
                    }
                }
            }
        }
        $this->setNode('_FILES', $files);
        
        return $this;
    }
	/**
	 *	@description	
	 */
	private	function assembleStandardFiles($FILES, $keyname, $Conversion)
	{
        $files = [];
        foreach($FILES as $i => $file) {
            foreach($file as $key => $val) {
                # Stop if there is nothing uploaded
                if($key == 'name' && empty($val)) {
                    continue;
                }
                $files[$i][$key]    =    $val;
                if($key == 'size') {
                    $files[$i]['size_attr'] = [
                        'kb' => round($Conversion->getByteSize((int) $val,['from' => 'b', 'to'=>'kb']), 2),
                        'mb' => round($Conversion->getByteSize((int) $val,['from' => 'b', 'to'=>'mb']), 2),
                        'gb' => round($Conversion->getByteSize((int) $val,['from' => 'b', 'to'=>'gb']), 2)
                    ];
                }
                elseif($key == 'name') {
                    $files[$i]['file_key_name']    =    $keyname;
                    $files[$i]['name_date']     =    date('YmdHis').'.'.strtolower(pathinfo($val, PATHINFO_EXTENSION));
                    $files[$i]['path_default']    =    DS.'client'.DS.'media'.DS.'images'.DS.$val;
                    $files[$i]['path_alt']        =    DS.'client'.DS.'media'.DS.'images'.DS.$files[$i]['name_date'];
                }
            }
        }
        $this->setNode('_FILES', $files);
	}
}