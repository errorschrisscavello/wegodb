<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User_m extends MY_Model
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

    public function get_where($id, $value = FALSE)
    {
        $user = parent::get_where($id);
        return $this->unset_password($user);
    }

    public function get_new()
    {
        return $this->unset_password(parent::get_new());
    }

    public function form($id = FALSE, $new = FALSE)
    {
        $user = FALSE;
        if($id)
        {
            $user = $this->get_where($id);
        }
        ob_start();
        if($user || $new)
        {
            $action = ($id) ? base_url('user/' . $id) : base_url('user');
            $username = ($user) ? $user->username : set_value('username');
            $email = ($user) ? $user->email : set_value('email');

            $heading = ($new) ? 'Creating new user' : 'Editing user: ' . $username;
            echo '<h2>' . $heading . '</h2>';

            echo form_open($action);
            ! $user || rest_method_input('put');

            echo form_group_open();
            echo form_label('Username', 'username');
            echo form_input(array(
                'id'=>'username',
                'name'=>'username',
                'value'=>$username
            ), '', form_control());
            echo form_group_close();

            echo form_group_open();
            echo form_label('Email', 'email');
            echo form_input(array(
                'id'=>'email',
                'name'=>'email',
                'value'=>$email
            ), '', form_control());
            echo form_group_close();

            echo form_group_open();
            echo form_label('Confirm Email', 'confirm_email');
            echo form_input(array(
                'id'=>'confirm_email',
                'name'=>'confirm_email'
            ), '', form_control());
            echo form_group_close();

            echo form_group_open();
            echo form_label('New Password', 'new_password');
            echo form_password(array(
                'id'=>'new_password',
                'name'=>'new_password'
            ), '', form_control());
            echo form_group_close();

            echo form_group_open();
            echo form_label('Confirm Password', 'confirm_password');
            echo form_password(array(
                'id'=>'confirm_password',
                'name'=>'confirm_password',
            ), '', form_control());
            echo form_group_close();

            echo form_submit('submit', 'Submit');
            echo form_close();
        }else{
            echo '<p>User not found</p>';
        }
        return ob_get_clean();
    }

    public function listing()
    {
        $users = $this->get();
        ob_start();
        ?>
        <?php if($users): ?>
            <table class="table">
                <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Active</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($users as $user): ?>
                    <tr>
                        <td><?php echo anchor(base_url('user/' . $user->id), 'Edit'); ?></td>
                        <td><?php echo form_delete('user', $user->id); ?></td>
                        <td><?php echo $user->username; ?></td>
                        <td><?php echo $user->email; ?></td>
                        <?php
                        if( ! (bool)$user->active)
                        {
                            $ci =& get_instance();
                            $user->active = $ci->auth->activation_form($user->email);
                        }else{
                            $user->active = 'true';
                        }
                        ?>
                        <td><?php echo $user->active; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No users found</p>
        <?php endif; ?>
        <?php
        return ob_get_clean();
    }

    public function filter_post()
    {
        $data = parent::filter_post();
        if(isset($data['password']))
        {
            $data['password'] = $this->auth->hash($data['password']);
        }
        return $data;
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