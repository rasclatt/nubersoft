<?php
namespace Nubersoft\Dto\Tables;

class SystemSettings extends \Nubersoft\Dto\Tables
{
    public string $category_id = '';
    public string $option_group_name = '';
    public string $option_attribute = '';
    public string $action_slug = '';
    public string $page_live = '';
    public int $page_order = 0;
    public int $usergroup = 0;
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        if (!empty($array['ID'])) {
            if (!is_numeric($array['ID']))
                throw new \Exception('Invalid request.', 403);
        }

        $array['timestamp'] = date('Y-m-d H:i:s');
        $array['page_order'] = (is_numeric($array['page_order'])) ? (int) $array['page_order'] : 0;
        $array['usergroup'] = (is_numeric($array['usergroup'])) ? (int) $array['usergroup'] : 1000;

        $this->table = 'system_settings';
        return $array;
    }
}
