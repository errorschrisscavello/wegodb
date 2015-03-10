<?php defined('BASEPATH') OR exit('No direct script access allowed');

class api_m extends MY_Model
{
    public $app_m;
    public $app_table_m;
    public $app_column_m;
    public $app_row_m;
    public $data;

    function __construct()
    {
        $ci =& get_instance();
        $ci->load->model('app_m');
        $ci->load->model('app_table_m');
        $ci->load->model('app_column_m');
        $ci->load->model('app_row_m');
        $this->app_m = $ci->app_m;
        $this->app_table_m = $ci->app_table_m;
        $this->app_column_m = $ci->app_column_m;
        $this->app_row_m = $ci->app_row_m;
        parent::__construct();
    }

    public function get_app($token)
    {
        $app = $this->app_m->get_where('token', $token);
        return $app;
    }

    public function get_table($name)
    {
        return $this->app_table_m->get_where('name', $name);
    }

    public function get_columns($table)
    {
        $columns = $this->app_column_m->get_where('app_table_id', $table->id);
        is_array($columns) || $columns = array($columns);
        return $columns;
    }

    public function get_rows($table)
    {
        $link_name = linked_table_name($table);
        return $this->db->get($link_name);
    }

    public function action()
    {
        $action = $this->data['action'];
        if($action == 'create')
        {
            return $this->create();
        }elseif($action == 'read'){
            return $this->read();
        }elseif($action == 'update'){
            return $this->update();
        }elseif($action == 'delete'){
            return $this->delete();
        }
        return FALSE;
    }

    public function create()
    {
        $token = $this->data['token'];
        $app = $this->get_app($token);
        $table_name = $this->data['table'];
        $table = $this->get_table($table_name);
        if($table->app_id == $app->id)
        {
            $columns = $this->get_columns($table);
            $column_names = array();
            foreach($columns as $column)
            {
                $column_names[] = $column->name;
            }
            $data = (isset($this->data['data'])) ? $this->data['data'] : array();
            $escaped = array();
            foreach($data as $key => $value)
            {
                if(in_array($key, $column_names))
                {
                    $escaped[$key] = $this->db->escape($value);
                }
            }
            $link_name = linked_table_name($table);
            if(count($escaped) > 0)
            {
                $this->db->insert($link_name, $escaped);
                return 'New row inserted with ID: ' . $this->db->insert_id();
            }
            return 'Unable to insert values for those fields';
        }
        return 'The table name did not match the app token';
    }

    public function read($id = FALSE)
    {
        $token = $this->data['token'];
        $app = $this->get_app($token);
        $table_name = $this->data['table'];
        $table = $this->get_table($table_name);
        if($table->app_id == $app->id)
        {
            $link_name = linked_table_name($table);
            $data = $this->db->get($link_name)->result();
            return $data;
        }
        return 'The table name did not match the app token';
    }

    public function update($id = FALSE)
    {
        $token = $this->data['token'];
        $app = $this->get_app($token);
        $table_name = $this->data['table'];
        $table = $this->get_table($table_name);
        if($table->app_id == $app->id)
        {
            if(isset($this->data['where']))
            {
                $where = (int)$this->data['where'];
                if($where > 0)
                {
                    $link_name = linked_table_name($table);
                    $this->db->where('id', $where);
                    $result = $this->db->get($link_name)->result();
                    $result = ($result) ? $result[0] : FALSE;
                    if($result)
                    {
                        $columns = $this->get_columns($table);
                        $column_names = array();
                        foreach($columns as $column)
                        {
                            $column_names[] = $column->name;
                        }
                        $data = (isset($this->data['data'])) ? $this->data['data'] : array();
                        $escaped = array();
                        foreach($data as $key => $value)
                        {
                            if(in_array($key, $column_names))
                            {
                                $escaped[$key] = $this->db->escape($value);
                            }
                        }
                        if(count($escaped) > 0)
                        {
                            $this->db->where('id', $where);
                            $this->db->update($link_name, $escaped);
                            return 'Updated row with ID: ' . $where;
                        }
                        return 'Unable to update values for those fields';
                    }
                    return 'No row found with ID: ' . $where;
                }
                return 'The provided "where" value was not valid';
            }
            return 'No "where" statement was provided for update';
        }
        return 'The table name did not match the app token';
    }

    public function delete($id = FALSE)
    {
        $token = $this->data['token'];
        $app = $this->get_app($token);
        $table_name = $this->data['table'];
        $table = $this->get_table($table_name);
        if($table->app_id == $app->id)
        {
            if(isset($this->data['where']))
            {
                $where = (int)$this->data['where'];
                if($where > 0)
                {
                    $link_name = linked_table_name($table);
                    $this->db->where('id', $where);
                    $result = $this->db->get($link_name)->result();
                    $result = ($result) ? $result[0] : FALSE;
                    if($result)
                    {
                        move_to_trash($link_name, $result);
                        $this->db->where('id', $where);
                        $this->db->delete($link_name);
                        return 'Deleted row with ID: ' . $where;
                    }
                    return 'No row found with ID: ' . $where;
                }
                return 'The provided "where" value was not valid';
            }
            return 'No "where" statement was provided';
        }
        return 'The table name did not match the app token';
    }
}