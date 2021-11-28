<?php
namespace Nubersoft\Settings;

class Admin extends \Nubersoft\Settings
{
    public function getAdminPage($key = 'full_path')
    {
        $page = $this->select()
            ->from('main_menus')
            ->where([
                ['c' => 'is_admin', 'v' => '1']
            ])
            ->fetch(1);

        if ($key)
            return (!empty($page[$key])) ? $page[$key] : false;

        return $page;
    }

    public function isAdminPage()
    {
        $admin = (!empty($this->getDataNode('routing')['is_admin'])) ? $this->getDataNode('routing')['is_admin'] : false;

        if (empty($admin))
            return false;

        return ($this->getDataNode('routing')['is_admin'] == 1);
    }
    /**
     *	@description	
     */
    public function authComponent(array $component)
    {
        if (isset($component[0]['ID']))
            $component = $component[0];

        $usergroup = ($component['usergroup']) ?? false;

        if ($usergroup && !is_numeric($usergroup))
            $usergroup = constant($usergroup);

        if ($usergroup) {
            if (!$this->isLoggedIn()) {
                return false;
            }

            if ($this->userGet('usergroup') > $usergroup)
                return false;
        }

        return true;
    }
}
