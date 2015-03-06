<?php defined('BASEPATH') OR exit('No direct script access allowed');

class app_column_m extends MY_Model
{
    public $table = 'app_columns';
    public $post_filter = array(
        'name'=>'name',
        'app_table_id'=>'table'
    );
    public $app_m;
    public $app_table_m;
    public $types = array(
        'bool',
        'int',
        'float',
        'string',
        'text'
    );

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
        $link_name = linked_table_name($app_table);
        return $this->db->table_exists($link_name);
    }

    public function get_num_columns($app_table)
    {
        if($this->has_link($app_table))
        {
            $link_name = linked_table_name($app_table);
            $fields = $this->db->list_fields($link_name);
            return count($fields);
        }
        return 0;
    }

    public function get_num_rows($app_table)
    {
        if($this->has_link($app_table))
        {
            $link_name = linked_table_name($app_table);
            return count($this->db->get($link_name)->result());
        }
        return 0;
    }


    public function get_columns($key = FALSE, $value = FALSE)
    {
        $app_tables = $this->app_table_m->get();
        $columns = array();
        if($app_tables)
        {
            foreach($app_tables as $app_table)
            {
                $app_columns = $this->get_all_where('app_table_id', $app_table->id);
                if($app_columns)
                {
                    foreach($app_columns as $app_column)
                    {
                        $column = new stdClass();
                        $column->id = $app_column->id;
                        $column->name = $app_column->name;
                        $link_name = linked_table_name($app_table);
                        $field_data = $this->db->field_data($link_name);
                        $field = get_field($column->name, $field_data);
                        $type = ($field) ? $field->type : '';
                        $default = ($field) ? $field->default : '';
                        $column->type = $type;
                        $column->default = $default;
                        $column->num_rows = $this->get_num_rows($app_table);
                        $column->table_name = $app_table->name;
                        $column->table_id = $app_table->id;
                        $app_id = $app_table->app_id;
                        $app = $this->app_m->get_where($app_id);
                        $column->app_id = $app_id;
                        $column->app_name = $app->name;
                        $can_append = TRUE;
                        if($value)
                        {
                            $can_append = ($column->$key == $value);
                        }
                        if($can_append)
                        {
                            $columns[] = $column;
                        }

                    }
                }
            }
        }
        return (count($columns) > 0) ? $columns : FALSE;
    }

    public function get_new()
    {
        $new = parent::get_new();
        $new->name = '';
        return $new;
    }

    public function create()
    {
        $data = $this->filter_post();
        $data['table'] = $this->input->post('table');
        $data['type'] = $this->input->post('type');
        $data['default'] = $this->input->post('default');
        $app_table = $this->app_table_m->get_where($data['table']);
        $link_name = linked_table_name($app_table);
        $name = $data['name'];
        $type = $data['type'];
        $default = $data['default'];
        $field_config = array();
        if($type == 'int')
        {
            $field_config['type'] = 'BIGINT';
            $field_config['constraint'] = 11;
            $field_config['default'] = (int)$default;
        }elseif($type == 'float'){
            $field_config['type'] = 'DOUBLE';
            $field_config['default'] = (double)$default;
        }elseif($type == 'string'){
            $field_config['type'] = 'VARCHAR';
            $field_config['constraint'] = 255;
            $field_config['default'] = $this->db->escape($default);
        }elseif($type == 'text'){
            $field_config['type'] = 'TEXT';
        }else{
            $field_config['type'] = 'TINYINT';
            $field_config['default'] = (bool)$default;
        }
        $fields = array(
            $name=>$field_config
        );
        $this->dbforge->add_column($link_name, $fields);
        return parent::create();
    }

    public function update($id)
    {
        return FALSE;
    }

    public function delete($id)
    {
        $column = $this->get_where($id);
        $app_table = $this->app_table_m->get_where($column->app_table_id);
        $link_name = linked_table_name($app_table);
        $this->dbforge->drop_column($link_name, $column->name);
        return parent::delete($id);
    }

    public function form($id = FALSE, $new = FALSE)
    {
        //TODO disable input 'default' when type 'text' is selected with javascript
        $app_tables = $this->app_table_m->get();
        ob_start();
        if($app_tables)
        {
            $app_column = FALSE;
            if($id)
            {
                $app_column = $this->get_where($id);
            }
            if($app_column || $new)
            {
                $action = ($id) ? base_url('app_column/' . $id) : base_url('app_column');
                $app_column_name = ($app_column) ? $app_column->name : set_value('name');

                $heading = ($new) ? 'Creating new app column: ' : 'Editing app column: ' . $app_column_name;
                echo '<h2>' . $heading . '</h2>';

                echo form_open($action);
                ! $app_column || rest_method_input('put');
                echo form_label('App Column Name', 'name');
                echo form_input(array(
                    'id'=>'name',
                    'name'=>'name',
                    'value'=>$app_column_name
                ));
                echo form_label('Type', 'type');
                echo form_dropdown(array(
                    'id'=>'type',
                    'name'=>'type'
                ), array(
                    'bool'=>'Boolean',
                    'int'=>'Integer',
                    'float'=>'Float',
                    'string'=>'String',
                    'text'=>'Text'
                ), set_value('type', 'bool'));
                echo form_label('Default Value', 'default');
                echo form_input(array(
                    'id'=>'default',
                    'name'=>'default',
                    'value'=>set_value('default', '')
                ));
                $app_tables = $this->app_table_m->get();
                $options = array();
                $selected = $this->input->post('table');
                foreach($app_tables as $app_table)
                {
                    $options[$app_table->id] = $app_table->name;
                    if($app_column)
                    {
                        if($app_column->app_table_id == $app_table->id)
                        {
                            $selected = $app_table->id;
                        }
                    }
                }
                $data = array(
                    'id'=>'table',
                    'name'=>'table'
                );
                echo form_label('App Table', 'app_table');
                echo form_dropdown($data, $options, $selected);
                echo form_submit('submit', 'Submit');
                echo form_close();
            }else{
                echo '<p>App column not found</p>';
            }
            echo '<p>' . anchor(base_url('app_column'), 'Back to App Columns') . '</p>';
        }else{
            echo '<p>Cannot create column without existing tables</p>';
        }
        return ob_get_clean();
    }

    public function listing()
    {
        if($filtered = $app_id = $this->input->get('app'))
        {
            $app_columns = $this->get_columns('app_id', $app_id);
            $app = $this->app_m->get_where($app_id);
            $sub_heading = 'Listing columns for app table: ' . $app->name;
        }elseif($filtered = $app_table_id = $this->input->get('app_table')){
            $app_columns = $this->get_columns('table_id', $app_table_id);
            $app_table = $this->app_table_m->get_where($app_table_id);
            $sub_heading = 'Listing columns for app table: ' . $app_table->name;
        }else{
            $app_columns = $this->get_columns();
            $sub_heading = 'Listing all app columns';
        }
        if($app_columns)
        {
            is_array($app_columns) || $app_columns = array($app_columns);
            for($i = count($app_columns) - 1; $i >= 0;  $i--)
            {
                $app_column = $app_columns[$i];
                if($app_column->name == 'id')
                {
                    unset($app_columns[$i]);
                }
            }
            $app_columns = (count($app_columns) > 0) ? $app_columns : FALSE;
        }
        ob_start();
        ?>
        <h2><?php echo $sub_heading; ?></h2>
        <?php if($app_columns): ?>
        <table>
            <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th>Column Name</th>
                    <th>Column Type</th>
                    <th># Rows</th>
                    <th>Table Name</th>
                    <th>App Name</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($app_columns as $app_column): ?>
                <tr>
                    <td><?php echo form_delete('app_column', $app_column->id); ?></td>
                    <td><?php echo anchor(base_url('app_row/?app_table=' . $app_column->table_id), 'Rows'); ?></td>
                    <td><?php echo $app_column->name; ?></td>
                    <td><?php echo $app_column->type; ?></td>
                    <td><?php echo $app_column->num_rows; ?></td>
                    <td><?php echo $app_column->table_name; ?></td>
                    <td><?php echo $app_column->app_name; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>No columns found</p>
        <?php endif; ?>
        <p><?php echo anchor(base_url('app'), 'Back to Apps'); ?></p>
        <p><?php echo anchor(base_url('app_table'), 'Back to App Tables'); ?></p>
        <?php if($filtered): ?>
        <p><?php echo anchor(base_url('app_column'), 'Show all App Columns'); ?></p>
        <?php endif; ?>
        <?php
        return ob_get_clean();
    }
}