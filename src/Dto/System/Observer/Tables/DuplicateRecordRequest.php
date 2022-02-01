<?php
namespace Nubersoft\Dto\System\Observer\Tables;

class DuplicateRecordRequest extends \SmartDto\Dto
{
    public int $ref_page = 0;
    public int $parent_dup = 0;
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        $array['ref_page'] = (int) $array['ref_page']?? 0;
        $array['parent_dup'] = (int) $array['parent_dup']?? 0;
        return $array;
    }
}