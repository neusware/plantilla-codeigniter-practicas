<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends MY_Controller {

  public function __construct() {
  	$this->dropdownLabel = "description";
    $this->model = "group";
    // $this->auth_enabled = false;

    parent::__construct();
    //$this->load->model('Presupuesto_model');
  }

}
