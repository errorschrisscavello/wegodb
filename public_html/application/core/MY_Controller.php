<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    public $model = '';
    public $load_model = FALSE;
    public $app_access = FALSE;
    public $rules = array();
    public $message = '';
    public $errors = array();
    public $public_uris = array(
        'api/invalid',
        'api/csrf',
        'error',
        'login',
        'logout'
    );

    function __construct()
    {
        parent::__construct();
        $valid_session = FALSE;
        $allowed_uri = in_array(uri_string(), $this->public_uris);
        if( ! $allowed_uri)
        {
            $valid_session = $this->auth->validate_session();
        }

        $is_api_request = $this->auth->is_api_request();

        if($allowed_uri || $valid_session && ! $is_api_request)
        {
            if(count($this->errors) > 0)
            {
                $this->set_errors();
            }

            if($this->load_model)
            {
                $this->load->model($this->model, $this->model, TRUE);
            }
        }elseif($is_api_request){

            if( ! $this->auth->validate_api())
            {
                //TODO send error message to api controller
                redirect('api/invalid');
            }
        }else{
            redirect('login');
        }
    }

    public function index($id = FALSE)
    {
        $id = (int)$id;
        if(is_post()) $this->create();
        if(is_get()) $this->read($id);
        if(is_put()) $this->update($id);
        if(is_delete()) $this->delete($id);
    }

    public function create(){}

    public function read($id = FALSE){}

    public function update($id = FALSE){}

    public function delete($id = FALSE){}

    public function get_rules_by_field($name)
    {
        $rules = $this->rules;
        foreach($rules as $rule)
        {
            if($name == $rule['field'])
            {
                return $rule;
            }
        }
    }

    public function set_errors()
    {
        $this->load->library('form_validation');

        foreach($this->errors as $func => $error)
        {
            $this->form_validation->set_message($func, $error);
        }
    }
}