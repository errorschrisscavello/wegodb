<?php defined('BASEPATH') OR exit('No direct script access allowed');

if( ! function_exists('linked_table_name'))
{
    function linked_table_name($app_table)
    {
        return 'app' . $app_table->app_id . '_table' . $app_table->id;
    }
}

if( ! function_exists('get_field'))
{
    function get_field($name, $field_data)
    {
        foreach($field_data as $field)
        {
            if($field->name == $name)
            {
                return $field;
            }
        }
        return FALSE;
    }
}

if( ! function_exists('move_to_trash'))
{
    function move_to_trash($source, $item)
    {
        $ci =& get_instance();
        $serialized = serialize($item);
        $data = array(
            'source'=>$source,
            'data'=>$serialized
        );
        $ci->db->insert('trash', $data);
        return $ci->db->insert_id();
    }
}

if( ! function_exists('table_from_link'))
{
    function table_from_link($link_name)
    {
        $ci =& get_instance();
        $id = str_replace('table', '', explode('_', $link_name)[1]);
        $ci->load->model('App_table_m');
        return $ci->App_table_m->get_where($id);
    }
}

if( ! function_exists('app_from_link'))
{
    function app_from_link($link_name)
    {
        $ci =& get_instance();
        $id = str_replace('app', '', explode('_', $link_name)[0]);
        $ci->load->model('App_m');
        return $ci->App_m->get_where($id);
    }
}

if( ! function_exists('field_to_label'))
{
    function field_to_label($name)
    {
        $words = explode('_', $name);
        $label = '';
        for($i = 0; $i < count($words); $i++)
        {
            $word = $words[$i];
            $capitalized = strtoupper(substr($word, 0, 1)) . substr($word, 1, strlen($word));
            $label .= $capitalized;
            $label .= ($i != (count($words) - 1)) ?  ' ' : '';
        }
        return $label;
    }
}