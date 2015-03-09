<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

//Defaults
$route['default_controller'] = 'admin/dashboard';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

//Admin
$route['dashboard'] = 'admin/dashboard';
$route['login'] = 'admin/login';
$route['logout'] = 'admin/logout';

$route['migrate'] = 'migration/migrate';

$route['user'] = 'user';
$route['user/(:num)'] = 'user/index/$1';

$route['user/activate'] = 'user/activate';
$route['user/send_activation'] = 'user/resend_activation';

$route['app'] = 'app';
$route['app/(:num)'] = 'app/index/$1';

$route['app_table'] = 'app_table';
$route['app_table/(:num)'] = 'app_table/index/$1';

$route['app_column'] = 'app_column';
$route['app_column/(:num)'] = 'app_column/index/$1';

//API
$route['api'] = 'api';
$route['api/invalid'] = 'api/invalid';
$route['api/error'] = 'api/error';
$route['api/csrf'] = 'api/csrf';