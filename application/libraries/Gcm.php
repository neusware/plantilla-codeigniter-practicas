<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


require 'vendor/firebase/php-jwt/src/JWT.php';
require 'vendor/firebase/php-jwt/src/Key.php';

/**
 *
 * @package GCM (Google Cloud Messaging)
 * @copyright (c) 2016 Fernando Valle
 * info: https://github.com/tato469/codeigniter-gcm
 * Description: PHP Codeigniter Google Cloud Messaging Library
 * License: BSD
 *
 * Copyright (c) 2012, AntonGorodezkiy
 * All rights reserved.
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 * 3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * Modifications Copyright (c) 2016, Fernando Valle
 * All rights reserved.
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 * 3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */

class GCM {
  protected $apiSendAddress = '';
  protected $credentialsJsonFilePath = '';
  protected $fireBaseAccessToken = '';
  protected $payload = array();
  protected $additionalData = array();
  protected $recepients = array();
  protected $title = '';
  protected $message = '';
  public $status = array();
  public $messagesStatuses = array();
  public $responseData = null;
  public $responseInfo = null;
  protected $errorStatuses = array(
    'Unavailable' => 'Maybe missed API key',
    'MismatchSenderId' => 'Make sure you\'re using one of those when trying to send messages to the device. If you switch to a different sender, the existing registration IDs won\'t work.',
    'MissingRegistration' => 'Check that the request contains a registration ID',
    'InvalidRegistration' => 'Check the formatting of the registration ID that you pass to the server. Make sure it matches the registration ID the phone receives in the google',
    'NotRegistered' => 'Not registered',
    'MessageTooBig' => 'The total size of the payload data that is included in a message can\'t exceed 4096 bytes'
  );

  /**
   * Constructor
   */

  public function __construct() {
    $ci =& get_instance();
    $ci->load->config('gcm',true);
    //By default is Android
    $this->apiSendAddress = $ci->config->item('gcm_api_send_address','gcm');
    $this->credentialsJsonFilePath = $ci->config->item('gcm_credentials_json_file_path','gcm');

    if (!$this->apiSendAddress) {
      show_error('GCM: Needed API Send Address');
    }

    if (!$this->credentialsJsonFilePath) {
      show_error('GCM: Credentials JSON File Not Found');
    }

    // Carga útil genérica
    $this->request = new stdClass();
    $this->request->message = new stdClass();
    $this->request->message->notification = new stdClass();
    $this->request->message->data = new stdClass();
    $this->request->message->token = null;

    // Carga útil de Android
    $this->request->message->android = new stdClass();
    $this->request->message->android->notification = new stdClass();
    $this->request->message->android->data = new stdClass();

    $this->request->message->android->collapse_key = 'GCM Notifications';
    $this->request->message->android->priority = "high";
    $this->request->message->android->direct_boot_ok = true;

    // Carga útil de iOs
    // $this->request->message->apns = new stdClass();
    // $this->request->message->apns->payload = new stdClass();
    // $this->request->message->apns->payload->notification = new stdClass();
    // $this->request->message->apns->payload->data = new stdClass();

    // $this->request->message->apns->headers = new stdClass();
    // $this->request->message->apns->headers->{"apns-priority"} = 10;
    // $this->request->message->apns->headers->{"apns-collapse-id"} = 'GCM Notifications';
  }

  public function clearPayload() {
    // Carga útil genérica
    $this->request = new stdClass();
    $this->request->message = new stdClass();
    $this->request->message->notification = new stdClass();
    $this->request->message->data = new stdClass();
    $this->request->message->token = null;

    // Carga útil de Android
    $this->request->message->android = new stdClass();
    $this->request->message->android->notification = new stdClass();
    $this->request->message->android->data = new stdClass();

    $this->request->message->android->collapse_key = 'GCM Notifications';
    $this->request->message->android->priority = "high";
    $this->request->message->android->direct_boot_ok = true;

    // Carga útil de iOs
    // $this->request->message->apns = new stdClass();
    // $this->request->message->apns->payload = new stdClass();
    // $this->request->message->apns->payload->notification = new stdClass();
    // $this->request->message->apns->payload->data = new stdClass();

    // $this->request->message->apns->headers = new stdClass();
    // $this->request->message->apns->headers->{"apns-priority"} = 10;
    // $this->request->message->apns->headers->{"apns-collapse-id"} = 'GCM Notifications';
  }

  /**
   * Setting GCM message
   *
   * @param <string> $message
   */

  public function setTitleAndMessage($title = '', $message = '') {
    $this->title = $title;
    $this->message = $message;

    $this->request->message->notification->title = $title;
    $this->request->message->notification->body = $message;
    $this->request->message->data->title = $title;
    $this->request->message->data->body = $message;

    $this->request->message->android->notification->title = $title;
    $this->request->message->android->notification->body = $message;
    $this->request->message->android->data->title = $title;
    $this->request->message->android->data->body = $message;

    // $this->request->message->apns->payload->notification->title = $title;
    // $this->request->message->apns->payload->notification->body = $message;
    // $this->request->message->apns->payload->data->title = $title;
    // $this->request->message->apns->payload->data->body = $message;
  }

  /**
   * Setting data to message
   *
   * @param <string> $data
   */

  public function setData($data) {
    $this->request->message->data= $data;
    if ($this->title !== '') {
      $this->request->message->data->title = $this->title;
    }
    if ($this->message !== '') {
      $this->request->message->data->body = $this->message;
    }

    $this->request->message->android->data = $data;
    if ($this->title !== '') {
      $this->request->message->android->data->title = $this->title;
    }
    if ($this->message !== '') {
      $this->request->message->android->data->body = $this->message;
    }

    // $this->request->message->apns->payload->data = $data;
    // if ($this->title !== '') {
    //   $this->request->message->apns->payload->data->title = $this->title;
    // }
    // if ($this->message !== '') {
    //   $this->request->message->apns->payload->data->body = $this->message;
    // }
  }

  public function setTtl($ttl) {
    $this->request->message->android->ttl = $ttl . "s";

    // $hours = $ttl / 3600;
    // $this->request->message->apns->headers->{"apns-expiration"} = strtotime(' + ' . $hours . ' hours');
  }

  /**
   * Setting group of messages
   * @param <string> $group
  */

  public function setGroup($group = '') {
    if (!$group)
      $this->request->message->android->collapse_key = 'GCM Notifications';
    else
      $this->request->message->android->collapse_key = $group;
  }

  /**
   * Adding one recepient
   *
   * @param <string> $group
   */

  public function addRecepient($registrationId) {
    array_push($this->recepients, $registrationId);
  }

  /**
   * Setting all recepients
   *
   * @param <string> $group
   */

  public function setRecepients($registrationIds) {
    $this->recepients = $registrationIds;
  }

  /**
   * Clearing group of messages
   */

  public function clearRecepients() {
    $this->recepients = array();
  }

  /**
   * Obtaining FireBase Access Token
   */

  protected function setFireBaseAccessToken() {
    $jsonInfo = json_decode(file_get_contents($this->credentialsJsonFilePath), true);

    $now_seconds = time();

    $privateKey = $jsonInfo['private_key'];

    $payload = [
        'iss' => $jsonInfo['client_email'],
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud' => $jsonInfo['token_uri'],
        //Token to be expired after 1 hour
        'exp' => $now_seconds + (60 * 60),
        'iat' => $now_seconds
    ];

    $jwt = JWT::encode($payload, $privateKey, 'RS256');

    // create curl resource
    $ch = curl_init();

    // set post fields
    $post = [
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
    ];

    $ch = curl_init($jsonInfo['token_uri']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    // execute!
    $response = curl_exec($ch);

    // close the connection, release resources used
    curl_close($ch);

    // do anything you want with your response
    $jsonObj = json_decode($response, true);

    $this->fireBaseAccessToken = $jsonObj['access_token'];
  }

  /**
   * Senging messages to Google Cloud Messaging
   *
   * @param <string> $group
   */

  public function send() {
    $this->recepients = array_unique($this->recepients);
    sort($this->recepients);

    $all_messages_sent = true;

    foreach ($this->recepients as $key => $token) {
      $this->request->message->token = $token;
      $data = json_encode($this->request);
      $all_messages_sent = $all_messages_sent && $this->request($data);
    }

    return $all_messages_sent;
  }

  protected function request($data) {
    // Obtain FireBase Access Token
    $this->setFireBaseAccessToken();
    $headers[] = 'Content-Type:application/json';
    $headers[] = 'Authorization: Bearer ' . $this->fireBaseAccessToken;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $this->apiSendAddress);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_HEADER, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    $this->responseData = curl_exec($curl);
    $this->responseInfo = curl_getinfo($curl);
    curl_close($curl);

    return $this->parseResponse();
  }

  protected function parseResponse() {
    if ($this->responseInfo['http_code'] == 200) {
      $this->status = array(
        'error' => 0,
        'message' => 'The message was sent successfully'
      );

      return true;
    } elseif ($this->responseInfo['http_code'] == 400) {
      $this->status = array(
        'error' => 1,
        'message' => 'Request could not be parsed as JSON'
      );

      return false;
    } elseif ($this->responseInfo['http_code'] == 401) {
      $this->status = array(
        'error' => 1,
        'message' => 'There was an error authenticating the sender account'
      );

      return false;
    } elseif ($this->responseInfo['http_code'] == 500) {
      $this->status = array(
        'error' => 1,
        'message' => 'There was an internal error in the GCM server while trying to process the request'
      );

      return false;
    } elseif ($this->responseInfo['http_code'] == 503) {
      $this->status = array(
        'error' => 1,
        'message' => 'Server is temporarily unavailable'
      );

      return false;
    } else {
      $this->status = array(
        'error' => 1,
        'message' => 'Status undefined'
      );

      return false;
    }
  }

}
