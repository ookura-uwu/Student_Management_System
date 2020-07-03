<?php
class User 
{
    private $_db, 
            $_data,
            $_sessionName,
            $_cookieName, 
            $_isLoggedIn,
            $_isStudent;

    public function __construct($user = null) 
    {
        $this->_db = DB::getInstance();

        $this->_sessionName = Config::get('session/session_name');
        $this->_cookieName = Config::get('remember/cookie_name');

        if (!$user) 
        {
            if (Session::exists($this->_sessionName)) 
            {
                $user = Session::get($this->_sessionName);

                if ($this->find($user)) 
                {
                    $this->_isLoggedIn = true;
                } 
                else 
                {
                    // process Logout
                }
            }
        } 
        else 
        {
            $this->find($user);
        }
    }

    public function update($fields = array(), $id = null) 
    {

        if (!$id && $this->isLoggedIn()) 
        {
            $id = $this->data()->user_id;
        }

        if (!$this->_db->update('users', 'user_id', $id, $fields)) {
            throw new Exception('There was a problem updating your account.');
        }
    }

    public function create($fields = array()) 
    {
        if (!$this->_db->insert('users', $fields)) 
        {
            throw new Exception('There was a problem creating your account.');
        }
    }

    public function find($user = null) 
    {
        if ($user) 
        {
            $field = (is_numeric($user)) ? 'user_id' : 'username';
            $data = $this->_db->get('users', array($field, '=', $user));

            if ($data->count()) 
            {
                $this->_data = $data->first();
                return true;
            }
        }
        return false;
    }

    public function login($username = null, $password = null, $remember = false) 
    {
        
        if (!$username && !$password && $this->exists()) 
        {
            Session::put($this->_sessionName, $this->data()->user_id);
        } 
        else 
        {
            $user = $this->find($username);
            if ($user) 
            {
                if ($this->data()->password === Hash::make($password, $this->data()->salt)) 
                {
                    Session::put($this->_sessionName, $this->data()->user_id);

                    if ($remember) 
                    {
                        $hash = Hash::unique();
                        $hashCheck = $this->_db->get('users_session', array('user_id', '=', $this->data()->user_id));

                        if (!$hashCheck->count()) 
                        {
                            $this->_db->insert('users_session', array(
                                'user_id' => $this->data()->user_id,
                                'hash' => $hash
                            ));
                        } 
                        else 
                        {
                            $hash = $hashCheck->first()->hash;
                        }

                        Cookie::put($this->_cookieName, $hash, Config::get('remember/cookie_expiry'));
                    }
                    
                    if ($this->data()->group_id === 1) 
                    {
                        $this->_isStudent = false;
                    }

                    return true;
                }
            }
        }

        return false;
    }

    public function hasPermission($key) 
    {
        $group = $this->_db->get('groups', array('group_id', '=', $this->data()->group_id));

        if ($group->count()) 
        {
            $permissions = $group->first()->name;

            if ($permissions == $key) 
            {
                return true;
            }
        }
        return false;
    }

    public function exists() 
    {
        return (!empty($this->_data)) ? true : false;
    }

    public function logout() {
        $this->_db->delete('users_session', array('user_id', '=', $this->data()->user_id));
        Session::delete($this->_sessionName);
        Cookie::delete($this->_cookieName);
    }

    public function data() 
    {
        return $this->_data;
    }

    public function isLoggedIn() 
    {
        return $this->_isLoggedIn;
    }

    public function isStudent() 
    {
        return $this->_isStudent;
    }
}