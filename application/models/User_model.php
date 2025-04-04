<?php

class User_model extends MY_Model {

  public function __construct() {
    parent::__construct();

    $this->_table = "users";
    $this->upload_fields = ["imagen" => "imagenes"];
    $this->upload_file_config['allowed_types'] = "jpg|png|jpeg";
    $this->after_get[] = 'cleanFields';

    // necesario para la funcion checkRestrict en MY_Controller ( so los nombres en singular y plural para el mensaje del response )
    $this->singular_name = 'usuario';
    $this->plural_name = 'usuarios';

    // hay muchos registros usuario en el modelo (user_group_model) en pk (user_id) [1:N]
    $this->has_many = array(
      'grupos' => array('model' => 'user_group_model' ,'primary_key' =>'user_id')
    );

    // Comentado para que no interfiera en la subida de imagen del usuario
    // TODO: crear algún flag para que esta validación se utilice o no en el update
    // $this->validate = array(
    //   'email' => [
    //     'field' => 'email',
    //     'label' => 'Email',
    //     'rules' => 'required|is_unique[users.email.id]',
    //   ],
    //   'username' => [
    //     'field' => 'username',
    //     'label' => 'Username',
    //     'rules' => 'required|is_unique[users.username.id]',
    //   ]
    // );
  }

  public function cleanFields($user) {
    unset($user->ip_address);
    unset($user->password);
    unset($user->salt);
    unset($user->activation_code);
    unset($user->forgotten_password_code);
    unset($user->forgotten_password_time);
    unset($user->remember_code);

    return $user;
  }

  public function cleanFieldsMany($users) {
    foreach ($users as $user) {
      unset($user->ip_address);
      unset($user->password);
      unset($user->salt);
      unset($user->activation_code);
      unset($user->forgotten_password_code);
      unset($user->forgotten_password_time);
      unset($user->remember_code);
    }
  }
}
