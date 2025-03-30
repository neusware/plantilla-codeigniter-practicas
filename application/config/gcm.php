<?php
/*
|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
|| Google Cloud Messaging Configurations
|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
*/

// $config['fcm_api_key'] = 'AAAAcdYA4gQ:APA91bEW0osa-9dKp2wnYa3tpiqROLlCuJgQXs0yofdbTIarH-wcuZ6zyjZ2YgviNC6AiSmNZCiEnFmny9BCbe5fSFGtpH-fd3BssekMLnYZ7wIGQCIPqGOqMOgf4cqM9VEmHqOfdDtF';
/*
|--------------------------------------------------------------------------
| API Send Address
| Rellenar [nombre_proyecto] con el nombre del proyecto de FireBase
|--------------------------------------------------------------------------
|
*/
$config['gcm_api_send_address'] = 'https://fcm.googleapis.com/v1/projects/[nombre_proyecto]/messages:send';
/*
|--------------------------------------------------------------------------
| Credentials JSON File Path
| Rellenar [nombre_archivo] con el nombre del archivo JSON con los credenciales de FireBase
|--------------------------------------------------------------------------
|
*/
$config['gcm_credentials_json_file_path'] = 'application/config/[nombre_archivo].json';
