<?php
namespace Nubersoft;

use \Nubersoft\nFileHandler as Files;

class System extends nSession\Controller
{
    protected $nApp, $nQuery;
    /**
     *	@description	
     *	@param	
     */
    public function __construct(
        nApp $nApp,
        nQuery $nQuery
    )
    {
        $this->nQuery = $nQuery;
        $this->nApp = $nApp;
    }

    public function login($username, $password)
    {
        # Check login validation
        $validate = $this->validate($username, $password, true);
        # Stop if invalid
        if (empty($validate)) {
            $this->nApp->toError((new ErrorMessaging)->getMessageAuto('invalid_user'), false, false);
            return false;
        } else {
            $user = $validate['user'];
            #See if user is active, stop if not
            if ($user['user_status'] != 'on') {
                $this->nApp->toError((new ErrorMessaging)->getMessageAuto('account_disabled'));
                return false;
            } elseif (!$validate['allowed']) {
                $this->nApp->toError((new ErrorMessaging)->getMessageAuto('invalid_user'));
                return false;
            }
            # Save the user session
            $this->toUserSession($user);
            # Regenerate the session id
            $this->newSessionId();
            # Report back
            $this->nApp->toSuccess((new ErrorMessaging)->getMessageAuto('success_login'));
            # Set success
            return true;
        }
    }

    public function validate($username, $password, $return = false)
    {
        $user = (new nUser)->getUser($username);

        if (empty($user['ID']))
            return false;

        $valid = password_verify($password, $user['password']);
        $allowed = ($valid && ($user['user_status'] == 'on'));

        if (empty($return))
            return $allowed;

        if (!is_numeric($user['usergroup']))
            $user['usergroup'] = (int) constant($user['usergroup']);

        return [
            'valid' => $valid,
            'status' => $user['user_status'],
            'allowed' => $allowed,
            'is_admin' => ($user['usergroup'] <= NBR_ADMIN),
            'user' => $user
        ];
    }

    public function logout($redirect = false)
    {
        $this->destroy();

        if ($redirect) {
            (new nRouter\Controller(new Conversion\Data))->redirect($redirect);
        }
    }

    public function toUserSession($user)
    {
        $this->set('user', $user);
        return $this;
    }
    /**
     * @description Downloads a valid file
     * */
    public function downloadFile($file)
    {
        if(!is_file($file)) {
            $this->nApp->toError("File is invalid.", 500);
            return $this;
        }
        # Download file
        Files::download($file);
    }

    public function deleteFile($file, $ID = false, $table = false)
    {
        $update = false;
        $err = (new ErrorMessaging)->getMessageAuto('fail_delete');
        $succ = (new ErrorMessaging)->getMessageAuto('success_delete');
        if (!is_file($file)) {
            if (!empty($table) && !empty($ID)) {
                $update = true;
                $this->nApp->toSuccess($succ);
            } else {
                $this->nApp->toError((new ErrorMessaging)->getMessageAuto('fail') . ': ' . $err);
                return false;
            }
        } else {
            if (unlink($file)) {
                $thumb = $this->deleteThumbnail($file);

                if (is_file($thumb)) {
                    if (unlink($thumb)) {
                        $this->nApp->toSuccess((new ErrorMessaging)->getMessageAuto('success_thumbremoved'));
                    } else {
                        $this->nApp->toError((new ErrorMessaging)->getMessageAuto('fail_thumbremoved'));
                    }
                }

                if (!empty($table) && !empty($ID))
                    $update = true;

                $this->nApp->toSuccess($succ);
            } else {
                $this->nApp->toError($err);
                return false;
            }
        }

        if ($update) {
            if (empty($this->nQuery))
                $this->nQuery = new nQuery;

            $this->nQuery->query("UPDATE {$table} SET file_path = '', file_name = '', file_size = '' WHERE ID = ?", [$ID]);
        }
    }

    public function deleteThumbnail($filename)
    {
        $info = pathinfo($filename);

        $thumb = $info['dirname'] . DS . 'thumbs' . DS . $info['filename'] . '.' . $info['extension'];

        return $thumb;
    }

    public function createReWrite($content, $path, $ext = '.htaccess')
    {
        $file = $path . DS . $ext;
        file_put_contents($file, $content);
        return is_file($file);
    }
}
