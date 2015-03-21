<?php defined('BASEPATH') OR exit('No direct script access allowed');

class App_column extends MY_Controller
{
    public $model = 'App_column_m';
    public $load_model = TRUE;
    public $message = '';
    public $errors = array();
    public $rules = array(
        array(
            'field'=>'name',
            'label'=>'Column Name',
            'rules'=>'trim|required|callback_valid_name'
        ),
        array(
            'field'=>'type',
            'label'=>'Type',
            'rules'=>'trim|required'
        ),
        array(
            'field'=>'table',
            'label'=>'Table',
            'rules'=>'trim|required'
        )
    );

    public function listing()
    {
        $this->set_message();
        $listing = $this->App_column_m->listing();
        $this->twig->render('admin/listing.twig', array(
            'title'=>'Listing App Columns',
            'heading'=>icon('th') . ' App Columns',
            'resource'=>'app_column',
            'message'=>$this->message,
            'listing'=>$listing,
            'new'=>anchor(base_url('app_column?new=1'), icon('plus') . ' New app column')
        ));
    }

    public function create()
    {
        $this->form_validation->set_rules($this->rules);
        $create = $this->form_validation->run();
        if($create)
        {
            $this->message = 'App Column created with ID: ' . $this->App_column_m->create();
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
            $form = $this->App_column_m->form($id, $create_new);
            $this->twig->render('admin/edit.twig', array(
                'title'=>'Edit App Column',
                'heading'=>icon('th') . ' App Columns',
                'resource'=>'app_column',
                'form'=>$form
            ));
        }else{
            $this->listing();
        }
    }

    public function update($id = FALSE)
    {
        $this->message = 'App Columns may not be updated, only deleted';
        $this->listing();
    }

    public function delete($id = FALSE)
    {
        $this->form_validation->set_rules(array(
            array(
                'field'=>'id',
                'label'=>'App Column',
                'rules'=>'required|callback_can_delete'
            )
        ));
        $delete = $this->form_validation->run();
        $this->message = ($delete) ? 'App Column deleted! Affected rows: ' . $this->App_column_m->delete($id) : $delete;
        $this->listing();
    }

    public function valid_name($str)
    {
        $first_char = substr($str, 0, 1);
        if($is_number_first_char = preg_match("/\d/", $first_char))
        {
            $this->form_validation->set_message('valid_name', 'Table name cannot begin with a number');
            return FALSE;
        }
        if($has_illegal_chars = preg_match_all("/[^0-9a-zA-Z_]/", $str)){
            $this->form_validation->set_message('valid_name', 'Table name cannot characters other than letters, numbers, or underscores');
            return FALSE;
        }
        $app_table_id = $this->input->post('table');
        $ci =& get_instance();
        $ci->load->model('1', '1');
        $app_table = $ci->App_table_m->get_where($app_table_id);
        $link_name = linked_table_name($app_table);
        if($ci->db->field_exists($str, $link_name))
        {
            $this->form_validation->set_message('valid_name', 'Column "' . $str . '" already exists on table "' . $app_table->name . '"');
            return FALSE;
        }
        return TRUE;
    }

    public function can_delete($str)
    {
        $column = $this->App_column_m->get_where($str);
        $ci =& get_instance();
        $ci->load->model('1', '1');
        $app_table = $ci->App_table_m->get_where($column->app_table_id);
        $num_rows = $this->App_column_m->get_num_rows($app_table);
        if($num_rows != 0)
        {
            $this->form_validation->set_message('can_delete', 'Cannot delete column on table with existing rows');
            return FALSE;
        }
        return TRUE;
    }
}