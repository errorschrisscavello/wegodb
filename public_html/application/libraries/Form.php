<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Form
{
    public $ci;

    function __construct()
    {
        $this->ci =& get_instance();
    }

    public function csrf()
    {
        ob_start();
        ?>
        <input type="hidden" name="<?php echo $this->ci->security->get_csrf_token_name(); ?>" value="<?php  echo $this->ci->security->get_csrf_hash(); ?>"/>
        <?php
        return  ob_get_clean();
    }

    public function inputs($field_data, $data)
    {
        ob_start();
        ?>
        <?php foreach($field_data as $field): ?>
        <?php $field_name = $field->name; ?>
        <?php $value = (isset($data->$field_name)) ? $data->$field_name : ''; ?>
        <label for="<?php echo $field_name; ?>"><?php echo $field_name; ?></label>
            <?php if($field->input == 'textarea'): ?>
                <textarea
                    name="<?php echo $field_name; ?>"
                    id="<?php echo $field_name; ?>"
                    cols="30"
                    rows="10">
                    <?php echo $value; ?>
                </textarea>
            <?php elseif($field->input == 'select'): ?>
            <select
                name="<?php echo $field_name; ?>"
                id="<?php echo $field_name; ?>"
                >
                <?php $field_options = $field->options; ?>
                <?php foreach($field_options as $option): ?>
                    <?php if($option['value'] == $value): ?>
                    <option
                        value="<?php echo $value; ?>"
                        selected="selected"
                        >
                        <?php echo $option['text']; ?>
                    </option>
                    <?php else: ?>
                        <option value="<?php echo $option['value']; ?>">
                            <?php echo $option['text']; ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
            <?php else: ?>
                <input
                type="<?php echo $field->input; ?>"
                id="<?php echo $field_name; ?>"
                name="<?php echo $field_name; ?>"
                value="<?php echo $value; ?>"
                <?php if($field_name == 'id'): ?>
                    disabled="disabled"
                <?php endif; ?>
                />
            <?php endif; ?>
        <?php endforeach; ?>
        <?php
        return ob_get_clean();
    }

    public function edit($action, $method, $field_data, $data, $multi = FALSE)
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

    public function delete($action, $resource, $id)
    {
        ob_start();
        ?>
        <form action="<?php echo $action; ?>" method="post">
            <input type="hidden" name="resource" value="<?php echo $resource; ?>"/>
            <input type="hidden" name="id" value="<?php echo $id; ?>"/>
            <?php rest_method_input('delete'); ?>
            <?php echo $this->csrf(); ?>
            <input type="submit" value="Delete"/>
        </form>
        <?php
        return ob_get_clean();
    }
}