<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model
{
    public $table = '';
    public $post_filter = array();

    public function get()
    {
        $result = $this->db->get($this->table)->result();
        return (count($result) > 0) ? $result : FALSE;
    }

    public function get_where($id, $value = FALSE)
    {
        $this->db->select('*');
        if($value)
        {
            $field = $id;
            $this->db->where($field, $value);
        }else{
            $this->db->where('id', $id);
        }
        $this->db->limit(1);
        $result = $this->get();
        return ($result) ? $result[0] : FALSE;
    }

    public function get_all_where($key, $value)
    {
        $this->db->select('*');
        $field = $key;
        $this->db->where($field, $value);
        $result = $this->get();
        return (count($result) > 0) ? $result : FALSE;
    }

    public function get_new()
    {
        $field_data = $this->db->field_data($this->table);
        $new = new stdClass();
        foreach($field_data as $field)
        {
            $field_name = $field->name;
            $new->$field_name = '';
        }
        return $new;
    }

    public function create()
    {
        $data = $this->filter_post();
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id)
    {
        $data = $this->filter_post();
        $this->db->where('id', $id);
        $this->db->update($this->table, $data);
        return $this->db->affected_rows();
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete($this->table);
        return $this->db->affected_rows();
    }

    //TODO best method for form binding?

    public function form($id = FALSE, $new = FALSE)
    {
        return '';
    }
    public function listing()
    {
        return '';
    }

    public function filter_post()
    {
        $data = array();
        $post_filter = $this->post_filter;
        if(count($post_filter) > 0)
        {
            foreach($post_filter as $field_name => $post_key)
            {
                $data[$field_name] = $this->input->post($post_key);
            }
        }
        return $data;
    }
}