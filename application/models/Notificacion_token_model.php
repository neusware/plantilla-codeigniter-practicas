<?php

class Notificacion_token_model extends MY_Model
{
	public $TIPO_SISTEMA_ANDROID;
	public $TIPO_SISTEMA_IOS;

	public function __construct()
	{
		$this->soft_delete = TRUE;
		$this->_table = "gcm_token";
		parent::__construct();

		$this->TIPO_SISTEMA_ANDROID = 1;
		$this->TIPO_SISTEMA_IOS = 2;
	}

	// public function saveAndroidToken($token)
	// {
	// 	$this->db->where('token',$token);
	// 	$this->db->from('android_keys');
	// 	$count = $this->db->count_all_results();
	// 	$this->db->flush_cache();
	//
	// 	$id = -1;
	//
	// 	if($count == 0)
	// 	{
	// 		$data = array(
	// 				'token' => $token,
	// 				'fecha_alta' => date("Y-m-d H:i:s"),
	// 				'ultima_actualizacion' => date("Y-m-d H:i:s"));
	// 		$this->db->insert('android_keys', $data);
	// 		$id = $this->db->insert_id();
	// 	}
	// 	else
	// 	{
	// 		$this->db->where('token',$token);
	// 		$query = $this->db->get('android_keys');
	//
	// 		foreach ($query->result() as $row)
	// 		{
	// 			return $this->updateAndroidToken($row->id,$token);
	// 		}
	// 	}
	//
	// 	return $id;
	// }
	//
	//
	// public function updateAndroidToken($id,$token)
	// {
	//
	// 	$this->db->where('id',$id);
	// 	$this->db->from("android_keys");
	// 	$count = $this->db->count_all_results();
	//
	// 	$data = array(
	// 		'token' => $token,
	// 		'ultima_actualizacion' => date("Y-m-d H:i:s"));
	//
	//
	// 	if($count > 0)
	// 	{
	// 		$this->db->where('id',$id);
	// 		$this->db->update('android_keys',$data);
	// 		$this->db->flush_cache();
	//
	// 		$this->db->where('id',$id);
	// 		$this->db->set('consulta', 'consulta+1', FALSE);
	// 		$this->db->update('android_keys');
	// 		return $id;
	// 	}
	// 	else
	// 	{
	// 		$this->db->flush_cache();
	// 		return $this->saveAndroidToken($token);
	// 	}
	//
	//
	// }

	// public function getTokens()	{
	// 	return json_decode(json_encode($this->notificacion_token->get_all()), true);
	// }

	public function getTokensForAllAndroidUsers()
	{
		// var_dump('getTokensForAllAndroidUsers');
		// var_dump($this->notificacion_token->get_many_by('tipo_sistema', $this->TIPO_SISTEMA_ANDROID));
		// die();
		return json_decode(json_encode($this->notificacion_token->get_many_by('tipo_sistema', $this->TIPO_SISTEMA_ANDROID)), true);
	}

	public function getTokensForAllIosUsers()
	{
		// var_dump('getTokensForAllIosUsers');
		// var_dump($this->notificacion_token->get_many_by('tipo_sistema', $this->TIPO_SISTEMA_IOS));
		// die();
		return json_decode(json_encode($this->notificacion_token->get_many_by('tipo_sistema', $this->TIPO_SISTEMA_IOS)), true);
	}

	// public function getTokensForThisUserIds($user_ids) {
	//   return json_decode(json_encode($this->notificacion_token->get_many_by('user_id', $user_ids)), true);
	// }

	public function getTokensForThisUserIdsOnAndroid($user_ids)
	{
		return json_decode(json_encode($this->notificacion_token->get_many_by(array('user_id' => $user_ids, 'tipo_sistema' => $this->TIPO_SISTEMA_ANDROID))), true);
	}

	public function getTokensForThisUserIdsOnIos($user_ids)
	{
		return json_decode(json_encode($this->notificacion_token->get_many_by(array('user_id' => $user_ids, 'tipo_sistema' => $this->TIPO_SISTEMA_IOS))), true);
	}

	public function sendNotifications($keysFromDatabase, $isIos = true, $datos_a_mandar)
	{
		// simple loading
		// note: you have to specify API key in config before
		$this->load->library('gcm');
		$this->gcm->clearPayload();

		//mandamos de 500 en 500 como máximo
		$keysGrouped500 = array_chunk($keysFromDatabase, 500, false);

		foreach ($keysGrouped500 as $keys) {
			$this->gcm->setTitleAndMessage($datos_a_mandar['TITULO'], $datos_a_mandar['TEXTO']);
			// set additional data
			$data = new stdClass();
			$data->TEXTO = $datos_a_mandar['TEXTO'];
			$data->EXTRA = $datos_a_mandar['EXTRA'];
			$this->gcm->setData($data);

			// TODO: setNotificationiOs solo si el dispositivo usa ios
			if ($isIos) {
				// $this->gcm->setNotificationiOs($datos_a_mandar['TITULO'], $datos_a_mandar['TEXTO']);
			}

			// also you can add time to live
			$this->gcm->setTtl(60 * 60 * 2); //2h live

			foreach ($keys as $key) {
				$this->gcm->addRecepient($key["token"]);
			}

			// or set to default
			// $this->gcm->setGroup(false);

			$this->gcm->send();
		}
	}
}

	// public function getTokens() {
  //   return $this->notificacion_token->get_all();
	// }
  //
	// public function getTokensForThisUserIds($user_ids)	{
  //   return $this->notificacion_token->get_many_by('user_id', $user_ids);
	// }

	// public function saveiOsToken($token)
	// {
	// 	$this->db->where('token',$token);
	// 	$this->db->from('ios_keys');
	// 	$count = $this->db->count_all_results();
	// 	$this->db->flush_cache();
  //
	// 	$id = -1;
  //
	// 	if($count == 0)
	// 	{
	// 		$data = array(
	// 				'token' => $token,
	// 				'fecha_alta' => date("Y-m-d H:i:s"),
	// 				'ultima_actualizacion' => date("Y-m-d H:i:s"));
	// 		$this->db->insert('ios_keys', $data);
	// 		$id = $this->db->insert_id();
	// 	}
	// 	else
	// 	{
	// 		$this->db->where('token',$token);
	// 		$query = $this->db->get('ios_keys');
  //
	// 		foreach ($query->result() as $row)
	// 		{
	// 			return $this->updateAndroidToken($row->id,$token);
	// 		}
	// 	}
  //
	// 	return $id;
	// }
  //
  //
	// public function updateiOsToken($id,$token)
	// {
	// 	$this->db->where('id',$id);
	// 	$this->db->from("ios_keys");
	// 	$count = $this->db->count_all_results();
  //
	// 	$data = array(
	// 		'token' => $token,
	// 		'ultima_actualizacion' => date("Y-m-d H:i:s"));
  //
  //
	// 	if($count > 0)
	// 	{
	// 		$this->db->where('id',$id);
	// 		$this->db->update('ios_keys',$data);
	// 		$this->db->flush_cache();
  //
	// 		$this->db->where('id',$id);
	// 		$this->db->set('consulta', 'consulta+1', FALSE);
	// 		$this->db->update('ios_keys');
	// 		return $id;
	// 	}
	// 	else
	// 	{
	// 		$this->db->flush_cache();
	// 		return $this->saveAndroidToken($token);
	// 	}
	// }
  //
  //
	// public function iOsTokens()
	// {
	// 	$query = $this->db->get('ios_keys');
	// 	return $query->result_array();
	// }
// }
