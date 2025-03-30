<?php

class Group_model extends MY_Model {
 public function __construct()
 {
     parent::__construct();

        $this->_table = "groups";
        $this->has_many = array(
          'users' => array('model' => 'user_group_model' ,'primary_key' =>'group_id')
        );
 }
}
