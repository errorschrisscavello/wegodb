<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends MY_Controller
{
    public $model = 'Api_m';
    public $load_model = TRUE;
    public $message = '';
    public $status = '';
    public $data = array();

    public function index($id = FALSE)
    {
        $this->status = 'success';
        $this->message = 'You have reached the WegoDB API Index';
        if(isset($_POST['action']))
        {
            if(isset($_POST['data']))
            {
                $_POST['data'] = json_decode($_POST['data']);
            }
            $this->Api_m->data = $_POST;
            $action = $this->Api_m->action();
            if(is_string($action))
            {
                $this->message = $action;
            }else{
                $this->message = 'Completed action: ' . $_POST['action'];
                $this->data = $action;
            }
        }
        $this->response();
    }

    public function csrf()
    {
        $this->status = 'success';
        $this->message = 'WegoDB CSRF token';
        $this->data = $this->auth->csrf();
        $this->response();
    }

    public function invalid()
    {
        $this->error('Invalid API credentials');
    }

    public function error($message = FALSE)
    {
        $this->status = 'error';
        $this->message = ($message) ? $message : 'An unknown error occurred';
        $this->response();
    }

    public function response()
    {
        $response = new stdClass();
        $response->data = $this->data;
        $response->message = $this->message;
        $response->status = $this->status;
        echo json_encode($response);
    }
}