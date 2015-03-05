<?php defined('BASEPATH') OR exit('No direct script access allowed');

class app_column_m extends MY_Model
{
    public $table = '';
    public $post_filter = array();
    public $app_m;
    public $app_table_m;

    function __construct()
    {
        $ci =& get_instance();
        $ci->load->model('app_m');
        $ci->load->model('app_table_m');
        $this->app_m =& $ci->app_m;
        $this->app_table_m =& $ci->app_table_m;
        parent::__construct();
    }

    public function has_link($app_table)
    {
        $link_name = linked_db_name($app_table);
        return $this->db->table_exists($link_name);
    }

    public function get_num_columns($app_table)
    {
        if($this->has_link($app_table))
        {
            $link_name = linked_db_name($app_table);
            return count($this->db->list_fields($link_name));
        }
        return 0;
    }

    public function get_num_rows($app_table)
    {
        if($this->has_link($app_table))
        {
            $link_name = linked_db_name($app_table);
            return count($this->db->get($link_name)->result());
        }
        return 0;
    }

    public function get()
    {
        $app_tables = $this->db->get('app_tables')->result();
        $tables = array();
        foreach($app_tables as $app_table)
        {
            $table = new stdClass();
            $table->id = $app_table->id;
            $app_id = (int)$app_table->app_id;
            $app_result = $this->app_m->get_where($app_id);
            $app_name = $app_result->name;
            $table->app_name = $app_name;
            $table->table_name = $app_table->name;
            $table->num_columns = $this->get_num_columns($app_table);
            $table->num_rows = $this->get_num_rows($app_table);
            $tables[] = $table;
        }
        return (count($tables) > 0) ? $tables : FALSE;
    }

    public function get_where($id)
    {
        //TODO return columns for specific table
        $table = $this->app_table_m->get_where($id)->name;
        $columns = $this->db->field_data('_' . $id);
        $data = array();
        $data['id'] = $id;
        $data['name'] = $table;
        foreach($columns as $column)
        {
            $d = new stdClass();
            $d->table = $table;
            $d->name = $column->name;
            $d->type = $column->type;
            $d->default = $column->default;
            $data[] = $d;
        }
        return $data;
    }

    public function get_new()
    {
        $new = parent::get_new();
        $new->name = '';
        return $new;
    }

    public function create($data)
    {
        $table = linked_db_name($data['table']);
        $name = $data['name'];
        $type = $data['type'];
        $default = $data['default'];

        $field_config = array();

        if($type == 'int')
        {
            $field_config['type'] = 'BIGINT';
            $field_config['constraint'] = 11;
            $field_config['default'] = (int)$default;
        }elseif($type == 'string'){
            $field_config['type'] = 'VARCHAR';
            $field_config['constraint'] = 65535;
        }elseif($type == 'text'){
            $field_config['type'] = 'TEXT';
        }else{
            $field_config['type'] = 'TINYINT';
            $field_config['default'] = (bool)$default;
        }

        $fields = array(
            $name=>$field_config
        );
        $this->dbforge->add_column($table, $fields);
        return ($this->db->field_exists($name, $table)) ? 'true' : 'false';
    }

    public function update($id, $data)
    {
        //TODO update column
        $this->db->where('id', $id);
        $this->db->update($this->table, $data);
        return $this->db->affected_rows();
    }

    public function delete($id)
    {
        //TODO delete column
        $this->db->where('id', $id);
        $this->db->delete($this->table);
        return $this->db->affected_rows();
    }

    public function field_data()
    {
        $app_tables = $this->db->get('app_tables')->result();
        $app_tables = ($app_tables) ? $app_tables : array();
        $app_table_names = array();
        foreach($app_tables as $app_table)
        {
            $app_table_names[] = array(
                'value'=>$app_table->id,
                'text'=>$app_table->name
            );
        }
        $field_data = array(
            (object)array(
                'name'=>'id',
                'input'=>'text'
            ),
            (object)array(
                'name'=>'table',
                'input'=>'select',
                'options'=>$app_table_names
            ),
            (object)array(
                'name'=>'name',
                'input'=>'text'
            ),
            (object)array(
                'name'=>'type',
                'input'=>'select',
                'options'=>array(
                    array(
                        'value'=>'bool',
                        'text'=>'Boolean'
                    ),
                    array(
                        'value'=>'int',
                        'text'=>'Integer'
                    ),
                    array(
                        'value'=>'string',
                        'text'=>'String'
                    ),
                    array(
                        'value'=>'text',
                        'text'=>'Text'
                    )
                )
            ),
            (object)array(
                'name'=>'default',
                'input'=>'text'
            )
        );
        return $field_data;
    }

    public function prep()
    {
        $table = $this->input->post('table');
        $name = $this->input->post('name');
        $type = $this->input->post('type');
        $default = $this->input->post('default');

        return array(
            'table'=>$table,
            'name'=>$name,
            'type'=>$type,
            'default'=>$default
        );
    }
}