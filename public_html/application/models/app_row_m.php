<?php defined('BASEPATH') OR exit('No direct script access allowed');

class app_row_m extends MY_Model
{
    public $app_m;
    public $app_table_m;
    public $app_column_m;

    function __construct()
    {
        $ci =& get_instance();
        $ci->load->model('app_m');
        $ci->load->model('app_table_m');
        $ci->load->model('app_column_m');
        $this->app_table_m =& $ci->app_m;
        $this->app_table_m =& $ci->app_table_m;
        $this->app_column_m =& $ci->app_column_m;
        parent::__construct();
    }

    public function form($id = FALSE, $new = FALSE)
    {
        //TODO return dynamic for to create new row
        return '';
    }

    public function listing()
    {
        //TODO return list of rows for table
        return '';
    }
}