<?php defined('BASEPATH') OR exit('No direct script access allowed');

class app_row_m extends MY_Model
{
    public $app_m;
    public $app_table_m;
    public $app_column_m;

    function __construct()
    {
        $ci =& get_instance();
        $ci->load->model('app_m');
        $ci->load->model('app_table_m');
        $ci->load->model('app_column_m');
        $this->app_m =& $ci->app_m;
        $this->app_table_m =& $ci->app_table_m;
        $this->app_column_m =& $ci->app_column_m;
        $this->set_table();
        parent::__construct();
    }

    public function set_table()
    {
        $id = FALSE;
        $get = $this->input->get('app_table');
        $post = $this->input->post('app_table');
        if($get)
        {
            $id = $get;
        }elseif($post){
            $id = $post;
        }
        if($id)
        {
            $app_table = $this->app_table_m->get_where($id);
            $link_name = linked_table_name($app_table);
            $this->table = $link_name;
        }
    }

    public function is_set_table()
    {
        return ($this->table != '');
    }

    public function get()
    {
        if($this->is_set_table())
        {
            return parent::get();
        }
        return FALSE;
    }

    public function get_where($id, $value = FALSE)
    {
        if($this->is_set_table())
        {
            return parent::get_where($id, $value);
        }
        return FALSE;
    }

    public function get_all_where($key, $value)
    {
        if($this->is_set_table())
        {
            return parent::get_all_where($key, $value);
        }
        return FALSE;
    }

    public function get_new()
    {
        if($this->is_set_table())
        {
            return parent::get_new();
        }
        return FALSE;
    }

    public function create()
    {
        if($this->is_set_table())
        {
            return parent::create();
        }
        return FALSE;
    }

    public function update($id)
    {
        if($this->is_set_table())
        {
            return parent::update($id);
        }
        return FALSE;
    }

    public function delete($id)
    {
        if($this->is_set_table())
        {
            $item = $this->get_where($id);
            if($item)
            {
                move_to_trash($this->table, $item);
            }
            return parent::delete($id);
        }
        return FALSE;
    }

    public function columns_to_post_filter($app_columns)
    {
        $post_filter = array();
        foreach($app_columns as $column)
        {
            $column_name = $column->name;
            $post_filter[$column_name] = $column_name;
        }
        return $post_filter;
    }

    public function filter_post()
    {
        $table_id = $this->input->post('app_table');
        $app_columns = $this->app_column_m->get_columns('table_id', $table_id);
        $this->post_filter = ($app_columns) ? $this->columns_to_post_filter($app_columns): array();
        return parent::filter_post();
    }

    public function form($id = FALSE, $new = FALSE)
    {
        $table_id = $this->input->get('app_table');
        ob_start();
        if($app_table = $this->app_table_m->get_where($table_id))
        {
            if($app_columns = $this->app_column_m->get_columns('table_id', $table_id))
            {
                $app_row = FALSE;
                if($id)
                {
                    $app_row = $this->get_where($id);
                }
                if($app_row || $new)
                {
                    $heading = ($new) ? 'Creating new app row on table: ' . $app_table->name : 'Editing app row with ID: ' . $id . ' from table: ' . $app_table->name;
                    echo '<h2>' . $heading . '</h2>';
                    $action = ($id) ? base_url('app_row/' . $id) : base_url('app_row');
                    $action .= '?app_table=' . $app_table->id;
                    echo form_open($action);
                    ! $app_row || rest_method_input('put');
                    echo form_hidden('app_table', $app_table->id);
                    foreach($app_columns as $column)
                    {
                        if($column->name != 'id')
                        {
                            $column_name = $column->name;
                            echo form_group_open();
                            echo form_label(field_to_label($column_name), $column->name);
                            echo form_input(array(
                                'id'=>$column_name,
                                'name'=>$column_name,
                                'value'=>($app_row) ? $app_row->$column_name : set_value($column->name, '')
                            ), '', form_control());
                            echo form_group_close();
                        }
                    }
                    echo form_submit('submit', 'Submit');
                    echo form_close();
                    echo '<p>' . anchor(base_url('app_row?app_table=' . $app_table->id), 'Back to App Rows for Table: ' . $app_table->name) . '</p>';
                }else{
                    echo '<p>App row not found</p>';
                }
            }else{
                echo '<p>No columns exist on table : ' . $app_table->name . '</p>';
            }
        }else{
            echo '<p>That table does not exist</p>';
        }
        echo '<p>' . anchor(base_url('app_row'), 'Back to App Rows') . '</p>';
        return ob_get_clean();
    }

    public function listing()
    {
        $app_table = FALSE;
        $app_tables = FALSE;
        $app_rows = $this->get();
        if(isset($_GET['app_table']))
        {
            $app_table = table_from_link($this->table);
            $sub_heading = 'Listing rows from app table: ' . $app_table->name;
        }else{
            $app_tables = $this->app_table_m->get();
            $sub_heading = 'Please select an app table to display it\'s rows';
        }
        ob_start();
        ?>
        <h2><?php echo $sub_heading; ?></h2>
        <?php if($app_table): ?>
            <?php if($app_rows): ?>
                <table class="table">
                    <?php $columns = $this->app_column_m->get_columns('table_id', $app_table->id); ?>
                    <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>ID</th>
                        <?php foreach($columns as $column): ?>
                            <th><?php echo $column->name; ?></th>
                        <?php endforeach; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($app_rows as $app_row):?>
                        <tr>
                            <td><?php echo anchor(base_url('app_row/' . $app_row->id . '?app_table=' . $app_table->id), 'Edit'); ?></td>
                            <td><?php echo form_delete('app_row', $app_row->id . '?app_table=' . $app_table->id, 'Trash'); ?></td>
                            <td><?php echo $app_row->id; ?></td>
                            <?php foreach($columns as $column): ?>
                                <?php $column_name = $column->name; ?>
                                <td><?php echo $app_row->$column_name; ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No rows exist on table: <?php echo $app_table->name; ?>. Create a new row!</p>
        <?php endif; ?>
        <?php elseif($app_tables): ?>
            <?php $apps = $this->app_m->get(); ?>
            <?php foreach($apps as $app): ?>
                <?php $app_tables = $this->app_table_m->get_all_where('app_id', $app->id); ?>
                <h3><?php echo icon('phone'); ?> App: <?php echo $app->name; ?></h3>
                <ul class="list-group">
                    <?php foreach($app_tables as $app_table): ?>
                        <li class="list-group-item"><?php echo anchor(base_url('app_row?app_table=' . $app_table->id), icon('th-large') . ' ' . $app_table->name); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No tables found. Create a table and add some columns and rows!</p>
        <?php endif; ?>
        <p><?php echo anchor(base_url('app'), 'Back to Apps'); ?></p>
        <p><?php echo anchor(base_url('app_table'), 'Back to App Tables'); ?></p>
        <p><?php echo anchor(base_url('app_column'), 'Back to App Columns'); ?></p>
        <?php
        return ob_get_clean();
    }
}