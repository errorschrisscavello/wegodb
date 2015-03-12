<?php

class Install
{
    public $heading = 'Welcome to the WegoDB Installer!';
    public $messages = array();
    public $errors = array();
    public $content = '';
    public $connection = FALSE;
    public $base_url = '';
    public $step = FALSE;
    public $database = FALSE;
    public $env = 'production';

    function __construct()
    {
        require_once INSTALL_PATH . 'install/inc/dump.php';
        $this->database = require INSTALL_PATH . 'install/inc/database.php';
        $this->base_url = 'http://' .$_SERVER['HTTP_HOST'] . '/';
    }

    public function install_user()
    {
        if( ! $this->connection)
        {
            $host = $this->database['hostname'];
            $user = $this->database['username'];
            $password = $this->database['password'];
            $db = $this->database['database'];
            $this->connect($host, $user, $password, $db);
        }
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = hash('sha512', $_POST['password']);
        $this->mail();
        $query = "INSERT INTO users (username, email, password, active) VALUES('$username', '$email', '$password', 0)";
        $result = mysqli_query($this->connection, $query);
        if($result == FALSE)
        {
            $this->errors[] = 'MySQL Error: ' . mysqli_error($this->connection);
        }
        if($result = mysqli_store_result($this->connection))
        {
            mysqli_free_result($this->connection);
        }
        $this->step = 'user';
    }

    public function create_salt()
    {
        $salt = hash('sha512', uniqid() . time());
        $data = "<?php

return '$salt';
";
        $file = INSTALL_PATH . 'install/inc/salt.php';
        file_put_contents($file, $data);
    }

    public function install_db()
    {
        $host = $_POST['host'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $database = $_POST['database'];

        $data = "<?php

return array(
    'hostname' => '$host',
    'username' => '$username',
    'password' => '$password',
    'database' => '$database'
);
";
        $file = INSTALL_PATH . 'install/inc/database.php';
        file_put_contents(INSTALL_PATH . 'install/inc/database.php', $data);

        $this->database = require INSTALL_PATH . 'install/inc/database.php';

        $this->database();

        $this->step = 'database';
    }

    public function valid_user()
    {
        require_once INSTALL_PATH . 'install/inc/form.php';

        form_trim(array(
            'username',
            'email',
            'confirm_email',
            'password',
            'confirm_password'
        ));
        form_required(array(
            'username',
            'email',
            'confirm_email',
            'password',
            'confirm_password'
        ), $this->errors);
        form_min_length(8, 'username', $this->errors);
        form_min_length(8, 'password', $this->errors);
        form_max_length(32, 'username', $this->errors);
        form_max_length(32, 'password', $this->errors);
        form_valid_email($this->errors);
        $regex = "/[^0-9a-zA-Z_]/";
        $chars_error_message = ' can only contain letters, numbers, and the underscore';
        $username_chars_error_message = 'The "Username" ' . $chars_error_message;
        form_regex($regex, $username_chars_error_message, 'username', $this->errors);
        $password_chars_error_message = 'The "Password" ' . $chars_error_message;
        form_regex($regex, $password_chars_error_message, 'password', $this->errors);
        form_matches('email', 'confirm_email', $this->errors);
        form_matches('password', 'confirm_password', $this->errors);

        return (count($this->errors) == 0);
    }

    public function connect($host, $user, $password, $db)
    {
        $this->connection = mysqli_connect($host, $user, $password, $db);
        if(mysqli_connect_errno())
        {
            $this->errors[] = 'MySQL Error: ' . mysqli_connect_error();
        }
    }

    public function view($data)
    {
        extract($data);
        require_once INSTALL_PATH . '/install/inc/view.php';
    }

    public function permissions()
    {
        $writable = is_writable(INSTALL_PATH);
        if($writable)
        {
            $data = file_get_contents(INSTALL_PATH . 'install/assets/htaccess.txt');
            $file = INSTALL_PATH . '.htaccess';
            file_put_contents($file, $data);
        }
        return $writable;
    }

    public function table_exists($name)
    {
        $result = mysqli_query($this->connection, "SHOW TABLES LIKE '$name'");
        $table_exists = (mysqli_num_rows($result) > 0);
        if($result = mysqli_store_result($this->connection))
        {
            mysqli_free_result($result);
        }
        return $table_exists;
    }

    public function database()
    {
        $host = $this->database['hostname'];
        $user = $this->database['username'];
        $password = $this->database['password'];
        $db = $this->database['database'];
        if($host != '' && $user != '' && $password != '' && $db != '')
        {
            if( ! $this->connection)
            {
                $this->connect($host, $user, $password, $db);
            }
            if($this->connection)
            {
                $users = $this->table_exists('users');
                $apps = $this->table_exists('apps');
                $app_tables = $this->table_exists('app_tables');
                $app_columns = $this->table_exists('app_columns');
                $trash = $this->table_exists('trash');
                if($users && $apps && $app_tables && $app_columns && $trash)
                {
                    return TRUE;
                }else{
                    $this->create_salt();
                    $query = file_get_contents(INSTALL_PATH . 'install/assets/database.sql');
                    if($result = mysqli_multi_query($this->connection, $query))
                    {
                        while(mysqli_next_result($this->connection) && mysqli_more_results($this->connection))
                        {
                            if($result = mysqli_store_result($this->connection))
                            {
                                mysqli_free_result($result);
                            }
                        }
                    }
                    return FALSE;
                }
            }
            return FALSE;
        }
        return FALSE;
    }

    public function user()
    {
        $query = "SELECT * FROM users";
        $result = mysqli_query($this->connection, $query);
        if($result == FALSE)
        {
            $this->errors[] = 'MySQL Error: ' . mysqli_error($this->connection);
            return FALSE;
        }
        if($result)
        {
            $rows = array();
            while($row = mysqli_fetch_row($result))
            {
                $rows[] = $row;
            }
            if($result = mysqli_store_result($this->connection))
            {
                mysqli_free_result($this->connection);
            }
            return (count($rows) > 0) ? $rows : FALSE;
        }
        return FALSE;
    }

    public function db_form()
    {
        ob_start();
        ?>
        <form action="/" method="post">
            <input type="hidden" name="type" id="type" value="database"/>
            <label for="host">Host</label>
            <input type="text" name="host" id="host" value="localhost"/>
            <label for="username">Username</label>
            <input type="text" name="username" id="username"/>
            <label for="password">Password</label>
            <input type="password" name="password" id="password"/>
            <label for="database">Database</label>
            <input type="text" name="database" id="database"/>
            <input type="submit" value="Submit"/>
        </form>
        <?php
        return ob_get_clean();
    }

    public function user_form()
    {
        ob_start();
        ?>
        <form action="/" method="post">
            <input type="hidden" name="type" id="type" value="user"/>
            <label for="username">Username</label>
            <input type="text" name="username" id="username"/>
            <label for="email">Email</label>
            <input type="text" name="email" id="email"/>
            <label for="confirm_email">Confirm Email</label>
            <input type="text" name="confirm_email" id="confirm_email"/>
            <label for="password">Password</label>
            <input type="password" name="password" id="password"/>
            <label for="confirm_password">Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm_password"/>
            <input type="submit" value="Submit"/>
        </form>
        <?php
        return ob_get_clean();
    }

    public function post()
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $type = $_POST['type'];
            if($type == 'database')
            {
                $this->install_db();
            }elseif($type == 'user'){
                if($this->valid_user())
                {
                    $this->install_user();
                }
            }
        }
    }

    public function mail()
    {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: <mail@wegodb.com> WegoDB";
        $subject = 'WegoDB [ New User "' . $username . '" Created ], please activate!';
        $salt = require INSTALL_PATH . 'install/inc/salt.php';
        $token = hash('sha512', $email . $salt);
        $activation_link = $this->base_url . 'user/activate?token=' . $token . '&email=' . urlencode($email);
        $message = "
            WegoDB

            Hello $username,
            A new user was created with the following credentials:

            username: $username
            email: $email

            Please save this information in a safe place and activate your account by clicking the link below.

            $activation_link
        ";
        $message = nl2br($message);
        mail($email, $subject, $message, $headers);
    }

    public function escape($string)
    {
        return mysqli_escape_string($this->connection, $string);
    }

    public function close()
    {
        if($this->connection)
        {
            mysqli_close($this->connection);
        }
    }

    public function next()
    {
        return '<a href="' . $this->base_url . '">Next</a>';
    }

    public function install()
    {
        $data = file_get_contents(INSTALL_PATH . 'install/assets/database.txt');
        $data = str_replace('%HOST%', $this->database['hostname'], $data);
        $data = str_replace('%USERNAME%', $this->database['username'], $data);
        $data = str_replace('%PASSWORD%', $this->database['password'], $data);
        $data = str_replace('%DATABASE%', $this->database['database'], $data);
        $file = INSTALL_PATH . 'application/config/database.php';
        file_put_contents($file, $data);

        $data = file_get_contents(INSTALL_PATH . 'install/assets/auth.txt');
        $salt = require INSTALL_PATH . 'install/inc/salt.php';
        $data = str_replace('%SALT%',$salt, $data);
        $file = INSTALL_PATH . 'application/config/auth.php';
        file_put_contents($file, $data);

        rename(INSTALL_PATH . 'index.php', INSTALL_PATH . 'install.php');

        $index = ($this->env == 'production') ? 'index' : 'login';
        $index = file_get_contents(INSTALL_PATH . 'install/assets/' . $index . '.txt');
        $file = INSTALL_PATH . 'index.php';
        file_put_contents($file, $index);

        header("Location: " . $this->base_url);
    }

    public function run()
    {
        $this->post();

        if( ! $this->permissions())
        {
            $this->messages[] = 'Cannot install. Please make this directory writable by the server.';
            $this->content = '<a href="' . $this->base_url . '">Check permissions again</a>';
        }elseif( ! $this->database()){
            $this->messages[] = 'Please provide your database credentials.';
            $this->content = $this->db_form();
        }elseif($this->step == 'database'){
            $this->messages[] = 'Database installed!';
            $this->content = $this->next();
        }elseif( ! $this->user()){
            $this->messages[] = 'Please create a user';
            $this->content = $this->user_form();
        }elseif($this->step == 'user'){
            $this->messages[] = 'User created! Check your email for an activation link! Click next to continue to the login form!';
            $this->content = $this->next();
        }else{
            $this->install();
        }

        $this->view(array(
            'heading'=>$this->heading,
            'messages'=>$this->messages,
            'errors'=>$this->errors,
            'content'=>$this->content
        ));

        $this->close();
    }
}

$install = new Install();
$install->run();