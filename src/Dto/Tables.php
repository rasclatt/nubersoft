<?php
namespace Nubersoft\Dto;

class Tables extends \SmartDto\Dto
{
    public ?int $ID;
    public $unique_id = null;
    public ?string $delete;

    protected string $table = '';
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        if(empty($array['unique_id']))
            $array['unique_id'] = \Nubersoft\nApp::call()->fetchUniqueId();

        if(!empty($array['ID'])) {
            if(!is_numeric($array['ID']))
                throw new \Exception('Invalid request.', 403);
        }

        $array['timestamp'] = date('Y-m-d H:i:s');

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