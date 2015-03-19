<?php defined('BASEPATH') OR exit('No direct script access allowed');

class admin_m extends MY_Model
{
    public function form($id = FALSE, $new = FALSE)
    {
        ob_start();
        ?>
        <form action="<?php echo base_url('login'); ?>" method="post">
            <div class="form-group">
                <label for="user">Username or Email</label>
                <input class="form-control" id="user" name="user" type="text" value="<?php echo set_value('user'); ?>"/>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input class="form-control" id="password" name="password" type="password"/>
            </div>

            <?php echo form_csrf(); ?>

            <?php echo form_submit('Login'); ?>
        </form>
        <?php
        return ob_get_clean();
    }
}