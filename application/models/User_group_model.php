<?php

class User_group_model extends MY_Model {
   public function __construct()
   {
       parent::__construct();

       $this->_table = "users_groups";

       $this->belongs_to = array( 'grupo' => array('model' => 'group_model' ,'primary_key' =>'group_id'),
                                  'usuario' => array('model' => 'user_model' ,'primary_key' =>'user_id'));
   }
}