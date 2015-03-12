<?php

define('INSTALL_PATH', __DIR__ . '/');

//drop database
//delete index.php
//mv assets/auth => application/config/auth.php
//mv assets/database.php => application/config/database.php
//mv reset/database.txt => inc/database.php
//mv reset/sat.txt => inc/salt.php
//mv install.php => index.php

$database = require_once INSTALL_PATH . 'install/inc/database.php';

$host = $database['hostname'];
$user = $database['username'];
$password = $database['password'];
$db = $database['database'];

$connection = mysqli_connect($host, $user, $password, $db);

$query = "DROP DATABASE IF EXISTS $db";
$result = mysqli_query($connection, $query);

$index = file_get_contents(INSTALL_PATH . 'install/reset/index.txt');
file_put_contents(INSTALL_PATH . 'index.php', $index);

$config_auth = file_get_contents(INSTALL_PATH . 'install/assets/auth.txt');
$config_auth = str_replace('%SALT%', '', $config_auth);
file_put_contents(INSTALL_PATH . 'application/config/auth.php', $config_auth);

$config_database = file_get_contents(INSTALL_PATH . 'install/assets/database.txt');
$config_database = str_replace('%HOST%', 'localhost', $config_database);
$config_database = str_replace('%USERNAME%', '', $config_database);
$config_database = str_replace('%PASSWORD%', '', $config_database);
$config_database = str_replace('%DATABASE%', '', $config_database);
file_put_contents(INSTALL_PATH . 'application/config/database.php', $config_database);

$database_txt = file_get_contents(INSTALL_PATH . 'install/reset/database.txt');
file_put_contents(INSTALL_PATH . 'install/inc/database.php', $database_txt);

$salt_txt = file_get_contents(INSTALL_PATH . 'install/reset/salt.txt');
file_put_contents(INSTALL_PATH . 'install/inc/salt.php', $salt_txt);

if(file_exists(INSTALL_PATH . '.htaccess'))
{
    unlink(INSTALL_PATH . '.htaccess');
}

if(file_exists(INSTALL_PATH . 'install.php'))
{
    unlink(INSTALL_PATH . 'install.php');
}

echo "Reset done.";
