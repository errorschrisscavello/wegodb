<?php defined('BASEPATH') OR exit('No direct script access allowed');

class user extends MY_Controller
{
    public $model = 'user_m';
    public $load_model = TRUE;
    public $message = '';
    public $errors = array(
        'is_unique'=>'{field} already exists'
    );
    public $rules = array(
        array(
            'field'=>'username',
            'label'=>'Username',
            'rules'=>'trim|required|min_length[8]|max_length[32]|is_unique[users.username]|alpha_dash'
        ),
        array(
            'field'=>'email',
            'label'=>'Email',
            'rules'=>'trim|required|is_unique[users.email]|valid_email'
        ),
        array(
            'field'=>'confirm_email',
            'label'=>'Confirm Email',
            'rules'=>'trim|required|matches[email]'
        ),
        array(
            'field'=>'new_password',
            'label'=>'New Password',
            'rules'=>'trim|required|min_length[8]|max_length[32]|alpha_dash'
        ),
        array(
            'field'=>'confirm_password',
            'label'=>'Confirm Password',
            'rules'=>'trim|required|matches[new_password]'
        )
    );

    public function listing()
    {
        $this->set_message();
        $listing = $this->user_m->listing();
        $this->twig->render('admin/listing.twig', array(
            'title'=>'Listing Users',
            'heading'=>icon('user') . ' Users',
            'resource'=>'user',
            'message'=>$this->message,
            'listing'=>$listing,
            'new'=>anchor(base_url('user?new=1'), icon('plus') . ' New user')
        ));
    }

    public function create()
    {
        $this->form_validation->set_rules($this->rules);
        $create = $this->form_validation->run();
        if($create)
        {
            $this->message = 'User created with ID: ' . $this->user_m->create();
            $this->message .= ' Check your email for an activation link!';
            $this->send_activation_email();
            $this->listing();
        }else{
            $this->read(FALSE, TRUE);
        }
    }

    public function read($id = FALSE, $new = FALSE)
    {
        $create_new = create_new() || $new;
        if($id || $create_new)
        {
            $form = $this->user_m->form($id, $create_new);
            $this->twig->render('admin/edit.twig', array(
                'title'=>'Edit User',
                'heading'=>icon('user') . ' Users',
                'resource'=>'user',
                'form'=>$form,
                'nav'=>anchor(base_url('user'), 'Back to Users')
            ));
        }else{
            $this->listing();
        }
    }

    public function update($id = FALSE)
    {
        $update = $this->can_update($id);
        if($update)
        {
            $this->message = ($update) ? 'User updated! Affected rows: ' . $this->user_m->update($id) : $update;
            $this->listing();
        }else{
            $this->read($id);
        }
    }

    public function delete($id = FALSE)
    {
        $this->form_validation->set_rules(array(
            array(
                'field'=>'id',
                'label'=>'User',
                'rules'=>'required|callback_can_delete'
            )
        ));
        $delete = $this->form_validation->run();
        $this->message = ($delete) ? 'User deleted! Affected rows: ' . $this->user_m->delete($id) : $delete;
        $this->listing();
    }

    public function activate()
    {
        $token = $this->input->get('token');
        $email = $this->input->get('email');
        $this->message = 'User not activated, resend activation email?';
        $this->message .= $this->auth->activation_form(urldecode($email));
        if($this->auth->user_exists($email))
        {
            if($this->auth->validate_activation($token, $email))
            {
                $this->auth->activate($email);
                $this->message = 'User activated!';
            }
        }
        $this->load->model('admin_m');
        $this->twig->render('public/login.twig', array(
            'message'=>$this->message,
            'form'=>$this->admin_m->form()
        ));
    }

    public function resend_activation()
    {
        $this->send_activation_email();
        $this->message = 'Activation email sent';
        $this->listing();
    }

    public function send_activation_email()
    {
        $username = $this->input->post('username');
        $email = $this->input->post('email');

        $activation_link = $this->auth->activation_link($email);

        $this->email->from('mail@wegodb.com', 'WegoDB');
        $this->email->to($email);

        $this->email->subject('WegoDB [ New User "' . $username . '" Created ], please activate!');
        $this->email->message("
            WegoDB

            Hello $username,
            A new user was created with the following credentials:

            username: $username
            email: $email

            Please save this information in a safe place and activate your account by clicking the link below.

            $activation_link
        ");

        $this->email->send();
    }

    public function can_update($id)
    {
        //TODO better method for conditional field updates
        $can_update = FALSE;
        if($id)
        {
            if($user = $this->user_m->get_where($id))
            {
                $rules = array();
                $username = $this->input->post('username');
                $email = $this->input->post('email');
                $confirm_email = $this->input->post('confirm_email');
                $new_password = $this->input->post('new_password');
                $confirm_password = $this->input->post('confirm_password');
                $this->user_m->post_filter = array();
                if($username != $user->username)
                {
                    $rules[] = $this->get_rules_by_field('username');
                    $this->user_m->post_filter['username'] = 'username';
                }
                if($email != $user->email || $confirm_email != '')
                {
                    $rules[] = $this->get_rules_by_field('email');
                    $rules[] = $this->get_rules_by_field('confirm_email');
                    $this->user_m->post_filter['email'] = 'email';
                }
                if($new_password != '' || $confirm_password != '')
                {
                    $rules[] = $this->get_rules_by_field('new_password');
                    $rules[] = $this->get_rules_by_field('confirm_password');
                    $this->user_m->post_filter['password'] = 'confirm_password';
                }
                $this->form_validation->set_rules($rules);
                $can_update = $this->form_validation->run();
            }
        }
        return $can_update;
    }

    public function can_delete($str)
    {
        $user = $this->user_m->get_where($str);
        if($user)
        {
            $username = $user->username;
            $is_current = $this->auth->is_current($username);
            $is_sole = (count($this->user_m->get()) == 1);
            if($is_current || $is_sole)
            {
                ! $is_current || $this->form_validation->set_message('can_delete', 'Cannot delete currently logged in user');
                ! $is_sole || $this->form_validation->set_message('can_delete', 'Cannot delete only existing user');
                return FALSE;
            }
        }
        return TRUE;
    }
}