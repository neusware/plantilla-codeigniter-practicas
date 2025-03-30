<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Token_model extends MY_Model{


  const TOKEN_LAPSED = -1;
  const TOKEN_INVALID = 0;
  const TOKEN_OK = 1;

  public function __construct() {
    parent::__construct();

    $this->_table = $this->config->item('rest_tokens_table');
    $this->primary_key = 'api_token_id';

    $this->before_create = ['checkForRemoveTokens'];
    $this->before_update = ['checkForRemoveTokens'];
  }

  public function create_token($user_id) {
      $date = new DateTime();

      // ***** Generate Token *****
      $char = "bcdfghjkmnpqrstvzBCDFGHJKLMNPQRSTVWXZaeiouyAEIOUY";
      $token = '';
      for ($i = 0; $i < 47; $i++) $token .= $char[(rand() % strlen($char))];

      $this->token->insert(['user_id'=> $user_id, 'token' => $token, 'created' => $date->format('Y-m-d H:i:s'), 'last_renew' => $date->format('Y-m-d H:i:s')]);

      return $token;
  }

  // Return TOKEN_LAPSED, TOKEN_INVALID or TOKEN_OK
  public function check_token($token)
  {
    $query = $this->db->from($this->config->item('rest_tokens_table'))->where(array("token" => $token))->get();
    $tokenObject = $query->row();
    if($tokenObject) {
      // return true;
      $date = new DateTime();
      $last_renew = DateTime::createFromFormat('Y-m-d H:i:s', $tokenObject->last_renew);
      $interval = $date->getTimestamp() - $last_renew->getTimestamp();

      return ($interval < $this->config->item('rest_token_expire')) ? 1 : -1;
    }
    else
      return 0;
  }

  public function renew_token($token)
  {
    $date = new DateTime();
    $this->primary_key = 'token';
    $this->token->update($token, ['last_renew' => $date->format('Y-m-d H:i:s')]);
    $this->primary_key = 'api_token_id';
  }

  public function get_user_id($token)
  {
    if($this->check_token($token))
    {
      $query = $this->db->from($this->config->item('rest_tokens_table'))->where(array("token" => $token))->get();
      $token = $query->row();
      return $token->user_id;
    }
    else
    {
      return null;
    }

  }


  public function is_unique_token($token){
    $this->db->select('*')->from($this->config->item('rest_tokens_table'))->where('token',$token);
    if($this->db->count_all_results() == 0)
      $status = self::SaveToken($token);
    else
      $status = false;
    return $status;
  }


  public function ListTokens($limit = 10){
    $this->db->select('*')->from($this->config->item('rest_tokens_table'))->order_by('token','RAND')->limit($limit);
    return $this->db->get()->result();
  }

  public function checkForRemoveTokens($data) {

    $this->db->where('datediff(now(), created) > 2');
    $this->db->delete($this->config->item('rest_tokens_table'));

    return $data;
  }
}
?>
