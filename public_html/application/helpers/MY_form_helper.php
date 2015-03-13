<?php defined('BASEPATH') OR exit('No direct script access allowed');

if( ! function_exists('form_csrf'))
{
    function form_csrf()
    {
        $ci =& get_instance();
        ob_start();
        ?>
        <input type="hidden" name="<?php echo $ci->security->get_csrf_token_name(); ?>" value="<?php  echo $ci->security->get_csrf_hash(); ?>"/>
        <?php
        return  ob_get_clean();
    }
}

if( ! function_exists('form_edit'))
{
    function form_edit($action, $method, $field_data, $data, $multi = FALSE)
    {
        $inputs = $this->inputs($field_data, $data);
        ob_start();
        ?>
        <?php if( ! $multi): ?>
        <form action="<?php echo $action; ?>" method="post">
            <?php rest_method_input($method); ?>
            <?php echo $inputs; ?>
            <?php echo $this->csrf(); ?>
            <input type="submit" value="Submit"/>
        </form>
        <?php else: ?>
            <?php foreach($data as $d): ?>
                <?php if(is_object($d) && $d->name != 'id'): ?>
                    <?php $inputs = $this->inputs($field_data, $d); ?>
                    <form action="<?php echo $action; ?>" method="post">
                        <?php rest_method_input($method); ?>
                        <?php echo $inputs; ?>
                        <?php echo $this->csrf(); ?>
                        <input type="submit" value="Submit"/>
                    </form>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php
        return ob_get_clean();
    }
}

if( ! function_exists('form_delete'))
{
    function form_delete($resource, $id, $text = FALSE)
    {
        $action = base_url($resource . '/' . $id);
        ob_start();
        ?>
        <form action="<?php echo $action; ?>" method="post">
            <input type="hidden" name="resource" value="<?php echo $resource; ?>"/>
            <input type="hidden" name="id" value="<?php echo $id; ?>"/>
            <?php rest_method_input('delete'); ?>
            <?php echo form_csrf(); ?>
            <?php $value = ($text) ? $text : 'Delete'; ?>
            <input type="submit" value="<?php echo $value; ?>"/>
        </form>
        <?php
        return ob_get_clean();
    }
}

if( ! function_exists('form_restore'))
{
    function form_restore($id)
    {
        $action = base_url('trash/' . $id);
        ob_start();
        ?>
        <form action="<?php echo $action; ?>" method="post">
            <input type="hidden" name="id" value="<?php echo $id; ?>"/>
            <?php echo form_csrf(); ?>
            <input type="submit" value="Restore"/>
        </form>
        <?php
        return ob_get_clean();
    }
}