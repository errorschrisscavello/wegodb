<?php defined('BASEPATH') OR exit('No direct script access allowed');

class app_m extends MY_Model
{
    public $table = 'apps';
    public $post_filter = array(
        'name'=>'name',
        'token'=>'token'
    );
    public $app_table_m;
    public $app_column_m;

    function __construct()
    {
        $ci =& get_instance();
        $ci->load->model('app_table_m');
        $ci->load->model('app_column_m');
        $this->app_table_m =& $ci->app_table_m;
        $this->app_column_m =& $ci->app_column_m;
        parent::__construct();
    }

    public function form($id = FALSE, $new = FALSE)
    {
        $app = FALSE;
        if($id)
        {
            $app = $this->get_where($id);
        }
        ob_start();
        if($app || $new)
        {
            $action = ($id) ? base_url('app/' . $id) : base_url('app');
            $app_name = ($app) ? $app->name : set_value('name');

            $heading = ($new) ? 'Creating new app' : 'Editing app: ' . $app_name;
            echo '<h2>' . $heading . '</h2>';

            echo form_open($action);
            ! $app || rest_method_input('put');

            echo form_group_open();
            echo form_label('App Name', 'name');
            echo form_input(array(
                'id'=>'name',
                'name'=>'name',
                'value'=>$app_name
            ), '', form_control());
            echo form_group_close();

            echo form_submit('submit', 'Submit');
            echo form_close();
        }else{
            echo '<p>App not found</p>';
        }
        echo '<p>' . anchor(base_url('app'), 'Back to Apps') . '</p>';
        return ob_get_clean();
    }

    public function listing()
    {
        $apps = $this->get();
        ob_start();
        ?>
        <?php if($apps): ?>
        <table class="table">
            <thead>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>App Name</th>
                <th>Token</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($apps as $app): ?>
                <tr>
                    <td><?php echo anchor(base_url('app/' . $app->id), 'Edit'); ?></td>
                    <td><?php echo form_delete('app', $app->id); ?></td>
                    <td><?php echo anchor(base_url('app_table/?app=' . $app->id), 'Tables'); ?></td>
                    <td><?php echo anchor(base_url('app_column/?app=' . $app->id), 'Columns'); ?></td>
                    <td><?php echo $app->name; ?></td>
                    <td><?php echo $app->token; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>No apps found</p>
        <?php endif; ?>
        <?php
        return ob_get_clean();
    }

    public function filter_post()
    {
        $data = parent::filter_post();
        $data['token'] = $this->auth->hash($data['name'] . $this->auth->salt);
        return $data;
    }

    public function delete($id)
    {
        $app_tables = $this->app_table_m->get_all_where('app_id', $id);
        if($app_tables)
        {
            foreach($app_tables as $app_table)
            {
                $this->app_table_m->delete($app_table->id);
            }
        }
        return parent::delete($id);
    }
}