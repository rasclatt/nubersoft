<?php
namespace Nubersoft\Settings;

class Model extends \Nubersoft\Settings
{
    public function deletePage($ID)
    {
        $this->query("DELETE FROM `main_menus` WHERE `ID` = ?", [$ID]);
        return ($this->query("SELECT COUNT(*) as count FROM `main_menus` WHERE `ID` = ?", [$ID])->getResults(1)['count'] == 0);
    }

    public function getPage($key = false)
    {
        # Get data node
        $data = $this->getDataNode('routing');

        if ($key)
            return (isset($data[$key])) ? $data[$key] : null;

        return $data;
    }

    public function getSettingContent($type, $key = false, $default = false)
    {
        # Get data node
        $data = $this->getDataNode();

        if (!empty($data['settings'][$type]))
            $data['settings'][$type] = \Nubersoft\ArrayWorks::organizeByKey($data['settings'][$type], 'category_id', ['unset' => false]);

        # Fetch main
        $core = (!empty($data['settings'][$type])) ? $data['settings'][$type] : $default;
        # Send back empty
        if (empty($key))
            return $core;
        # Fetch the htaccess portion
        if ($key) {
            $data = (!empty($core[$key])) ? $this->dec($core[$key]['option_attribute']) : $default;
            $default = (!is_array($data)) ? $this->dec($data) : $data;
        }
        return $default;
    }

    public function getMenu($id = null, string $column = 'ID', $useDto = false)
    {
        $sql = (!empty($id)) ? " WHERE {$column} = ?" : '';
        $menu = $this->query("SELECT * FROM main_menus{$sql} ORDER BY page_order ASC", (!empty($id) ? [$id] : null))->getResults((!empty($id) && $column == 'ID'));

        if (!empty($menu)) {
            return ($useDto) ? array_map(function ($v) {
                return new \Nubersoft\Dto\Menu($v);
            }, $menu) : $menu;
        }

        return [];
    }
}
