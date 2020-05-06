<?php
namespace Nubersoft;

class nUser extends \Nubersoft\nQuery
{
    public    function getUser($value, $type = 'username')
    {
        $data    =    $this->select()
            ->from('users')
            ->where([['c' => $type, 'v' => $value]])
            ->fetch(1);
    
        return $data;
    }
    
    public    function userExists($value, $type = 'username')
    {
        return $this->select("COUNT(*) as count", false)
            ->from("users")
            ->where([['c'=>$type, 'v'=> $value]])
            ->fetch(1)['count'];
    }
    
    public    function create($data, $autologin = false)
    {
        $data        =    $this->getHelper('ArrayWorks')->trimAll($data);
        $username    =    (!empty($data['username']))? $data['username'] : false;
        $password    =    (!empty($data['password']))? $this->getHelper('nUser')->hashPassword($data['password']) : false;
        $first_name    =    (!empty($data['first_name']))? $this->enc($data['first_name']) : false;
        $last_name    =    (!empty($data['last_name']))? $this->enc($data['last_name']) : false;
        $status        =    (!empty($data['user_status']))? $data['user_status'] : 'on';
        $email        =    (!empty($data['email']))? $data['email'] : $username;
        $usergroup    =    (!empty($data['usergroup']))? $data['usergroup'] : NBR_WEB;
        
        if(!is_numeric($usergroup))
            $usergroup    =    constant($usergroup);
        
        if(!filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('invalid_username'));
            return false;
        }
        
        $required    =    [
            (!empty($data['username'])),
            (!empty($data['password'])),
            (!empty($data['first_name'])),
            (!empty($data['last_name']))
        ];
        
        if(array_sum($required) < 4) {
            $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('required'));
            return false;
        }
        
        if($this->userExists($username)) {
            $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('fail_userexists'));
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
                    date('YmdHis').rand(1000000,9999999),
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
        
        if($autologin) {
            if($this->userExists($username)){
                $this->getHelper('System')->login($username, $password);
                $this->redirect($this->getPage('full_path'));
            }
        }
    }
    
    public    function hashPassword($password, $cost = 9)
    {
        $passcost            =    (defined('NBR_PASS_COST') && is_numeric(NBR_PASS_COST))? NBR_PASS_COST : $cost;
        return password_hash($password, PASSWORD_DEFAULT, ['cost' => $passcost]);
    }
}