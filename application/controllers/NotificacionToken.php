<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class NotificacionToken extends MY_Controller {

  public function __construct() {
    $this->model = "notificacion_token";
    // $this->auth_enabled = false;
    parent::__construct();
  }

	public function saveGcmToken_post() {
    $gcm_token = $this->post('token');
    $user_id = $this->post('user_id');
    $gcm_token_id = $this->post('gcm_token_id');
    $tipo_sistema = $this->post('tipo_sistema');

    if($gcm_token_id) {
      $token_table_data = $this->notificacion_token->with_deleted()->get_by(array('id' => $gcm_token_id, 'user_id' => $user_id));

      // Si existe en la BD
      if ($token_table_data) {
        // Si existe en la base de datos y el token que tiene es distinto al que tenemos guardado
        if ($token_table_data->token == $gcm_token) {
          $data = array('ultima_actualizacion' => date("Y-m-d H:i:s"));

          $this->actualizarDatosToken($gcm_token_id, $data);
        } else {
          $data = array(
            'token' => $gcm_token,
            'ultima_actualizacion' => date("Y-m-d H:i:s"),
            'tipo_sistema' => $tipo_sistema
          );

          $this->actualizarDatosToken($gcm_token_id, $data);
        }
      } else {
        $this->response(array('id_token' => null, 'error' => 'Datos erróneos, prueba sin el gcm_token_id'), self::HTTP_OK, self::CODE_BAD);
      }
    } else {
      // no se ha recibido token_id
      $token_table_data_only_by_token = $this->notificacion_token->with_deleted()->get_by(array('token' => $gcm_token, 'user_id' => $user_id));

      if ($token_table_data_only_by_token) {
        // existe ese token para el usuario que se ha pasado, así que se actualiza la fecha y devuelve el token_id
        $data = array('ultima_actualizacion' => date("Y-m-d H:i:s"), 'tipo_sistema' => $tipo_sistema);

        $this->actualizarDatosToken($token_table_data_only_by_token->id, $data);
      } else {
        //no existe ese token para el usuario que se ha pasado, así que se crea
        $data = array(
          'token' => $gcm_token,
          'user_id' => $user_id,
          'fecha_alta' => date("Y-m-d H:i:s"),
          'ultima_actualizacion' => date("Y-m-d H:i:s"),
          'tipo_sistema' => $tipo_sistema
        );
        $id = $this->notificacion_token->insert($data);
        if($id) {
          $this->response(array('id_token' => intval($id)), self::HTTP_OK, self::CODE_OK);
        } else {
          $this->response(array('id_token' => null, 'error' => 'Error al crear datos del token'), self::HTTP_OK, self::CODE_BAD);
        }
      }
    }
	}

  private function actualizarDatosToken($id, $datos) {
    $consulta = $this->notificacion_token->with_deleted()->get_by(array('id' => $id))->consulta;
    $datos['consulta'] = $consulta + 1;
    $datos['deleted'] = 0;

    if($this->notificacion_token->update($id, $datos)) {
      $this->response(array('id_token' => intval($id)), self::HTTP_OK, self::CODE_OK);
    } else {
      $this->response(array('id_token' => null, 'error' => 'Error al actualizar datos del token'), self::HTTP_OK, self::CODE_BAD);
    }
  }

}
