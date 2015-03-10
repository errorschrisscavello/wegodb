<?php defined('BASEPATH') OR exit('No direct script access allowed');

class trash_m extends MY_Model
{
    public $table = 'trash';
    public $post_filter = array(
        'source'=>'source',
        'data'=>'data'
    );
    public $app_m;
    public $app_table_m;
    public $app_column_m;

    function __construct()
    {
        $ci =& get_instance();
        $ci->load->model('app_m');
        $ci->load->model('app_table_m');
        $this->app_m =& $ci->app_m;
        $this->app_table_m =& $ci->app_table_m;
        $this->app_column_m =& $ci->app_column_m;
        parent::__construct();
    }

    public function create()
    {
        $data = $this->filter_post();
        $table = $_POST['link_name'];
        $this->db->insert($table, $data);
        $id = $this->input->post('id');
        $this->delete($id);
        return $this->db->insert_id();
    }

    public function filter_post()
    {
        $id = $this->input->post('id');
        $item = $this->get_where($id);
        $data = array();
        $unserialized = unserialize($item->data);
        foreach($unserialized as $key => $value)
        {
            if($key != 'id')
            {
                $data[$key] = $value;
            }
        }
        $_POST['link_name'] = $item->source;
        return $data;
    }

    public function listing()
    {
        $trash = $this->get();
        ob_start();
        if($trash)
        {
            $apps = array();
            $tables = array();
            foreach($trash as $item)
            {
                $link_name = $item->source;
                $app = app_from_link($link_name);
                $table = table_from_link($link_name);
                if( ! array_key_exists($app->name, $apps))
                {
                    $apps[$app->name] = $app;
                }
                if( ! array_key_exists($table->name, $tables))
                {
                    $tables[$table->name] = $table;
                }
            }
            ?>
            <h2>Listing Trash</h2>
            <?php foreach($apps as $app_name => $app): ?>
            <h3>Trash for app: <?php echo $app_name; ?></h3>
            <?php foreach($tables as $table_name => $table): ?>
                <?php if($table->app_id == $app->id): ?>
                    <h4>Table: <?php echo $table_name; ?></h4>
                    <?php
                    $thead = '';
                    $tbody = '';
                    $i = 0;
                    foreach($trash as $item)
                    {
                        $row = FALSE;
                        if($item->source == linked_table_name($table))
                        {
                            $row = unserialize($item->data);
                        }
                        if($row)
                        {
                            if($i == 0)
                            {
                                $thead .= '<th></th>';
                                $thead .= '<th></th>';
                            }
                            $tbody .= '<tr>';
                            $tbody .= '<td>' . form_restore($item->id) . '</td>';
                            $tbody .= '<td>' . form_delete('trash', $item->id) . '</td>';
                            foreach($row as $key => $value)
                            {
                                if($key != 'id')
                                {
                                    if($i == 0)
                                    {
                                        $thead .= '<th>' . $key . '</th>';
                                    }
                                    $tbody .= '<td>' . $value  . '</td>';
                                }
                            }
                            $tbody .= '</tr>';
                        }
                        $i++;
                    }
                    ?>
                    <table>
                        <thead>
                        <tr>
                            <?php echo $thead; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php echo $tbody; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endforeach; ?>
        <?php
        }else{
            echo '<p>Trash is empty</p>';
        }
        return ob_get_clean();
    }
}