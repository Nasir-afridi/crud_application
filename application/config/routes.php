<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'users';
$route['users/add'] = 'users/add';
$route['users/save'] = 'users/save';
$route['users/edit/(:num)'] = 'users/edit/$1';
$route['users/update/(:num)'] = 'users/update/$1';
$route['users/delete/(:num)'] = 'users/delete/$1';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
