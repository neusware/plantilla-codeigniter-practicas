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
|	https://codeigniter.com/user_guide/general/routing.html
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

$route['default_controller'] = 'vue';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// For ajax request from vue must add the route like this:
$route['api/example/list'] = 'example/list';

$route['api/external/login'] = 'external/login';
$route['api/auth/info'] = 'auth/info';
$route['api/auth/logout'] = 'auth/logout';

// Notificaciones push para móviles (ios y android)
$route['api/notificacion_token/saveGcmToken'] = 'notificacionToken/saveGcmToken';

// Server Configuration
$route['api/serverConfiguration/getMaintenanceStatus'] = 'serverConfiguration/getMaintenanceStatus';

// User
$route['api/user/createUser'] = 'user/createUser';
$route['api/user/getAllUsers/page/(:num)'] = 'user/getAllUsers/$1'; // $1 = numero de pagina
$route['api/user/getFilteredUsers'] = 'user/getFilteredUsers';
$route['api/user/updateUserImage'] = 'user/updateUserImage';
$route['api/user/deleteUserImage/(:num)'] = 'user/deleteUserImage/$1'; // $1 = user_id
$route['api/user/phpVersion'] = 'user/phpVersion'; // $1 = user_id

// External
$route['api/external/forgotPassword'] = 'external/forgotPassword';
$route['api/external/changePassword'] = 'external/changePassword';

// TODO: insertar aquí las nuevas rutas
// rutas genericas MY_controller
// :any es el parametro en super == autoload. Hay que tener clara la nomenclatura de los nombres
$route['api/(:any)/all'] = '$1/all'; // $1 -> placeholder dinámico (/:i)
$route['api/(:any)/page/(:num)'] = '$1/page/$2';
$route['api/(:any)/page/(:num)/(:any)'] = '$1/page/$2/$3';
$route['api/(:any)/create'] = '$1/create';
$route['api/(:any)/update'] = '$1/update';
$route['api/(:any)/delete'] = '$1/delete';
$route['api/(:any)/dropdown'] = '$1/dropdown';
$route['api/(:any)/filteredDropdown'] = '$1/filteredDropdown';
$route['api/(:any)/dropdownById/(:num)'] = '$1/dropdownById/$2';
$route['api/(:any)/data/(:num)'] = '$1/data/$2';
$route['api/(:any)/uploadFile'] = '$1/uploadFile';
$route['api/(:any)/file/(:any)/(:any)/(:any)'] = '$1/file/$2/$3/$4';

// -------------------------rutas providers

// $route['api/(:any)/page/(:num)'] = '$1/page/$2';
$route['api/provider/getAllProviders/page/(:num)'] = 'provider/getAllProviders/$1';

// $route['api/(:any)/data/(:num)'] = '$1/data/$2';
$route['api/provider/getProvider/(:num)'] = 'provider/getProvider/$1';

$route['api/provider/getFilteredProviders'] = 'provider/getFilteredProviders';

// $route['api/(:any)/delete'] = '$1/delete';

// TODO Update e Insert (no se pueden los d user pq usan auth), y demas métodos para archivos asociados (imagen)


//! ojo con el orden
// Everything that is not API is redirected to vue page
$route['(.+)'] = 'vue/index';
