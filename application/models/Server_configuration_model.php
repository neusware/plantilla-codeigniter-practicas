<?php

class Server_configuration_model extends MY_Model {
   public function __construct()
   {
       parent::__construct();

       $this->_table = "server_configuration";
   }
}