<?php
namespace Nubersoft\Dto\Tables;

class UserRoles extends \Nubersoft\Dto\Tables
{
    public string $user_role = '';
    public string $user_attribute = '';
    public int $user_id = 0;
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        if(!empty($array['ID'])) {
            if(!is_numeric($array['ID']))
                throw new \Exception('Invalid request.', 403);
        }

        $array['timestamp'] = date('Y-m-d H:i:s');
        
        $this->table = 'user_roles';
        return $array;
    }
}