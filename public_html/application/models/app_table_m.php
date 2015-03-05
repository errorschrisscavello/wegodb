<?php defined('BASEPATH') OR exit('No direct script access allowed');

class app_table_m extends MY_Model
{
    public $table = 'app_tables';
    public $post_filter = array(
        'name'=>'name',
        'app_id'=>'app_id'
    );

    function __construct()
    {
        parent::__construct();
    }

    public function create($data)
    {
        $id = parent::create($data);
        $link_name = linked_db_name($id);
        $this->dbforge->add_field(array(
            'id'=>array(
                'type'=>'INT',
                'constraint'=>11,
                'unsigned'=>TRUE,
                'auto_increment'=>TRUE
            )
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($link_name);
        //TODO create linked table
        return $id;
    }

    public function delete($id = FALSE)
    {
        //TODO delete linked table
        return parent::delete($id);
    }

    public function field_data()
    {
        $field_data = parent::field_data();
        $apps = $this->db->get('apps')->result();
        $app_options = array();
        foreach($apps as $app)
        {
            $app_options[] = array(
                'value'=>$app->id,
                'text'=>$app->name
            );
        }
        foreach($field_data as $field)
        {
            if($field->name == 'app_id')
            {
                $field->input = 'select';
                $field->options = $app_options;
            }
        }
        return $field_data;
    }
}