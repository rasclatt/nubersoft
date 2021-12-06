<?php
namespace Nubersoft\Dto;

class Tables extends \SmartDto\Dto
{
    protected string $table = '';
    public $ID = null;
    public ?int $unique_id = null;
    public ?string $delete = null;
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        if(empty($array['unique_id']))
            $array['unique_id'] = (int) \Nubersoft\nApp::call()->fetchUniqueId();

        if(!empty($array['ID'])) {
            if(!is_numeric($array['ID']))
                throw new \Exception('Invalid request.', 403);
        }
        return $array;
    }
    /**
     *	@description	
     *	@param	
     */
    public function getTable()
    {
        return $this->table;
    }
}