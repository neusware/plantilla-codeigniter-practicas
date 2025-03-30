<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ServerConfiguration extends MY_Controller {

  public function __construct() {
    $this->model = "server_configuration";
    $this->auth_enabled = FALSE;
    parent::__construct();
  }

  public function getMaintenanceStatus_get() {
    $status = $this->{$this->model}->get_id_array(['id' => 1], 'active');
    $roles_can_access = 0;

    if($status[0] == null) {
      $status[0] = 0;
    }

    if($status[0] == 1) {
      $gruops_can_access = $this->group->get_id_array(['maintenance_access' => 1], 'id');
      $roles_can_access = sizeof($gruops_can_access);
    }

    $this->response(['status' => $status[0], 'roles_can_access' => $roles_can_access],self::HTTP_OK,self::CODE_OK);
  }

}
