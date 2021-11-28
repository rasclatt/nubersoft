<?php
namespace Nubersoft;

class nUser extends \Nubersoft\nQuery
{
    protected $required   =   [
        'username',
        'password',
        'first_name',
        'last_name',
        'country'
    ];

    public function getUser($value, $type = 'username')
    {
        return $this->select()
            ->from('users')
            ->where([
                [
                    'c' => $this->stripTableName($type),
                    'v' => strtolower($value)
                ]
            ])
            ->fetch(1);
    }

    public function userExists($value, string $type = 'username'): int
    {
        return (int) $this->query("SELECT COUNT(*) as count FROM users WHERE `{$this->stripTableName($type)}` = LOWER(?)", [strtolower($value)])->getResults(1)['count'];
    }
    /**
     * @description 
     */
    public function setRequiredFields(array $array, $override = false)
    {
        $this->required =   ($override) ? $array : array_merge($this->required, $array);
        return $this;
    }
    /**
     * @description This is a simple empty check
     */
    protected function isValid($key, array $data)
    {
        if (!isset($data[$key]))
            return false;

        return (!empty(trim($data[$key])));
    }

    public function create($data, $autologin = false)
    {
        $data = \Nubersoft\ArrayWorks::trimAll($data);
        $username = strtolower((!empty($data['username'])) ? $data['username'] : false);
        $password = (!empty($data['password'])) ? $this->hashPassword($data['password']) : false;
        $first_name = (!empty($data['first_name'])) ? $this->enc($data['first_name']) : false;
        $last_name = (!empty($data['last_name'])) ? $this->enc($data['last_name']) : false;
        $status = (!empty($data['user_status'])) ? $data['user_status'] : 'on';
        $email = strtolower((!empty($data['email'])) ? $data['email'] : $username);
        $usergroup = (!empty($data['usergroup'])) ? $data['usergroup'] : NBR_WEB;

        if (!is_numeric($usergroup))
            $usergroup    =    constant($usergroup);

        $Errors = new \Nubersoft\ErrorMessaging();

        if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $this->toError($Errors->getMessageAuto('invalid_username'));
            return false;
        }
        # Stop of required fields are missing
        if (empty($this->required))
            throw new \Exception('You need required fields to create users');
        # Check if fields are required
        $allowed   =   false;
        foreach ($this->required as $r) {
            $allowed = $this->isValid($r, $data);
            if (!$allowed)
                break;
        }
        # If not allowed, just stop
        if (!$allowed) {
            $msg = 'required';
        } elseif ($this->userExists($username) != 0) {
            $allowed = false;
            $msg = 'fail_userexists';
        }
        # Stop and return message
        if (!$allowed) {
            $this->toError($Errors->getMessageAuto($msg));
            return false;
        }

        $this->insert('users')
            ->columns([
                'unique_id',
                'username',
                'password',
                'first_name',
                'last_name',
                'user_status',
                'email',
                'usergroup',
                'timestamp'
            ])
            ->values([
                [
                    date('YmdHis') . rand(1000000, 9999999),
                    $username,
                    $password,
                    $first_name,
                    $last_name,
                    $status,
                    $username,
                    $usergroup,
                    date('Y-m-d H:i:s')
                ]
            ])
            ->write();

        if ($autologin) {
            if ($this->userExists($username)) {
                $this->getHelper('System')->login($username, $password);
                $this->redirect($this->getPage('full_path'));
            }
        }

        return $this->getUser($username);
    }

    public function hashPassword($password, $cost = 9)
    {
        $passcost = (defined('NBR_PASS_COST') && is_numeric(NBR_PASS_COST)) ? NBR_PASS_COST : $cost;
        return password_hash($password, PASSWORD_DEFAULT, ['cost' => $passcost]);
    }
}
