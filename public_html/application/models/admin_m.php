<?php defined('BASEPATH') OR exit('No direct script access allowed');

class admin_m extends MY_Model
{
    public function form($id = FALSE, $new = FALSE)
    {
        ob_start();
        ?>
        <form action="<?php echo base_url('login'); ?>" method="post">
            <label for="user">Username or Email</label>
            <input id="user" name="user" type="text" value="<?php echo set_value('user'); ?>"/>

            <label for="password">Password</label>
            <input id="password" name="password" type="password"/>

            <?php echo form_csrf(); ?>

            <input type="submit" value="Login"/>
        </form>
        <?php
        return ob_get_clean();
    }
}