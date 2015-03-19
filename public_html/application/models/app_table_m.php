<?php defined('BASEPATH') OR exit('No direct script access allowed');

class app_table_m extends MY_Model
{
    public $table = 'app_tables';
    public $post_filter = array(
        'name'=>'name',
        'app_id'=>'app'
    );
    public $app_m;
    public $app_column_m;

    function __construct()
    {
        $ci =& get_instance();
        $ci->load->model('app_m');
        $ci->load->model('app_column_m');
        $this->app_m =& $ci->app_m;
        $this->app_column_m =& $ci->app_column_m;
        parent::__construct();
    }

    public function create()
    {
        $id = parent::create();
        $app_table = $this->get_where($id);
        $link_name = linked_table_name($app_table);
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
        return $id;
    }

    public function delete($id = FALSE)
    {
        $app_table = $this->get_where($id);
        $app_columns = $this->app_column_m->get_all_where('app_table_id', $id);
        if($app_columns)
        {
            foreach($app_columns as $app_column)
            {
                $this->app_column_m->delete($app_column->id);
            }
        }
        $link_name = linked_table_name($app_table);
        $this->dbforge->drop_table($link_name, TRUE);
        return parent::delete($id);
    }

    public function form($id = FALSE, $new = FALSE)
    {
        $apps = $this->app_m->get();
        ob_start();
        if($apps)
        {
            $app_table = FALSE;
            if($id)
            {
                $app_table = $this->get_where($id);
            }
            if($app_table || $new)
            {
                $action = ($id) ? base_url('app_table/' . $id) : base_url('app_table');
                $app_table_name = ($app_table) ? $app_table->name : set_value('name');

                $heading = ($new) ? 'Creating new app table' : 'Editing app table: ' . $app_table_name;
                echo '<h2>' . $heading . '</h2>';

                echo form_open($action);
                ! $app_table || rest_method_input('put');

                echo form_group_open();
                echo form_label('App Table Name', 'name');
                echo form_input(array(
                    'id'=>'name',
                    'name'=>'name',
                    'value'=>$app_table_name
                ), '', form_control());
                echo form_group_close();

                $apps = $this->app_m->get();
                $options = array();
                $selected = $this->input->post('app');
                foreach($apps as $app)
                {
                    $options[$app->id] = $app->name;
                    if($app_table)
                    {
                        if($app_table->app_id == $app->id)
                        {
                            $selected = $app->id;
                        }
                    }
                }
                $data = array(
                    'id'=>'app',
                    'name'=>'app'
                );

                echo form_group_open();
                echo form_label('App', 'app');
                echo form_dropdown($data, $options, $selected, form_control());
                echo form_group_close();

                echo form_submit('submit', 'Submit');
                echo form_close();
            }else{
                echo '<p>App table not found</p>';
            }
            echo '<p>' . anchor(base_url('app_table'), 'Back to App Tables') . '</p>';
        }else{
            echo '<p>Cannot create table without existing app</p>';
        }
        return ob_get_clean();
    }

    public function listing()
    {
        if($filtered = $app_id = $this->input->get('app'))
        {
            $app = $this->app_m->get_where($app_id);
            $app_tables = $this->get_where('app_id', $app->id);
            if($app_tables)
            {
                is_array($app_tables) || $app_tables = array($app_tables);
            }
            $sub_heading = 'Listing tables for app: ' . $app->name;
        }else{
            $app_tables = $this->get();
            $sub_heading = 'Listing all app tables';
        }
        ob_start();
        ?>
        <?php if($app_tables): ?>
        <h2><?php echo $sub_heading; ?></h2>
        <table class="table">
            <thead>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>App Table Name</th>
                <th>App Name</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($app_tables as $app_table): ?>
                <tr>
                    <td><?php echo anchor(base_url('app_table/' . $app_table->id), 'Edit'); ?></td>
                    <td><?php echo form_delete('app_table', $app_table->id); ?></td>
                    <td><?php echo anchor(base_url('app_column/?app_table=' . $app_table->id), 'Columns'); ?></td>
                    <td><?php echo anchor(base_url('app_row/?app_table=' . $app_table->id), 'Rows'); ?></td>
                    <td><?php echo $app_table->name; ?></td>
                    <?php
                    $app = $this->app_m->get_where($app_table->app_id);
                    $app_name = ($app) ? $app->name : 'No app selected';
                    ?>
                    <td><?php echo $app_name; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>No app tables found</p>
        <?php endif; ?>
        <p><?php echo anchor(base_url('app'), 'Back to Apps'); ?></p>
        <?php if($filtered): ?>
        <p><?php echo anchor(base_url('app_table'), 'Show all App Tables'); ?></p>
        <?php endif; ?>
        <?php
        return ob_get_clean();
    }
}