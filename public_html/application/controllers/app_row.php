<?php defined('BASEPATH') OR exit('No direct script access allowed');

class app_row extends MY_Controller
{
    public $model = 'app_row_m';
    public $load_model = TRUE;
    public $message = '';
    public $errors = array();
    public $rules = array();

    public function listing()
    {
        //TODO list rows
    }

    public function create()
    {
        //TODO list row
    }

    public function read($id = FALSE, $new = FALSE)
    {
        //TODO list or edit rows
    }

    public function update($id = FALSE)
    {
        //TODO update row
    }

    public function delete($id = FALSE)
    {
        //TODO delete rows
    }
}