<?php defined('BASEPATH') OR exit('No direct script access allowed');

class app_column extends MY_Controller
{
    public $model = 'app_column_m';
    public $load_model = TRUE;
    public $messages = array();
    public $rules = array(
        array(
            'field'=>'table',
            'label'=>'Table',
            'rules'=>'trim|required'
        ),
        array(
            'field'=>'type',
            'label'=>'Type',
            'rules'=>'trim|required'
        )
    );

    public function table($message = FALSE)
    {
        $app_columns = $this->app_column_m->get();
        $this->twig->render('admin/table.twig', array(
            'title'=>'Listing Apps',
            'message'=>$message,
            'resource'=>'app_column',
            'table_name'=>'App Columns',
            'table'=>$app_columns
        ));
    }

    public function create()
    {
        //TODO check if column name exists before creation
        $this->form_validation->set_rules($this->rules);
        $create = $this->form_validation->run();
        $message = ($create) ? 'App Column created: ' . $this->app_column_m->create($this->app_column_m->prep()) : $create;
        $this->table($message);
    }

    public function read($id = FALSE)
    {
        if($id || create_new())
        {
            $data = (create_new()) ? $this->app_column_m->get_new() : $this->app_column_m->get_where($id);
            $method = (create_new()) ? 'post' : 'put';
            $this->twig->render('admin/edit.twig', array(
                'title'=>'Edit App Column',
                'table_name'=>'App Columns',
                'resource'=>'app_column',
                'heading'=>'name',
                'method'=>$method,
                'field_data'=>$this->app_column_m->field_data(),
                'data'=>$data,
                'multi'=>($id) ? TRUE : FALSE
            ));
        }else{
            $this->table();
        }
    }

    public function update($id = FALSE)
    {
        $update = TRUE;
        $message = ($update) ? 'App Column updated! Affected rows: ' . $this->app_column_m->update($id, $this->app_column_m->prep()) : $update;
        $this->table($message);
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
        $message = ($delete) ? 'App Column deleted! Affected rows: ' . $this->app_column_m->delete($id, $this->app_column_m->prep()) : $delete;
        $this->table($message);
    }

    public function can_delete($str)
    {
        //TODO deny if app column has rows
        return TRUE;
    }
}