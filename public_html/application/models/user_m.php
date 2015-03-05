<?php defined('BASEPATH') OR exit('No direct script access allowed');

class user_m extends MY_Model
{
    public $table = 'users';
    public $post_filter = array(
        'username'=>'username',
        'email'=>'email',
        'password'=>'confirm_password'
    );

    public function get()
    {
        $users = parent::get();
        return $this->unset_password($users);
    }

    public function get_where($id)
    {
        $user = parent::get_where($id);
        return $this->unset_password($user);
    }

    public function get_new()
    {
        return $this->unset_password(parent::get_new());
    }

    public function prep()
    {
        $data = parent::prep();
        if(isset($data['password']))
        {
            $data['password'] = $this->auth->hash($data['password']);
        }
        return $data;
    }

    public function field_data()
    {
        $field_data = parent::field_data();
        $filtered = array();
        foreach($field_data as $field)
        {
            if($field->name != 'password' && $field->name != 'active')
            {
                $filtered[] = $field;
            }
        }
        $filtered[] = (object)array(
            'name'=>'confirm_email',
            'type'=>'varchar',
            'max_length'=>'128',
            'primary_key'=>0,

            'input'=>'text'
        );
        $filtered[] = (object)array(
            'name'=>'new_password',
            'type'=>'varchar',
            'max_length'=>'128',
            'primary_key'=>0,

            'input'=>'password'
        );
        $filtered[] = (object)array(
            'name'=>'confirm_password',
            'type'=>'varchar',
            'max_length'=>'128',
            'primary_key'=>0,

            'input'=>'password'
        );
        return $this->sort_fields($filtered, array(
            'username',
            'email',
            'confirm_email',
            'new_password',
            'confirm_password'
        ));
    }

    public function unset_password($result)
    {
        if($result)
        {
            if(is_array($result))
            {
                foreach($result as $user)
                {
                    unset($user->password);
                }
            }else{
                unset($result->password);
            }
        }
        return $result;
    }
}