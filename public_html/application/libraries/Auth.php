<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth
{
    private $ci;
    public $salt = 'a6166a67d535ee7883196b3c30f98264354b490c58a3cb1c650590ff9873ab7bb74d47e6e41be5205229d3c4889f08fcad21822c4c6007acabb66dfd85b1c14d';

    function __construct()
    {
        $this->ci =& get_instance();
    }

    public function is_api_request()
    {
        return (isset($_POST['csrf']) && isset($_POST['token']));
    }

    public function validate_api()
    {
        $api_csrf = $this->ci->input->post('csrf');
        $api_token = $this->ci->input->post('token');

        return($api_csrf == $this->api_csrf() && $api_token == $this->salt);
    }

    public function api_csrf()
    {
        return md5($this->salt);
    }

    public function activate($user)
    {
        $user = $this->get_user_by_identity($user)->result()[0];
        $id = $user->id;
        $this->ci->db->where('id', $id);
        $this->ci->db->update('users', array(
            'active'=>1
        ));
        return $this->ci->db->affected_rows();
    }

    public function activation_form($email)
    {
        $user = $this->get_user_by_identity($email)->result()[0];
        ob_start();
        $this->ci->load->helper('form');
        ?>
        <form action="<?php echo base_url('user/send_activation'); ?>" method="post">
            <input name="email" type="hidden" value="<?php echo $user->email; ?>"/>
            <input name="username" type="hidden" value="<?php echo $user->username; ?>"/>
            <?php echo form_csrf(); ?>
            <input type="submit" value="Resend activation email"/>
        </form>
        <?php
        return ob_get_clean();
    }

    public function activation_link($email)
    {
        $token = $this->hash($email . $this->salt);
        $email = urlencode($email);
        $link = base_url('user/activate') . '?token=' . $token . '&email=' . $email;
        return $link;
    }

    public function validate_activation($token, $email)
    {
        $email = urldecode($email);
        $match = $this->hash($email . $this->salt);
        return ($token == $match);
    }

    public function validate_credentials()
    {
        //TODO check if user is active
        $user = $this->ci->input->post('user');
        $password = $this->ci->input->post('password');

        return ($this->user_exists($user) && $this->password_matches($user, $password));
    }

    public function validate_session()
    {
        $session = $this->ci->session;
        $user = $session->userdata('user');
        $logged_in = $session->userdata('logged_in');
        return ($this->user_exists($user) && $logged_in);
    }

    public function password_matches($user, $password)
    {
        $q = $this->get_user_by_identity($user);
        if(count($q->result()) != 1)
        {
            return FALSE;
        }
        $user = $q->result()[0];
        if($this->hash($password) == $user->password)
        {
            return TRUE;
        }
        return FALSE;
    }

    public function hash($string)
    {
        return hash('sha512', $string);
    }

    public function get_user_by_identity($user)
    {
        $db = $this->ci->db;

        $db->select('*');
        $db->where('email', $user);
        $db->or_where('username', $user);

        return $db->get('users');
    }

    public function user_exists($user)
    {
        $q = $this->get_user_by_identity($user);
        $user_exists = (count($q->result()) > 0);
        return $user_exists;
    }

    public function session_create()
    {
        $user = $this->ci->input->post('user');
        $q = $this->get_user_by_identity($user)->result()[0];
        $username = $q->username;
        $session = $this->ci->session;
        $session->set_userdata('user', $username);
        $session->set_userdata('logged_in', TRUE);
    }

    public function session_destroy()
    {
        $session = $this->ci->session;
        $session->set_userdata('user', NULL);
        $session->set_userdata('logged_in', FALSE);
        $session->sess_destroy();
    }

    public function is_active($user)
    {
        $q = $this->get_user_by_identity($user)->result();
        if(count($q) > 0)
        {
            $user = $q[0];
            return ((int)$user->active > 0);
        }
        return FALSE;
    }

    public function current()
    {
        return $this->ci->session->userdata('user');
    }

    public function is_current($username)
    {
        return ($username == $this->current());
    }
}