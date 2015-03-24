<?php defined('BASEPATH') OR exit('No direct script access allowed');

class App_row extends MY_Controller
{
    public $model = 'App_row_m';
    public $load_model = TRUE;
    public $message = '';
    public $errors = array();
    public $rules = array();

    public function listing()
    {
        $this->set_message();
        $listing = $this->App_row_m->listing();
        $table = $this->input->get('app_table');
        $new = ($table) ? anchor(base_url('app_row?new=1&app_table=' . $table), icon('plus') . ' New app row') : '';
        $this->twig->render('admin/listing.twig', array(
            'title'=>'Listing App Rows',
            'heading'=>icon('th-list') . ' App Rows',
            'resource'=>'app_row',
            'message'=>$this->message,
            'listing'=>$listing,
            'new'=>$new
        ));
    }

    public function create()
    {
        $this->message = 'App Row created with ID: ' . $this->App_row_m->create();
        $this->listing();
    }

    public function read($id = FALSE, $new = FALSE)
    {
        $create_new = create_new() || $new;
        if($id || $create_new)
        {
            $form = $this->App_row_m->form($id, $create_new);
            $this->twig->render('admin/edit.twig', array(
                'title'=>'Edit App Row',
                'heading'=>icon('th-list') . ' App Rows',
                'resource'=>'app_row',
                'form'=>$form
            ));
        }else{
            $this->listing();
        }
    }

    public function update($id = FALSE)
    {
        $update = $id;
        if($update)
        {
            $this->message = ($update) ? 'App Row updated! Affected rows: ' . $this->App_row_m->update($id) : $update;
            $this->listing();
        }else{
            $this->read($id);
        }
    }

    public function delete($id = FALSE)
    {
        $delete = $id;
        $this->message = ($delete) ? 'App Row sent to Trash! Affected rows: ' . $this->App_row_m->delete($id) : $delete;
        $this->listing();
    }

    public function can_delete($str)
    {
        $row = $this->App_row_m->get_where($str);
        $ci =& get_instance();
        $ci->load->model('App_table_m');
        $app_table = $ci->App_table_m->get_where($row->app_table_id);
        $num_rows = $this->App_row_m->get_num_rows($app_table);
        if($num_rows != 0)
        {
            $this->form_validation->set_message('can_delete', 'Cannot delete row on table with existing rows');
            return FALSE;
        }
        return TRUE;
    }
}