<?php defined('BASEPATH') OR exit('No direct script access allowed');

if( ! function_exists('linked_db_name'))
{
    function linked_db_name($id)
    {
        $id = is_object($id) ? $id->id : $id;
        return '_' . $id;
    }
}