<?php
namespace Nubersoft\System\Observer;

use \Nubersoft\{
    nApp,
    nQuery\enMasse as nQueryEnMasse,
    nDynamics,
    nRouter\Controller as Router,
    nToken,
    Settings\Controller as Settings,
    DataNode,
    nRender,
    ErrorMessaging,
    Dto\Tables as TablesDto
};

use \Nubersoft\Dto\Settings\Page\View\ConstructRequest as Helpers;

class Tables extends \Nubersoft\System\Observer
{
    use nQueryEnMasse, nDynamics;

    protected $nRender, $nApp, $Router, $Token, $Settings, $DataNode, $LocaleMsg;

    public function __construct(
        Router $Router,
        nToken $Token,
        Settings $Settings,
        DataNode $DataNode,
        nApp $nApp,
        ErrorMessaging $LocaleMsg
    ) {
        $this->Router = $Router;
        $this->Token = $Token;
        $this->Settings = $Settings;
        $this->DataNode = $DataNode;
        $this->nApp = $nApp;
        $this->nRender = new nRender(new Helpers);
        $this->LocaleMsg = $LocaleMsg;
    }
    /**
     *	@description	
     *	@param	
     */
    public function saveTable(TablesDto $Table, nToken $Token, string $token = null)
    {
        # Convert Dto to POST
        $POST = $Table->toArray();
        # Set the table
        $table = $Table->getTable();
        # Divert on user table
        if ($table == 'users') {
            $POST = $this->getRowsInTable($table, $POST);
            $this->updateUserData($POST, $token, $Token, $table, $Table->ID);
            return false;
        }
        # If token invalid, stop
        if (empty($token) || !$Token->match('page', $token)) {
            $this->nApp->toError($this->LocaleMsg->getMessageAuto('invalid_request'));
            return false;
        }
        # Write a success message
        $msg = $this->LocaleMsg->getMessageAuto('success');
        # Edit a current row
        if (!empty($Table->ID)) {    
            if (!empty($POST['delete'])) {
                $this->removeCurrentFilePath($POST['ID'], $table);
                $this->deleteFrom($table, $POST['ID']);
                $this->nApp->redirect( $this->nRender->getPage('full_path') . "?table=" . $table . "&msg=success_delete");
            } else
                $this->updateData($POST, $table, $msg);
        } else {
            if (empty($POST['ID'])) {
                
                $this->setFileData($POST);
                $sql = "INSERT INTO `" . $table . "` (`" . implode('`, `', array_keys($POST)) . "`) VALUES(" . implode(',', array_fill(0, count($POST), '?')) . ")";

                @$this->nQuery()->query($sql, array_values($POST));

                if ($this->nApp->isAjaxRequest() && $table == 'media') {
                     $this->nApp->ajaxResponse([
                        'html' => [
                             $this->nApp->getPlugin('admintools', 'media' . DS . 'index.php')
                        ],
                        'sendto' => [
                            '#admin-content'
                        ]
                    ]);
                }

                 $this->nApp->redirect( $this->nRender->getPage('full_path') . "?table=" . $table . "&msg=success");
            } else {
                $this->nApp->toError($this->LocaleMsg->getMessageAuto('invalid_request'));
            }
        }

        if ($this->nApp->isAjaxRequest()) {
             $this->nApp->ajaxResponse($this->getSystemMessages());
        }
    }

    public function listen()
    {
        if (!$this->nApp->isAdmin()) {
            $this->nApp->toError($this->LocaleMsg->getMessageAuto(403));
            return false;
        }
        $Token = $this->Token;
        $POST = (!empty($this->nApp->getPost('ID'))) ? $this->nApp->getPost() : array_filter($this->nApp->getPost());
        $action = (!empty($POST['action'])) ? $POST['action'] : false;
        $token = (!empty($this->nApp->getPost('token')['nProcessor'])) ? $this->nApp->getPost('token')['nProcessor'] : false;
        # Remove action k/v
        if (isset($POST['action']))
            unset($POST['action']);
        # Remove the token k/v
        if (isset($POST['token']))
            unset($POST['token']);
        # Set the table
        $table = preg_replace('/[^\dA-Z\.-_]/i', '', $this->nApp->getRequest('table'));
        # Create a Dto
        $tableDtoPath = "\\Nubersoft\\Dto\\Tables\\".str_replace(' ', '', ucwords(str_replace('_',' ', $table)));
        # Run action
        switch ($action) {
            case ('edit_table_rows_details'):
                # Stop if table is empty
                if(empty($table))
                    throw new \Exception('Bad request', 500);
                # Remove all unlrealted fields from the post array
                $this->filterUnMatched($POST, $table);
                # If there is a Dto, apply it
                class_exists($tableDtoPath)? $this->saveTable(new $tableDtoPath($POST), $Token, $token) : $this->editTable($POST, $table, $token, $Token);
                return $this;
            case ('edit_user_details'):
                $this->updateUserData((new $tableDtoPath($POST))->toArray(), $token, $Token, $table, $this->nApp->getRequest('ID'));
                return $this;
            case ('edit_component'):
                $action = $POST['subaction']?? null;
                $POST = (new \Nubersoft\Dto\Tables\Components($POST))->toArray();
                $timestamp = date('Y-m-d H:i:s');
                if (!empty($action)) {
                    switch ($action) {
                        case ('add_new'):
                            $this->addNewRecord($POST);
                            break;
                        case ('duplicate'):
                            $this->duplicateRecord($POST);
                    }
                } else {
                    if ($this->nApp->getPost('delete') == 'on') {
                        $this->deleteRecord($POST['ID']);
                        $msg = "Refresh the page to see update.";
                    } else {
                        $this->updateRecord($POST, $token, $Token);
                    }
                }
                if (!isset($msg))
                    $msg = 'Updated';

                if ($this->nApp->isAjaxRequest()) {
                     $this->nApp->ajaxResponse(['alert' => $msg, 'msg' => $this->getSystemMessages()]);
                }
                break;
            case ('update_admin_url'):
                $this->updatePage($token, $Token, $POST);
                break;
            case ('create_new_page'):
                # Match token for all
                if (empty($token) || !$Token->match('page', $token, false, false)) {
                    $this->nApp->toError($this->LocaleMsg->getMessageAuto('invalid_tokenmatch'));
                    return false;
                }

                if (empty($POST['full_path'])) {
                    $dpath = implode('/', [date('Y'), date('m'), date('d'), date('s')]);
                    $path = ( $this->nRender->getPage('is_admin') != 1) ?  $this->nRender->getPage('full_path') . $dpath : '/' . $dpath;
                } else
                    $path = $this->convertToStandardPath($POST['full_path']);

                @$this->nQuery()->insert("main_menus")
                    ->columns(['unique_id', 'full_path', 'page_live', 'menu_name', 'link'])
                    ->values([[$this->nApp->fetchUniqueId(), '/' . trim($path, '/') . '/', 'off', 'Untitled', 'untitled' . date('YmdHis')]])
                    ->write();
                $this->Router->redirect($this->nApp->localeUrl($path));
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
            $this->nApp->toError($this->LocaleMsg->getMessageAuto('invalid_tokenmatch'));
            return false;
        }
        $ID = $this->nApp->getPost('ID');
        if (empty($ID)) {
            $this->nApp->toError($this->LocaleMsg->getMessageAuto('invalid_page'));
            return false;
        }

        if (!empty($POST['delete'])) {
            if ($allow_delete) {
                if ($this->Settings->deletePage($ID)) {
                    $this->Router->redirect('/?msg=success_delete');
                }
                return true;
            } else {
                $this->nApp->toError($this->LocaleMsg->getMessageAuto('403_delete'));
                return false;
            }
        }

        $POST['full_path'] = $this->Router->convertToStandardPath($POST['full_path']);
        $existing = $this->Router->getPage($POST['full_path'], 'full_path');
        if (empty($POST['full_path'])) {
            $this->nApp->toError($this->LocaleMsg->getMessageAuto('invalid_slug'));
            return false;
        }

        if ($existing->is_valid) {
            if ($existing->ID != $POST['ID']) {
                $this->nApp->toError($this->LocaleMsg->getMessageAuto('invalid_slugexists'));
                return false;
            }
        }
        # Remove the ID for the update
        unset($POST['ID']);

        if ($POST['full_path'] == '/') {
            $POST['is_admin'] = 2;
            $POST['link'] = 'home';
        } else {
            $POST['link'] = strtolower(pathinfo($POST['full_path'], PATHINFO_BASENAME));
        }
        # Remove unavailable keys
        $this->filterUnMatched($POST, 'main_menus');

        $sql = [];
        foreach ($POST as $key => $value) {
            $sql[] = "`{$key}` = ?";
        }
        $this->query("UPDATE `main_menus` SET " . implode(', ', $sql) . " WHERE ID = ? ", array_values(array_merge(array_values($POST), [$ID])));

        if ( $this->nRender->getPage('full_path') != $POST['full_path']) {
            $this->Router->redirect($POST['full_path'] . '?msg=fail_update');
        } else {
            $this->Router->redirect($POST['full_path'] . '?msg=success_settingssaved&' . http_build_query($this->nApp->getGet()));
        }
    }

    /**
     *	@description	
     */
    public function filterUnMatched(&$array, $table)
    {
        $cols = [];
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

    protected function updateData($POST, $table, $msg = null, $err = false)
    {
        $table = preg_replace('/[^0-9A-Z\_\.\`\-]/i', '', $table);
        $ID = (is_numeric($POST['ID'])) ? $POST['ID'] : false;
        if (empty($POST) || empty($ID)) {
            $this->nApp->toError((!empty($err)) ? $err : $this->LocaleMsg->getMessageAuto('no_action'));
            return false;
        }
        # Process file and the fields associated with the file
        $this->setFileData($POST, $ID);
        # Remove any fields that don't exist in the table
        //$this->filterUnMatched($POST, $table);
        # Create array
        foreach ($POST as $keys => $values) {
            $bind[] = $values;
            $sql[] = "`{$keys}` = ?";
        }
        $this->query("UPDATE `{$table}` SET " . implode(', ', $sql) . " WHERE ID = '{$ID}'", $bind);

        $this->nApp->toSuccess($msg);
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
            $this->nApp->toError($this->LocaleMsg->getMessageAuto('invalid_request'));
            return false;
        }
        if ($this->nApp->getPost('delete') == 'on') {
            $this->query("DELETE FROM `" . str_replace('`', '', $table) . "` WHERE ID = ?", [$ID]);

            if (empty((new \Nubersoft\nUser)->getUser($ID, 'ID'))) {
                $this->Router->redirect($this->nApp->localeUrl(\Nubersoft\nReflect::instantiate('\Nubersoft\nRender')->getPage('full_path') . '?table=users&msg=success_delete'));
            } else {
                $this->nApp->toError($this->LocaleMsg->getMessageAuto(500));
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
                    $POST['password'] = (new \Nubersoft\nUser)->hashPassword($this->nApp->getPost('password', false));
                } else {
                    unset($POST['password']);
                }

                if (count($required) < 6) {
                    $this->nApp->toError($this->LocaleMsg->getMessageAuto('required'));
                    return $this;
                }

                $this->updateData($POST, 'users', $this->LocaleMsg->getMessageAuto('account_saved'), $this->LocaleMsg->getMessageAuto('account_savedfail'));
            } else {
                if (!empty($POST['ID'])) {
                    $this->nApp->toError($this->LocaleMsg->getMessageAuto('invalid_request'));
                    return $this;
                }

                if ((new \Nubersoft\nUser)->getUser($this->nApp->getRequest('username'))) {
                    $this->nApp->toError($this->LocaleMsg->getMessageAuto('fail_userexists'));
                    return $this;
                }

                $required = array_filter([
                    (isset($POST['username']) && filter_var($POST['username'], FILTER_VALIDATE_EMAIL)) ? $POST['username'] : false,
                    (isset($POST['email']) && filter_var($POST['email'], FILTER_VALIDATE_EMAIL)) ? $POST['email'] : false,
                    $this->nApp->getPost('first_name', false),
                    $this->nApp->getPost('last_name', false),
                    ($POST['usergroup']) ?? false,
                    ($POST['user_status']) ?? 'on',
                    (isset($POST['password'])) ? trim($this->nApp->getPost('password', false)) : false
                ]);

                if (count($required) < 7) {
                    $this->nApp->toError($this->LocaleMsg->getMessageAuto('required'));
                    return $this;
                }

                $POST['password'] = trim($this->nApp->getPost('password', false));
                $POST['unique_id'] = $this->nApp->fetchUniqueId();
                $POST['timestamp'] = date('Y-m-d H:i:s');

                if ((new \Nubersoft\nUser)->create($POST)) {
                    $this->nApp->toSuccess($this->LocaleMsg->getMessageAuto('success_usercreate'));
                    return $this;
                } else {
                    $this->nApp->toError($this->LocaleMsg->getMessageAuto(500));
                    return $this;
                }
            }
        }
    }

    protected function addNewRecord($POST)
    {
        $type = (!empty($POST['parent_type'])) ? $POST['parent_type'] : 'code';
        $refpage = (!empty($POST['ref_page'])) ? $POST['ref_page'] :  $this->nRender->getPage('unique_id');

        unset($POST['parent_type']);

        (new \Nubersoft\nQuery)
            ->insert('components')
            ->columns(['unique_id', 'ref_page', 'component_type', 'title', 'page_live'])
            ->values([
                [$this->nApp->fetchUniqueId(), $refpage, 'code', 'Untitled (' . date('Y-m-d H:i:s') . ')', 'off']
            ])
            ->write();
        $this->nApp->toSuccess($this->LocaleMsg->getMessageAuto('success_componentcreate'));

        return $this;
    }

    protected function duplicateRecord($POST)
    {
        $ID = $POST['parent_dup'];
        unset($POST['parent_dup']);

        $duplicate = $this->getHelper("Settings")->getComponent($ID);
        if (empty($duplicate)) {
            $this->nApp->toError($this->LocaleMsg->getMessageAuto('invalid_component'));
            return $this;
        }

        unset($duplicate['ID']);
        $duplicate['unique_id'] = $this->nApp->fetchUniqueId();

        if (!empty($duplicate['file_path'])) {
            $from = NBR_DOMAIN_ROOT . $duplicate['file_path'] . $duplicate['file_name'];
            $finfo = pathinfo($from);
            $fname = $finfo['filename'] . date('YmdHis') . '.' . $finfo['extension'];
            $to = $finfo['dirname'] . DS . $fname;
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
            $duplicate['title'] .= "-Copy {$timestamp}";

        $duplicate['timestamp'] = $timestamp;

        $duplicate = array_filter($duplicate);

        (new \Nubersoft\nQuery)->insert('components')
            ->columns(array_keys($duplicate))
            ->values([
                array_values($duplicate)
            ])
            ->write();

        $this->Router->redirect($this->nApp->localeUrl( $this->nRender->getPage('full_path') . '?msg=success_create'));
    }

    protected function deleteRecord($ID)
    {
        $query = $this->query("SELECT unique_id FROM components WHERE ID = ?", [$ID])->getResults(1);

        if (!empty($query['unique_id'])) {
            $this->query("UPDATE components SET parent_id = '' WHERE parent_id = ?", [$query['unique_id']]);
        }
        $this->query("DELETE FROM `components` WHERE ID = ?", [$ID]);

        if (empty($this->Settings->getComponent($ID))) {
            $this->nApp->toSuccess($this->LocaleMsg->getMessageAuto('success_delete'));
        } else {
            $this->nApp->toError($this->LocaleMsg->getMessageAuto(500));
        }
    }

    protected function updateRecord($POST, $token, $Token)
    {
        if (empty($POST['ID']))
            return $this;

        if (empty($token) || !$Token->match('component_' . $POST['ID'], $token)) {
            $this->nApp->toError($this->LocaleMsg->getMessageAuto('invalid_request'));
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
        $page_match = ( $this->nRender->getPage('unique_id') == $POST['ref_page']);
        if (!$page_match) {
            $thisObj = $this;
            $uniques = [];
            $Page = new \Nubersoft\Settings\Page\Controller;
            $struct = $Page->getContentStructure( $this->nRender->getPage('unique_id'));
            $test = \Nubersoft\ArrayWorks::recurseApply($struct, function ($k, $v) use ($POST, $thisObj, &$uniques) {

                if ($k == $POST['unique_id']) {
                    if (!empty($v)) {
                        \Nubersoft\ArrayWorks::getRecursiveKeys($v, $uniques);
                    }
                }
            });

            if (!empty($uniques)) {
                $c = count($uniques);
                $uniques = array_merge([$POST['ref_page']], $uniques);
                $this->query("UPDATE components SET `ref_page` = ? WHERE `unique_id` IN (" . (implode(',', array_fill(0, $c, '?'))) . ")", $uniques);
            }
        }
        $this->updateData($POST, 'components', 'Component updated');
        if (!$page_match && !$this->nApp->isAjaxRequest()) {
            $newPage = $this->getHelper('nRouter')->getPage($POST['ref_page'], 'unique_id');
            $this->Router->redirect($newPage['full_path']);
        }
        return $this;
    }

    public function editTable($POST, $table, $token, $Token, $msg = 'Row saved')
    {
        if ($table == 'users') {
            $POST = $this->getRowsInTable($table, $POST);
            $this->updateUserData($POST, $token, $Token, $table, $this->nApp->getRequest('ID'));
            return false;
        }

        if (empty($token) || !$Token->match('page', $token)) {
            $this->nApp->toError($this->LocaleMsg->getMessageAuto('invalid_request'));
            return false;
        }
        
        if (!empty($POST['ID']) && is_numeric($POST['ID'])) {
            if (!empty($POST['delete'])) {
                $this->removeCurrentFilePath($POST['ID'], $table);
                $this->deleteFrom($table, $POST['ID']);
                 $this->nApp->redirect( $this->nRender->getPage('full_path') . "?table=" . $table . "&msg=success_delete");
            } else {
                $POST = $this->getRowsInTable($table, $POST);
                $this->updateData($POST, $table, $msg);
            }
        } else {
            if (empty($POST['ID'])) {
                $POST['timestamp'] = date('Y-m-d H:i:s');
                $POST['unique_id'] = $this->nApp->fetchUniqueId();
                $POST = $this->getRowsInTable($table, $POST);
                $this->setFileData($POST);
                $sql = "INSERT INTO `" . $table . "` (`" . implode('`, `', array_keys($POST)) . "`) VALUES(" . implode(',', array_fill(0, count($POST), '?')) . ")";
                @$this->nQuery()->query($sql, array_values($POST));

                if ($this->nApp->isAjaxRequest() && $table == 'media') {
                     $this->nApp->ajaxResponse([
                        'html' => [
                             $this->nApp->getPlugin('admintools', 'media' . DS . 'index.php')
                        ],
                        'sendto' => [
                            '#admin-content'
                        ]
                    ]);
                }

                 $this->nApp->redirect( $this->nRender->getPage('full_path') . "?table=" . $table . "&msg=success");
            } else {
                $this->nApp->toError($this->LocaleMsg->getMessageAuto('invalid_request'));
            }
        }

        if ($this->nApp->isAjaxRequest()) {
             $this->nApp->ajaxResponse($this->getSystemMessages());
        }
    }

    public function getRowsInTable($table, $array = false)
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
                $POST['file'] = $POST['file_path'] . $POST['file_name'];

                $move = move_uploaded_file($FILES[0]['tmp_name'], str_replace(DS . DS, DS, NBR_DOMAIN_ROOT . DS . $POST['file_path'] . DS . $POST['file_name']));

                if (!$move) {
                    unset($POST['file_name'], $POST['file_path'], $POST['file_size'], $POST['file']);
                    $this->nApp->toError($this->LocaleMsg->getMessageAuto('fail_upload'));
                }
            } else
                $this->nApp->toError($this->LocaleMsg->getMessageAuto('fail_upload'));
        }
    }

    public function deleteFrom($table, $value, $col = "ID")
    {
        @$this->nQuery()->query("DELETE FROM {$table} WHERE {$col} = ?", [$value]);
    }
}