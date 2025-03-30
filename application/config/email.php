<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['email_config']['bcc_batch_mode'] = true;
$config['email_config']['protocol'] = 'smtp';
$config['email_config']['smtp_host'] = 'smtp.gmail.com';
$config['email_config']['smtp_port'] = 587;
$config['email_config']['smtp_user'] = 'no-reply@signlab.es';
$config['email_config']['smtp_pass'] = '123Signlab.';
$config['email_config']['smtp_crypto'] = 'tls';
$config['email_config']['mailtype'] = 'html';
$config['email_config']['charset'] = 'utf-8';
$config['email_config']['newline'] = "\r\n";