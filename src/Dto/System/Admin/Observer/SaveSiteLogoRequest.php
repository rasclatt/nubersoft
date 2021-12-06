<?php
namespace Nubersoft\Dto\System\Admin\Observer;

use \Nubersoft\nApp;

class SaveSiteLogoRequest extends \SmartDto\Dto
{
    // public $jwtToken = '';
    // public $token = '';
    //public $action = '';
    public $category_id = '';
    public $option_group_name = '';
    public $setting = '';
    public $delete = '';
    public $allowed_mimes = [
        'image/jpeg',
        'image/png',
        'image/gif'
    ];
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        return (nApp::call()->getPost());
    }
    /**
     *	@description	
     *	@param	
     */
    public function setting()
    {
        $this->setting = new SaveSiteLogoRequestSetting($this->setting);
    }
}

class SaveSiteLogoRequestSetting extends \SmartDto\Dto
{
    public $header_company_logo_toggle = 'off';
}