<?php defined('BASEPATH') OR exit('No direct script access allowed');

class admin extends MY_Controller
{
    public $model = 'admin_m';
    public $load_model = TRUE;
    public $message = '';
    public $errors = array();

    public $rules = array(
        array(
            'field'=>'user',
            'label'=>'Username or Email',
            'rules'=>'trim|required|callback_valid_user'
        ),
        array(
            'field'=>'password',
            'label'=>'Password',
            'rules'=>'trim|required|callback_valid_password'
        )
    );

    public function dashboard()
    {
        $this->twig->render('admin/dashboard.twig');
    }

    public function login()
    {
        $authorized = FALSE;
        if(is_post())
        {
            $this->form_validation->set_rules($this->rules);

            $authorized = $this->form_validation->run();
        }

        if($authorized)
        {
            $this->auth->session_create();
            redirect('dashboard');
        }else{
            $this->twig->render('public/login.twig', array(
                'form'=>$this->admin_m->form()
            ));
        }
    }

    public function logout()
    {
        $this->auth->session_destroy();
        redirect('login');
    }

    public function valid_user($str)
    {
        if( ! $this->auth->user_exists($str))
        {
            $this->form_validation->set_message('valid_user', 'User: "' . $str . '"" does not exist');
            return FALSE;
        }elseif( ! $this->auth->is_active($str)){
            $this->form_validation->set_message('valid_user', 'User: "' . $str . '"" is not activated');
            return FALSE;
        }
        return TRUE;
    }

    public function valid_password($str)
    {
        $user = $this->input->post('user');

        if( ! $this->auth->password_matches($user, $str))
        {
            $this->form_validation->set_message('valid_password', 'Password did not match that user');
            return FALSE;
        }
        return TRUE;
    }
}