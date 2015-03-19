<?php defined('BASEPATH') OR exit('No direct script access allowed');

class app extends MY_Controller
{
    public $model = 'app_m';
    public $load_model = TRUE;
    public $message = '';
    public $errors = array(
        'is_unique'=>'{field} is already used for an existing app'
    );
    public $rules = array(
        array(
            'field'=>'name',
            'label'=>'Name',
            'rules'=>'trim|required|min_length[1]|max_length[32]|is_unique[apps.name]|alpha_dash'
        )
    );

    public function listing()
    {
        $this->set_message();
        $listing = $this->app_m->listing();
        $this->twig->render('admin/listing.twig', array(
            'title'=>'Listing Apps',
            'heading'=>icon('phone') . ' Apps',
            'resource'=>'app',
            'message'=>$this->message,
            'listing'=>$listing,
            'new'=>anchor(base_url('app?new=1'), icon('plus') . ' New app')
        ));
    }

    public function create()
    {
        $this->form_validation->set_rules($this->rules);
        $create = $this->form_validation->run();
        if($create)
        {
            $this->message = 'App created with ID: ' . $this->app_m->create();
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
            $form = $this->app_m->form($id, $create_new);
            $this->twig->render('admin/edit.twig', array(
                'title'=>'Edit App',
                'heading'=>icon('phone') . ' Apps',
                'resource'=>'app',
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
            $this->message = ($update) ? 'App updated! Affected rows: ' . $this->app_m->update($id) : $update;
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
                'label'=>'App',
                'rules'=>'required|callback_can_delete'
            )
        ));
        $delete = $this->form_validation->run();
        $this->message = ($delete) ? 'App deleted! Affected rows: ' . $this->app_m->delete($id) : $delete;
        $this->listing();
    }

    public function can_delete($str)
    {
        $ci =& get_instance();
        $ci->load->model('app_table_m');
        $app_tables = $ci->app_table_m->get_all_where('app_id', $str);
        if($app_tables)
        {
            $this->form_validation->set_message('can_delete', 'Cannot delete app with existing tables');
            return FALSE;
        }
        return TRUE;
    }
}