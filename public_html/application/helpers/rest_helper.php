<?php defined('BASEPATH') OR exit('No direct script access allowed');

if( ! function_exists('create_new'))
{
    function create_new()
    {
        return (isset($_GET['new']));
    }
}

if( ! function_exists('rest_method_input'))
{
    function rest_method_input($method)
    {
        if($method == 'put' || $method == 'delete')
        {
            ?>
            <input type="hidden" name="REQUEST_METHOD" value="<?php echo strtoupper($method); ?>"/>
            <?
        }
    }
}

if( ! function_exists('get'))
{
    function is_get()
    {
        return ($_SERVER['REQUEST_METHOD'] == 'GET');
    }
}

if( ! function_exists('post'))
{
    function is_post()
    {
        return ($_SERVER['REQUEST_METHOD'] == 'POST' && ! is_put() && ! is_delete());
    }
}

if( ! function_exists('put'))
{
    function is_put()
    {
        $is_put = FALSE;
        if(isset($_POST['REQUEST_METHOD']))
        {
            $is_put = ($_POST['REQUEST_METHOD'] == 'PUT');
        }
        return $is_put;
    }
}

if( ! function_exists('delete'))
{
    function is_delete()
    {
        $is_delete = FALSE;
        if(isset($_POST['REQUEST_METHOD']))
        {
            $is_delete = ($_POST['REQUEST_METHOD'] == 'DELETE');
        }
        return $is_delete;
    }
}