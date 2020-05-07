<?php
namespace Nubersoft;
/**
 *    @description    
 */
class ErrorMessaging extends \Nubersoft\nApp
{
    use \Nubersoft\Settings\enMasse;
    
    const   DEFAULT_CODES   =   [
        200 => 'OK',
        404 => 'Page does not exist',
        403 => 'Permission denied',
        500 => 'An unknown error occurred',
        '403_delete' => 'Permission denied to delete',
        'access_admin' => 'This is only accessable by admin or on the admin page',
        'success' => 'Action was successful',
        'success_sql' => 'SQL performed successfully',
        'success_login' => 'You have successfully signed in',
        'success_logout' => 'You have successfully signed out',
        'success_create' => 'Item created successfully',
        'success_delete' => 'Item deleted successfully',
        'success_update' => 'Item updated successfully',
        'success_saved' => 'Successfully saved',
        'success_email' => 'Email sent successfully',
        'success_thumbremoved' => "Thumbnail removed",
        'success_upload' => "File uploaded successfully",
        'success_cachedeleted' => 'Cache was successfully deleted',
        'success_plugininactive' => 'Plugin is inactive',
        'success_pluginactive' => 'Plugin is activated',
        'success_settingssaved' => 'Settings were saved',
        'success_usercreate' => 'User successfully created',
        'success_componentcreate' => 'Component created successfully',
        'fail_saved' => 'Saving was unsuccessful',
        'fail_thumbremoved' => "Thumbnail failed to be removed.",
        'fail_email' => 'Email failed to send',
        'fail' => 'Action failed',
        'fail_sql' => 'SQL failed on execution',
        'fail_login' => 'Invalid Username or Password',
        'fail_logout' => 'You have unsuccessfully signed out. Try again',
        'fail_create' => 'Item failed to created',
        'fail_delete' => 'Item failed to delete',
        'fail_update' => 'Item failed to update',
        'fail_userexists' => 'User already exists',
        'fail_usercreate' => 'An error occurred trying to create user',
        'fail_upload' => 'File failed to upload - check file/folder permissions or file size limit',
        'fail_exists' => 'Item does not exist',
        'fail_widget' => 'Can not activate Widget. Missing widget name.',
        'fail_connection' => 'Connection failed',
        'required' => 'Required fields can not be empty',
        'required_filetype' => 'File must me valid type',
        'account_disabled' => 'Your account is disabled',
        'account_saved' => 'User account saved',
        'account_savedfail' => 'User account failed to save',
        'cart_added' => 'Added to cart',
        'cart_failadded' => 'Product failed to add to cart',
        'cart_nothing' => 'Nothing to add to cart',
        'invalid_token' => 'Security token is invalid',
        'invalid_tokenmatch' => 'Security token does not match',
        'invalid_request' => 'Invalid request',
        'invalid_user' => 'Username or password is invalid',
        'invalid_username' => 'Username is invalid',
        'invalid_file' => 'File is invalid',
        'invalid_download' => 'Download is not available',
        'invalid_code' => 'Code is invalid',
        'invalid_page' => 'Page does not exist',
        'invalid_slug' => 'The slug is invalid',
        'invalid_slugexists' => 'The slug already exists',
        'invalid_component' => 'Component does not exist',
        'no_action' => 'No action was taken',
        'ajax_invalid' => "No actions to take, you may have been logged out",
        'site_comingsoon' => "Site coming soon",
        'site_maintenance' => "Site is being worked on"
    ];
    /**
     *    @description    
     */
    public    static    function getMessage($code, $locale = 'us', $lang = 'en')
    {
        $defcode    =   500;
        $def        =   self::DEFAULT_CODES[500];
        $local      =   $locale.$lang;
        
        if(empty($local))
            $local  =   \Nubersoft\nApp::create()->getSession('locale').\Nubersoft\nApp::create()->getSession('locale_lang');
        
        if(empty($local))
            $local  =   'usen';
        
        $code    =    (new ErrorMessaging())->getComponentBy([
            'category_id' => 'translator',
            'component_type' => 'status_code',
            'title' => $code.$local
        ]);
        
        if(empty($code) && $local != 'usen') {
            $code    =    (new ErrorMessaging())->getComponentBy([
                'category_id' => 'translator',
                'component_type' => 'status_code',
                'title' => $code.'usen'
            ]);
        }
        
        $msg    =   (empty($code))? $def : $code[0]['content'];
        
        return (empty($msg))? $def : $msg;
    }
	/**
	 *	@description	
	 */
	public	function createCode($code, $message, $locale = 'us', $lang = 'en')
	{
        $exists =   $this->getComponentBy([
            'category_id' => 'translator',
            'component_type' => 'status_code',
            'title' => $code.$locale.$lang
        ]);
        
        if($exists) {
            $this->deleteComponent($exists[0]['ID']);
        }
        
        $this->addComponent([
            'category_id' => 'translator',
            'component_type' => 'status_code',
            'title' => $code.$locale.$lang,
            'content' => $message
        ]);
        
        return  $this->getComponentBy([
            'category_id' => 'translator',
            'component_type' => 'status_code',
            'title' => $code.$locale.$lang
        ]);
    }
	/**
	 *	@description	
	 */
	public	function getMessageAuto($keycode)
	{
        return self::getMessage($keycode, $this->getSession('locale'), $this->getSession('locale_lang'));
	}
}