<?php defined('BASEPATH') OR exit('No direct script access allowed');

class api extends MY_Controller
{
    public $model = 'api_m';
    public $load_model = TRUE;
    public $message = '';

    public function index($id = FALSE)
    {
        $this->message = 'API index';
        echo $this->message;
    }

    public function error()
    {
        $this->message = 'Error: invalid API credentials';
        echo $this->message;
    }
}