<?php
namespace Nubersoft\Dto;

class Tables extends \SmartDto\Dto
{
    public ?int $ID;
    public ?int $unique_id;
    public ?string $delete;

    protected string $table = '';
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        if(empty($array['unique_id']))
            $array['unique_id'] = (int) \Nubersoft\nApp::call()->fetchUniqueId();
        else 
            $array['unique_id'] = (int) $array['unique_id'];

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