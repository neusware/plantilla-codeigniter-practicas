<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// *** Tokens ***
/* Default table schema:
 * CREATE TABLE `api_tokens` (
    `api_token_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `token` VARCHAR(50) NOT NULL,
    `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`api_token_id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
 */
$config['rest_token_name'] = 'X-Token';
$config['rest_tokens_table'] = 'api_tokens';
$config['rest_token_expire'] = '3600'; // Token expires in one hour if does not call the API.
$config['renew_token_every_call'] = TRUE; // Token renews other rest_token_expire time when does a call.
