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

    public function get_where($id)
    {
        $this->db->select('*');
        $this->db->where('id', $id);
        $this->db->limit(1);
        $result = $this->get();
        return ($result) ? $result[0] : $result;
    }

    public function get_new()
    {
        $field_data = $this->field_data();
        $new = new stdClass();
        foreach($field_data as $field)
        {
            $field_name = $field->name;
            $new->$field_name = '';
        }
        return $new;
    }

    public function create($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
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

    public function field_data()
    {
        $field_data = $this->db->field_data($this->table);
        foreach($field_data as $field)
        {
            $field->input = 'text';
        }
        return $field_data;
    }

    public function prep()
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

    public function sort_fields($fields, $order)
    {
        $ordered = array();
        if( ! in_array('id', $order))
        {
            $order = array_merge(array('id'), $order);
        }
        foreach($order as $field_name)
        {
            foreach($fields as $field)
            {
                if($field_name == $field->name)
                {
                    $ordered[] = $field;
                }
            }
        }
        return $ordered;
    }
}