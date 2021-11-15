<?php
namespace Nubersoft\System\Observer;

use \Nubersoft\{
    nQuery\enMasse as nQueryEnMasse,
    nDynamics,
    nRouter\Controller as Router,
    nToken,
    Settings\Controller as Settings,
    DataNode
};

class Tables extends \Nubersoft\System\Observer
{
    use nQueryEnMasse, nDynamics;

    protected $Router, $Token, $Settings, $DataNode;

    public function __construct(
        Router $Router,
        nToken $Token,
        Settings $Settings,
        DataNode $DataNode
    ) {
        $this->Router = $Router;
        $this->Token = $Token;
        $this->Settings = $Settings;
        $this->DataNode =   $DataNode;
    }

    public function listen()
    {
        if (!$this->isAdmin()) {
            $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto(403));
            return false;
        }

        $Router  = $this->Router;
        $Token  = $this->Token;
        $DataNode = $this->DataNode;
        $POST  = (!empty($this->getPost('ID'))) ? $this->getPost() : array_filter($this->getPost());
        $action  = (!empty($POST['action'])) ? $POST['action'] : false;
        $token  = (!empty($this->getPost('token')['nProcessor'])) ? $this->getPost('token')['nProcessor'] : false;
        $ctoken  = (!empty($this->getPost('token')['component'][$this->getPost('ID')])) ? $this->getPost('token')['component'][$this->getPost('ID')] : false;
        # Remove action k/v
        if (isset($POST['action']))
            unset($POST['action']);
        # Remove the token k/v
        if (isset($POST['token']))
            unset($POST['token']);

        switch ($action) {
            case ('edit_table_rows_details'):
                $this->editTable($POST, $this->getRequest('table'), $token, $Token);
                return $this;
            case ('edit_user_details'):
                $this->updateUserData($POST, $token, $Token, $this->getRequest('table'), $this->getRequest('ID'));
                return $this;
            case ('edit_component'):
                $timestamp = date('Y-m-d H:i:s');
                if (!empty($POST['subaction'])) {
                    $action = $POST['subaction'];
                    unset($POST['subaction']);

                    switch ($action) {
                        case ('add_new'):
                            $this->addNewRecord($POST);
                            break;
                        case ('duplicate'):
                            $this->duplicateRecord($POST);
                    }
                } else {
                    if ($this->getPost('delete') == 'on') {
                        $this->deleteRecord($POST['ID']);
                        $msg =   "Refresh the page to see update.";
                    } else {
                        $this->updateRecord($POST, $token, $Token);
                    }
                }
                if (!isset($msg))
                    $msg =   'Updated';

                if ($this->isAjaxRequest()) {
                    $this->ajaxResponse(['alert' => $msg, 'msg' => $this->getSystemMessages()]);
                }
                break;
            case ('update_admin_url'):
                $this->updatePage($token, $Token, $POST);
                break;
            case ('create_new_page'):
                # Match token for all
                if (empty($token) || !$Token->match('page', $token, false, false)) {
                    $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('invalid_tokenmatch'));
                    return false;
                }

                if (empty($POST['full_path'])) {
                    $dpath = implode('/', [date('Y'), date('m'), date('d'), date('s')]);
                    $path = ($this->getPage('is_admin') != 1) ? $this->getPage('full_path') . $dpath : '/' . $dpath;
                } else
                    $path = $this->convertToStandardPath($POST['full_path']);

                @$this->nQuery()->insert("main_menus")
                    ->columns(['unique_id', 'full_path', 'page_live', 'menu_name', 'link'])
                    ->values([[$this->fetchUniqueId(), '/' . trim($path, '/') . '/', 'off', 'Untitled', 'untitled' . date('YmdHis')]])
                    ->write();
                $this->Router->redirect($this->localeUrl($path));
                break;
            case ('update_page'):
                $this->updatePage($token, $Token, $POST);
        }

        return $this;
    }

    protected function updatePage($token, $Token, $POST, $allow_delete = true)
    {
        # Match token for all
        if (empty($token) || !$Token->match('page', $token, false, false)) {
            $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('invalid_tokenmatch'));
            return false;
        }

        $ID = $this->getPost('ID');

        if (empty($ID)) {
            $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('invalid_page'));
            return false;
        }

        if (!empty($POST['delete'])) {
            if ($allow_delete) {
                if ($this->Settings->deletePage($ID)) {
                    $this->Router->redirect('/?msg=success_delete');
                }
                return true;
            } else {
                $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('403_delete'));
                return false;
            }
        }

        $POST['full_path'] = $this->Router->convertToStandardPath($POST['full_path']);
        $existing = $this->Router->getPage($POST['full_path'], 'full_path');
        if ($existing instanceof \SmartDto\Dto)
            $existing = $existing->toArray();

        if (empty($POST['full_path'])) {
            $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('invalid_slug'));
            return false;
        }

        if (!empty($existing['full_path'])) {
            if ($existing['ID'] != $POST['ID']) {
                $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('invalid_slugexists'));
                return false;
            }
        }
        # Remove the ID for the update
        unset($POST['ID']);

        if ($POST['full_path'] == '/') {
            $POST['is_admin']   =   2;
            $POST['link']   =   'home';
        } else {
            $POST['link'] = strtolower(pathinfo($POST['full_path'], PATHINFO_BASENAME));
        }
        # Remove unavailable keys
        $this->filterUnMatched($POST,  'main_menus');

        $sql = [];
        foreach ($POST as $key => $value) {
            $sql[] = "`{$key}` = ?";
        }
        $this->query("UPDATE `main_menus` SET " . implode(', ', $sql) . " WHERE ID = ? ", array_values(array_merge(array_values($POST), [$ID])));

        if ($this->getPage()->full_path != $POST['full_path']) {
            $this->Router->redirect($POST['full_path'] . '?msg=fail_update');
        } else {
            $this->Router->redirect($POST['full_path'] . '?msg=success_settingssaved&' . http_build_query($this->getGet()));
        }
    }

    /**
     *	@description	
     */
    public function filterUnMatched(&$array, $table)
    {
        $cols   =   [];
        # Get the fields from database
        \Nubersoft\ArrayWorks::extractAll(array_map(function ($v) {
            return $v['Field'];
        }, $this->query("describe {$table}")->getResults()), $cols);
        # Loop through array and remove invalid fields from array
        foreach ($array as $key => $value) {
            if (!in_array($key, $cols))
                unset($array[$key]);
        }
    }

    protected function updateData($POST, $table, $msg, $err = false)
    {
        $table = preg_replace('/[^0-9A-Z\_\.\`\-]/i', '', $table);
        $ID  = (is_numeric($POST['ID'])) ? $POST['ID'] : false;

        if (empty($POST) || empty($ID)) {
            $this->toError((!empty($err)) ? $err : $this->getHelper('ErrorMessaging')->getMessageAuto('no_action'));
            return false;
        }
        # Process file and the fields associated with the file
        $this->setFileData($POST, $ID);
        # Remove any fields that don't exist in the table
        $this->filterUnMatched($POST, $table);
        # Create array
        foreach ($POST as $keys => $values) {
            $bind[] = $values;
            $sql[] = "`{$keys}` = ?";
        }

        $this->query("UPDATE `{$table}` SET " . implode(', ', $sql) . " WHERE ID = '{$ID}'", $bind);

        $this->toSuccess($msg);
        return true;
    }

    protected function getCurrentFilePath($ID, $table = 'components')
    {
        try {
            $path = $this->query("SELECT CONCAT(`file_path`, `file_name`) as `image_url` FROM {$table} WHERE ID = ?", [$ID])->getResults(1);

            return (!empty($path['image_url'])) ? $path['image_url'] : false;
        } catch (\PDOException $e) {
            return false;
        }
    }

    protected function removeCurrentFilePath($ID, $table = 'components')
    {
        $file = $this->getCurrentFilePath($ID, $table);
        if (!empty($file)) {
            if (is_file($img = NBR_DOMAIN_ROOT . $file)) {
                unlink($img);
                $this->query("UPDATE {$table} SET `file_name` = '', `file_path` = '', `file_size` = '' WHERE ID = ?", [$ID]);
            }
        }
    }

    protected function updateUserData($POST, $token, $Token, $table, $ID)
    {
        if (empty($token) || !$Token->match('page', $token)) {
            $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('invalid_request'));
            return false;
        }
        if ($this->getPost('delete') == 'on') {
            $this->query("DELETE FROM `" . str_replace('`', '', $table) . "` WHERE ID = ?", [$ID]);

            if (empty($this->getHelper('nUser')->getUser($ID, 'ID'))) {
                $this->Router->redirect($this->localeUrl(\Nubersoft\nReflect::instantiate('\Nubersoft\nRender')->getPage('full_path') . '?table=users&msg=success_delete'));
            } else {
                $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto(500));
                return $this;
            }
        } else {
            if (!empty($POST['ID']) && is_numeric($POST['ID'])) {
                $required = array_filter([
                    (isset($POST['username']) && filter_var($POST['username'], FILTER_VALIDATE_EMAIL)) ? $POST['username'] : false,
                    (isset($POST['email']) && filter_var($POST['email'], FILTER_VALIDATE_EMAIL)) ? $POST['email'] : false,
                    ($POST['first_name']) ?? $POST['first_name'],
                    ($POST['last_name']) ?? $POST['last_name'],
                    ($POST['usergroup']) ?? $POST['usergroup'],
                    ($POST['user_status']) ?? $POST['user_status']
                ]);

                if (!empty($POST['password'])) {
                    $POST['password'] = $this->getHelper('nUser')->hashPassword($this->getPost('password', false));
                } else {
                    unset($POST['password']);
                }

                if (count($required) < 6) {
                    $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('required'));
                    return $this;
                }

                $this->updateData($POST, 'users', $this->getHelper('ErrorMessaging')->getMessageAuto('account_saved'), $this->getHelper('ErrorMessaging')->getMessageAuto('account_savedfail'));
            } else {
                if (!empty($POST['ID'])) {
                    $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('invalid_request'));
                    return $this;
                }

                if ($this->getHelper('nUser')->getUser($this->getRequest('username'))) {
                    $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('fail_userexists'));
                    return $this;
                }

                $required = array_filter([
                    (isset($POST['username']) && filter_var($POST['username'], FILTER_VALIDATE_EMAIL)) ? $POST['username'] : false,
                    (isset($POST['email']) && filter_var($POST['email'], FILTER_VALIDATE_EMAIL)) ? $POST['email'] : false,
                    $this->getPost('first_name', false),
                    $this->getPost('last_name', false),
                    ($POST['usergroup']) ?? false,
                    ($POST['user_status']) ?? 'on',
                    (isset($POST['password'])) ? trim($this->getPost('password', false)) : false
                ]);

                if (count($required) < 7) {
                    $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('required'));
                    return $this;
                }

                $POST['password'] = trim($this->getPost('password', false));
                $POST['unique_id'] = $this->fetchUniqueId();
                $POST['timestamp'] = date('Y-m-d H:i:s');

                if ($this->getHelper('nUser')->create($POST)) {
                    $this->toSuccess($this->getHelper('ErrorMessaging')->getMessageAuto('success_usercreate'));
                    return $this;
                } else {
                    $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto(500));
                    return $this;
                }
            }
        }
    }

    protected function addNewRecord($POST)
    {
        $type  = (!empty($POST['parent_type'])) ? $POST['parent_type'] : 'code';
        $refpage = (!empty($POST['ref_page'])) ? $POST['ref_page'] : $this->getPage('unique_id');

        unset($POST['parent_type']);

        $this->getHelper("nQuery")
            ->insert('components')
            ->columns(['unique_id', 'ref_page', 'component_type', 'title', 'page_live'])
            ->values([
                [$this->fetchUniqueId(), $refpage, 'code', 'Untitled (' . date('Y-m-d H:i:s') . ')', 'off']
            ])
            ->write();
        $this->toSuccess($this->getHelper('ErrorMessaging')->getMessageAuto('success_componentcreate'));

        return $this;
    }

    protected function duplicateRecord($POST)
    {
        $ID = $POST['parent_dup'];
        unset($POST['parent_dup']);

        $duplicate = $this->getHelper("Settings")->getComponent($ID);
        if (empty($duplicate)) {
            $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('invalid_component'));
            return $this;
        }

        unset($duplicate['ID']);
        $duplicate['unique_id'] = $this->fetchUniqueId();

        if (!empty($duplicate['file_path'])) {
            $from = NBR_DOMAIN_ROOT . $duplicate['file_path'] . $duplicate['file_name'];
            $finfo = pathinfo($from);
            $fname = $finfo['filename'] . date('YmdHis') . '.' . $finfo['extension'];
            $to  = $finfo['dirname'] . DS . $fname;
            if (!copy($from, $to)) {
                $duplicate['file_path'] =
                    $duplicate['file_name'] = '';
            } else {
                $duplicate['file_path'] = str_replace(NBR_DOMAIN_ROOT, '', $finfo['dirname'] . DS);
                $duplicate['file_name'] = $fname;
            }
        }
        $timestamp = date('Y-m-d H:i:s');
        if (!empty($duplicate['title']))
            $duplicate['title']    .= "-Copy {$timestamp}";

        $duplicate['timestamp'] = $timestamp;

        $duplicate = array_filter($duplicate);

        $this->getHelper("nQuery")->insert('components')
            ->columns(array_keys($duplicate))
            ->values([
                array_values($duplicate)
            ])
            ->write();

        $this->Router->redirect($this->localeUrl($this->getPage('full_path') . '?msg=success_create'));
    }

    protected function deleteRecord($ID)
    {
        $query = $this->query("SELECT unique_id FROM components WHERE ID = ?", [$ID])->getResults(1);

        if (!empty($query['unique_id'])) {
            $this->query("UPDATE components SET parent_id = '' WHERE parent_id = ?", [$query['unique_id']]);
        }
        $this->query("DELETE FROM `components` WHERE ID = ?", [$ID]);

        if (empty($this->getHelper('Settings')->getComponent($ID))) {
            $this->toSuccess($this->getHelper('ErrorMessaging')->getMessageAuto('success_delete'));
        } else {
            $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto(500));
        }
    }

    protected function updateRecord($POST, $token, $Token)
    {
        $POST['timestamp'] = date('Y-m-d H:i:s');
        if (empty(!$POST['ID'])) {
            if (empty($token) || !$Token->match('component_' . $POST['ID'], $token)) {
                $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('invalid_request'));
                return false;
            }

            if (!empty($POST['file_name'])) {
                $fname = $this->query("SELECT `file_name` as `filename` FROM components WHERE ID = ?", [$POST['ID']])->getResults(1);
                $fname = (!empty($fname['filename'])) ? $fname['filename'] : false;

                if (!empty($fname)) {
                    if ($fname !== $POST['file_name']) {
                        $ext = pathinfo($fname, PATHINFO_EXTENSION);
                        $newFnm = trim(preg_replace('/[^A-Z0-9\-\_]/i', '', pathinfo($POST['file_name'], PATHINFO_FILENAME)));

                        if (!empty($newFnm)) {
                            $old = NBR_DOMAIN_ROOT . $POST['file_path'] . $fname;
                            $new = NBR_DOMAIN_ROOT . $POST['file_path'] . $newFnm . '.' . $ext;
                            $thumb = NBR_DOMAIN_ROOT . $POST['file_path'] . 'thumbs' . DS . $fname . '.' . $ext;

                            if (is_file($thumb))
                                unlink($thumb);

                            rename($old, $new);

                            $POST['file_name'] = $newFnm . '.' . $ext;
                        }
                    }
                }
            }
            $page_match = ($this->getPage('unique_id') == $POST['ref_page']);
            if (!$page_match) {
                $thisObj = $this;
                $uniques = [];
                $Page  = $this->getHelper('Settings\Page\Controller');
                $struct  = $Page->getContentStructure($this->getPage('unique_id'));
                $test  = $this->getHelper('ArrayWorks')->recurseApply($struct, function ($k, $v) use ($POST, $thisObj, &$uniques) {

                    if ($k == $POST['unique_id']) {
                        if (!empty($v)) {
                            \Nubersoft\ArrayWorks::getRecursiveKeys($v, $uniques);
                        }
                    }
                });

                if (!empty($uniques)) {
                    $c   = count($uniques);
                    $uniques = array_merge([$POST['ref_page']], $uniques);

                    $this->query("UPDATE components SET `ref_page` = ? WHERE `unique_id` IN (" . (implode(',', array_fill(0, $c, '?'))) . ")", $uniques);
                }
            }
            $this->updateData($POST, 'components', 'Component updated');
            if (!$page_match && !$this->isAjaxRequest()) {
                $newPage = $this->getHelper('nRouter')->getPage($POST['ref_page'], 'unique_id');
                $this->Router->redirect($newPage['full_path']);
            }
        }
        return $this;
    }

    public function editTable($POST, $table, $token, $Token, $msg = 'Row saved')
    {
        if ($table == 'users') {
            $this->updateUserData($POST, $token, $Token, $table, $this->getRequest('ID'));
            return false;
        }

        if (empty($token) || !$Token->match('page', $token)) {
            $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('invalid_request'));
            return false;
        }

        if (!empty($POST['ID']) && is_numeric($POST['ID'])) {
            if (!empty($POST['delete'])) {
                $this->removeCurrentFilePath($POST['ID'], $table);
                $this->deleteFrom($table, $POST['ID']);
                $this->redirect($this->getPage('full_path') . "?table=" . $table . "&msg=success_delete");
            } else
                $this->updateData($POST, $table, $msg);
        } else {
            if (empty($POST['ID'])) {
                $POST['timestamp'] = date('Y-m-d H:i:s');
                $POST['unique_id'] = $this->fetchUniqueId();
                $POST = $this->getRowsInTable($table, $POST);
                $this->setFileData($POST);
                $sql = "INSERT INTO `" . $table . "` (`" . implode('`, `', array_keys($POST)) . "`) VALUES(" . implode(',', array_fill(0, count($POST), '?')) . ")";
                @$this->nQuery()->query($sql, array_values($POST));

                if ($this->isAjaxRequest() && $table == 'media') {
                    $this->ajaxResponse([
                        'html' => [
                            $this->getPlugin('admintools', 'media' . DS . 'index.php')
                        ],
                        'sendto' => [
                            '#admin-content'
                        ]
                    ]);
                }

                $this->redirect($this->getPage('full_path') . "?table=" . $table . "&msg=success_created");
            } else {
                $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('invalid_request'));
            }
        }

        if ($this->isAjaxRequest()) {
            $this->ajaxResponse($this->getSystemMessages());
        }
    }

    public function    getRowsInTable($table, $array = false)
    {
        $columns = $this->getColumnsInTable($table);

        if (is_bool($array))
            return $columns;

        foreach ($array as $key => $value) {
            if (!in_array($key, $columns)) {
                unset($array[$key]);
            }
        }

        return $array;
    }

    protected function setFileData(&$POST, $ID = false)
    {
        $FILES = $this->getDataNode('_FILES');

        if (!empty($FILES)) {
            if ($FILES[0]['error'] == 0) {
                if (is_numeric($ID))
                    $this->removeCurrentFilePath($ID);
                $POST['file_name'] = preg_replace('/[^A-Z0-9_-]/i', '', pathinfo($FILES[0]['name'], PATHINFO_FILENAME)) . '.' . pathinfo($FILES[0]['name'], PATHINFO_EXTENSION);
                $POST['file_path'] = pathinfo($FILES[0]['path_default'], PATHINFO_DIRNAME) . DS;
                $POST['file_size'] = $FILES[0]['size'];
                $POST['file']   = $POST['file_path'] . $POST['file_name'];
                $path = str_replace(DS . DS, DS, NBR_DOMAIN_ROOT . DS . $POST['file_path']);
                $this->isDir($path, true);

                $move   =   move_uploaded_file($FILES[0]['tmp_name'], str_replace(DS . DS, DS, $path . DS . $POST['file_name']));

                if (!$move) {
                    unset($POST['file_name'], $POST['file_path'], $POST['file_size'], $POST['file']);
                    $msg = $this->getHelper('ErrorMessaging')->getMessageAuto('fail_upload');
                    ($this->isAjaxRequest()) ? $this->ajaxResponse([
                        'alert' => "{$msg}: {$path}"
                    ]) : $this->toError($msg);
                }
            } else
                $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('fail_upload'));
        }
    }

    public function deleteFrom($table, $value, $col = "ID")
    {
        @$this->nQuery()->query("DELETE FROM {$table} WHERE {$col} = ?", [$value]);
    }
}
