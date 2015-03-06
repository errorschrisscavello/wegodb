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