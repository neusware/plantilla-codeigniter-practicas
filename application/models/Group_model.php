<?php

class Group_model extends MY_Model {
 public function __construct()
 {
     parent::__construct();

        $this->_table = "groups";
        // grupos pertenecen a muchos usuarios en el modelo user_group_model en pk (group_id) [1:N]
        $this->has_many = array(
          'users' => array('model' => 'user_group_model' ,'primary_key' =>'group_id')
        );
 }
}
