<?php
namespace Nubersoft\Dto\Tables;

use \Nubersoft\Dto\File\Table;
use \Nubersoft\Dto\Helper\FolderWorks\IsDirRequest;
use \Nubersoft\ErrorMessaging;
use \Nubersoft\Helper\ {
    File,
    FolderWorks
};

class Media extends \Nubersoft\Dto\Tables
{
    public $file = '';
    public $file_path = '';
    public $file_name = '';
    public $usergroup = '';
    public $username = '';
    public $content = '';
    public $login_view = '';
    public $page_live = 'off';
    public ?int $file_size = 0;
    public ?int $terms_id = 0;
    public ?int $page_order = 1;
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        $this->table = 'media';
        $array = parent::beforeConstruct($array);
        $file = File::get();
        if(empty($file->tmp_name) || (!empty($file->tmp_name) && !$this->saveFile($file)))
            throw new \Exception((new ErrorMessaging)->getMessageAuto('fail_upload'), 500);
        return array_merge($array, $file->toArray());
    }
    /**
     *	@description	
     *	@param	
     */
    private function saveFile(Table $file)
    {
        $destination = str_replace(DS . DS, DS, NBR_DOMAIN_ROOT . DS . $file->file_path . DS . $file->file_name);
        $dto = new IsDirRequest();
        $dto->dir = pathinfo($destination, PATHINFO_DIRNAME);
        if(FolderWorks::isDir($dto))
            return move_uploaded_file($file->tmp_name, $destination);
        return false;
    }
}