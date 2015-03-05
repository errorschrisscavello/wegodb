<?php defined('BASEPATH') OR exit('No direct script access allowed');

class api extends MY_Controller
{
    public function index($id = FALSE)
    {
        echo 'API index';
    }

    public function error()
    {
        echo 'Error: invalid API credentials';
    }
}