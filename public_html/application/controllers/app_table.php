<?php defined('BASEPATH') OR exit('No direct script access allowed');

class app_table extends MY_Controller
{
    public $model = 'app_table_m';
    public $load_model = TRUE;
    public $messages = array();
    public $rules = array(
        array(
            'field'=>'name',
            'label'=>'Name',
            'rules'=>'trim|required|min_length[1]|max_length[32]|alpha'
        )
    );

    public function table($message = FALSE)
    {
        $app_tables = $this->app_table_m->get();
        $this->twig->render('admin/table.twig', array(
            'title'=>'Listing Apps',
            'message'=>$message,
            'resource'=>'app_table',
            'table_name'=>'App Tables',
            'table'=>$app_tables
        ));
    }

    public function create()
    {
        $this->form_validation->set_rules($this->rules);
        $create = $this->form_validation->run();
        $message = ($create) ? 'App Table created with ID: ' . $this->app_table_m->create($this->app_table_m->prep()) : $create;
        $this->table($message);
    }

    public function read($id = FALSE)
    {
        if($id || create_new())
        {
            $data = (create_new()) ? $this->app_table_m->get_new() : $this->app_table_m->get_where($id);
            $method = (create_new()) ? 'post' : 'put';
            $this->twig->render('admin/edit.twig', array(
                'title'=>'Edit App Table',
                'table_name'=>'App Tables',
                'resource'=>'app_table',
                'heading'=>'name',
                'method'=>$method,
                'field_data'=>$this->app_table_m->field_data(),
                'data'=>$data
            ));
        }else{
            $this->table();
        }
    }

    public function update($id = FALSE)
    {
        $update = TRUE;
        $message = ($update) ? 'App Table updated! Affected rows: ' . $this->app_table_m->update($id, $this->app_table_m->prep()) : $update;
        $this->table($message);
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
        $message = ($delete) ? 'App Table deleted! Affected rows: ' . $this->app_table_m->delete($id, $this->app_table_m->prep()) : $delete;
        $this->table($message);
    }

    public function can_delete($str)
    {
        //TODO deny if app table has rows
        return TRUE;
    }
}