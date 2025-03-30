<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserGroup extends MY_Controller {

  public function __construct() {
    $this->model = "user_group";

    // $this->auth_enabled = false;
    parent::__construct();
  }

}
