<?php defined('BASEPATH') OR exit('No direct script access allowed');

class app extends MY_Controller
{
    public $model = 'app_m';
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
        $apps = $this->app_m->get();
        $this->twig->render('admin/table.twig', array(
            'title'=>'Listing Apps',
            'message'=>$message,
            'resource'=>'app',
            'table_name'=>'Apps',
            'table'=>$apps
        ));
    }

    public function create()
    {
        $this->form_validation->set_rules($this->rules);
        $create = $this->form_validation->run();
        $message = ($create) ? 'App created with ID: ' . $this->app_m->create($this->app_m->prep()) : $create;
        $this->table($message);
    }

    public function read($id = FALSE)
    {
        if($id || create_new())
        {
            $data = (create_new()) ? $this->app_m->get_new() : $this->app_m->get_where($id);
            $method = (create_new()) ? 'post' : 'put';
            $this->twig->render('admin/edit.twig', array(
                'title'=>'Edit App',
                'table_name'=>'Apps',
                'resource'=>'app',
                'heading'=>'name',
                'method'=>$method,
                'field_data'=>$this->app_m->field_data(),
                'data'=>$data
            ));
        }else{
            $this->table();
        }
    }

    public function update($id = FALSE)
    {
        $update = TRUE;
        $message = ($update) ? 'App updated! Affected rows: ' . $this->app_m->update($id, $this->app_m->prep()) : $update;
        $this->table($message);
    }

    public function delete($id = FALSE)
    {
        $this->form_validation->set_rules(array(
            array(
                'field'=>'id',
                'label'=>'App',
                'rules'=>'required|callback_can_delete'
            )
        ));
        $delete = $this->form_validation->run();
        $message = ($delete) ? 'App deleted! Affected rows: ' . $this->app_m->delete($id, $this->app_m->prep()) : $delete;
        $this->table($message);
    }

    public function can_delete($str)
    {
        //TODO deny if app has attached tables
        return TRUE;
    }
}