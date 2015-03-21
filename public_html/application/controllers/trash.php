<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Trash extends MY_Controller
{
    public $model = 'Trash_m';
    public $load_model = TRUE;
    public $message = '';
    public $errors = array();
    public $rules = array(
        array(
            'field'=>'id',
            'label'=>'Item',
            'rules'=>'required|callback_can_restore'
        )
    );

    public function listing()
    {
        $this->set_message();
        $listing = $this->Trash_m->listing();
        $this->twig->render('admin/listing.twig', array(
            'title'=>'Listing Trash',
            'heading'=>icon('trash') . ' Trash',
            'resource'=>'trash',
            'message'=>$this->message,
            'listing'=>$listing,
            'new'=>''
        ));
    }

    public function create()
    {
        $this->form_validation->set_rules($this->rules);
        $create = $this->form_validation->run();
        $this->message = ($create) ? 'Item restored with ID: ' . $this->Trash_m->create() : 'Item not restored';
        $this->read();
    }

    public function read($id = FALSE, $new = FALSE)
    {
        $this->listing();
    }

    public function update($id = FALSE)
    {
        return FALSE;
    }

    public function delete($id = FALSE)
    {
        $delete = $id;
        $this->message = ($delete) ? 'Trash item deleted! Affected rows: ' . $this->Trash_m->delete($id) : 'Item not deleted';
        $this->read();
    }

    public function can_restore($str)
    {
        $item = $this->Trash_m->get_where($str);
        if($item)
        {
            $ci =& get_instance();
            if($ci->db->table_exists($item->source))
            {
                return TRUE;
            }
            $this->form_validation->set_message('can_restore', 'Trash item source table does not exist');
            return FALSE;
        }
        $this->form_validation->set_message('can_restore', 'Trash item does not exist');
        return FALSE;
    }
}