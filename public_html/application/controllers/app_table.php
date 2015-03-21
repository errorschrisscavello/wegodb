<?php defined('BASEPATH') OR exit('No direct script access allowed');

class App_table extends MY_Controller
{
    public $model = 'App_table_m';
    public $load_model = TRUE;
    public $message = '';
    public $errors = array();
    public $rules = array(
        array(
            'field'=>'name',
            'label'=>'Name',
            'rules'=>'trim|required|min_length[1]|max_length[32]|callback_valid_name'
        )
    );

    public function listing()
    {
        $this->set_message();
        $listing = $this->App_table_m->listing();
        $this->twig->render('admin/listing.twig', array(
            'title'=>'Listing App Tables',
            'heading'=>icon('th-large') . ' App Tables',
            'resource'=>'app_table',
            'message'=>$this->message,
            'listing'=>$listing,
            'new'=>anchor(base_url('app_table?new=1'), icon('plus') . ' New app table')
        ));
    }

    public function create()
    {
        $this->form_validation->set_rules($this->rules);
        $create = $this->form_validation->run();
        if($create)
        {
            $this->message = 'App Table created with ID: ' . $this->App_table_m->create();
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
            $form = $this->App_table_m->form($id, $create_new);
            $this->twig->render('admin/edit.twig', array(
                'title'=>'Edit App Table',
                'heading'=>icon('th-large') . ' App Tables',
                'resource'=>'app_table',
                'form'=>$form
            ));
        }else{
            $this->listing();
        }
    }

    public function update($id = FALSE)
    {
        $this->form_validation->set_rules($this->rules);
        $update = $this->form_validation->run();
        if($update)
        {
            $this->message = ($update) ? 'App Table updated! Affected rows: ' . $this->App_table_m->update($id) : $update;
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
                'label'=>'App Table',
                'rules'=>'required|callback_can_delete'
            )
        ));
        $delete = $this->form_validation->run();
        $this->message = ($delete) ? 'App Table deleted! Affected rows: ' . $this->App_table_m->delete($id) : $delete;
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
        $app_id = $this->input->post('app');
        $ci =& get_instance();
        $ci->load->model('App_m');
        $app = $ci->App_m->get_where($app_id);
        $app_tables = $this->App_table_m->get_all_where('app_id', $app_id);
        $count = 0;
        if($app_tables)
        {
            foreach($app_tables as $app_table)
            {
                if($app_table->name == $str)
                {
                    $count++;
                }
            }
            if($count > 0)
            {
                $this->form_validation->set_message('valid_name', 'Table "' . $str . '" already exists in App: "' . $app->name . '"');
                return FALSE;
            }
        }
        return TRUE;
    }

    public function can_delete($str)
    {
        $ci =& get_instance();
        $ci->load->model('App_column_m');
        $app_columns = $ci->App_column_m->get_all_where('app_table_id', $str);
        if($app_columns)
        {
            $this->form_validation->set_message('can_delete', 'Cannot delete table with existing columns');
            return FALSE;
        }
        return TRUE;
    }
}