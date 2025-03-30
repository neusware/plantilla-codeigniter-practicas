<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */

require APPPATH . 'core/REST_Controller.php';

/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */

class MY_Controller extends REST_Controller {

	const CODE_OK = 20000;
	const CODE_BAD = 40000;
  const CODE_SESSION_EXPIRED = 50008;
  const CODE_SHOW_SUCCESS_MESSAGE = 20001;
	const CODE_SHOW_WARNING_MESSAGE = 30001;
  const CODE_SHOW_ERROR_MESSAGE = 40001;
  const CODE_SHOW_MAITENANCE_MESSAGE = 90001;

	protected $mUser = null;
  protected $mUserGroups = null;
  protected $model = null;
  protected $language_tag = null;

  //Dropdown
  protected $dropdownLabel = "nombre";
  protected $dropDownValue = "id";

	// Field that has the name of the file which is going to be downloaded
  protected $file_name_field = null;

  protected $store_user_id = FALSE;

  // Add every method that not needs to be checked or if are manually checked.
  protected $manualTokenValidation = [];
  protected $restrictions = [];
  private $mustTokenValidation = ["file"];


	public function __construct()	{
    if($this->language_tag == null) $this->language_tag = $this->model;

		parent::__construct();

		$this->load->library(array('ion_auth'));
    $this->load->library('email');
    $this->email->initialize($this->config->item('email_config'));

    if($this->auth_enabled) {
      $user_id = $this->token->get_user_id($this->api_token);
      $this->mUser = $this->ion_auth_model->user($user_id)->result();
      $this->mUserGroups = $this->ion_auth_model->get_users_groups($user_id)->result();
    }
	}

	public function response($data = NULL, $http_code = NULL, $code = NULL, $continue = FALSE) {
		$code = $code != NULL ? $code : self::CODE_OK;

		parent::response(array("code" => $code, "data" => $data), $http_code, $continue);
	}

  protected function _check_token () {
    if(!(in_array($this->router->method, $this->manualTokenValidation) || in_array($this->router->method,$this->mustTokenValidation))) {
      if (!empty($this->_args[$this->config->item('rest_token_name')])) {
        $this->api_token = $this->_args[$this->config->item('rest_token_name')];
        $this->token_validation();
        $this->_check_rol();
      } else {
        $this->response(array("message" => $this->lang->line('unauthorized')),self::HTTP_UNAUTHORIZED,self::CODE_BAD);
      }
    }
  }

  protected function _check_rol () {
    if ($this->api_token) {
      if(count($this->restrictions) > 0 && array_key_exists($this->router->method, $this->restrictions)) {
        $user_id = $this->token->get_user_id($this->api_token);
        $user_groups = $this->user_group->with('grupo')->get_many_by(array('user_id' => $user_id));
        foreach ($user_groups as $key => $group) {
          if (in_array($group->grupo->name, $this->restrictions[$this->router->method]["groups_allowed"])) {
            return true;
          }
        }
        $this->response(array("message" => $this->lang->line('unauthorized')),self::HTTP_UNAUTHORIZED,self::CODE_BAD);
      }
    } else {
      $this->response(array("message" => $this->lang->line('unauthorized')),self::HTTP_UNAUTHORIZED,self::CODE_BAD);
    }
  }

  protected function token_validation() {
    $token_status = $this->token->check_token($this->api_token);

    switch ($token_status) {
      case Token_model::TOKEN_OK :
        if($this->config->item('renew_token_every_call')){
          $this->token->renew_token($this->api_token); //Renew token
        }
        $status = $this->server_configuration->get_id_array(['id' => 1], 'active');
        $roles_can_access = 0;

        if($status[0] != null) {
          if($status[0] == 1) {
            $user_id = $this->token->get_user_id($this->api_token);
            if($user_id != null) {
              $this->mUserGroups = $this->ion_auth_model->get_users_groups($user_id)->result();
              if(sizeof($this->mUserGroups) > 0) {
                $gruops_can_access = $this->group->get_id_array(['id' => $this->mUserGroups[0]->id], 'maintenance_access');
                if($gruops_can_access[0] == 0) {
                  $this->response(null, self::HTTP_OK, self::CODE_SHOW_MAITENANCE_MESSAGE);
                }
              }
            }
          }
        }
        break;

      case Token_model::TOKEN_LAPSED :
        $this->response(array("error" => $this->lang->line('lapsed_session')),self::HTTP_OK,self::CODE_SESSION_EXPIRED);
        break;

      case Token_model::TOKEN_INVALID :
        $this->response(array("message" => $this->lang->line('unauthorized')),self::HTTP_UNAUTHORIZED,self::CODE_BAD);
        break;

      default:
        $this->response(array("message" => $this->lang->line('unauthorized')),self::HTTP_UNAUTHORIZED,self::CODE_BAD);
        break;
    }
  }

  public function all_get() {
    $elements = $this->{$this->model}->with_everything()->get_all();

    $this->response($elements,self::HTTP_OK,self::CODE_OK);
  }

  public function page_get($pagina = 1,$buscador = null) {
    $elements = $this->{$this->model}->with_everything()->paginate($pagina, array(), 10);

    $this->response($elements,self::HTTP_OK,self::CODE_OK);
  }

  public function create_post() {
    if($this->model != null) {
      $element = $this->post("data") != null ? $this->post("data") : $this->post();

      if(is_string($element)) $element = json_decode($element);
      else $element = json_decode(json_encode($element));

      if(isset($element->id)) unset($element->id);
      if($this->store_user_id)
        $element->usuario_id = $this->token->get_user_id($this->api_token);

      $element_id = count((array)$element) ? $this->{$this->model}->insert($element) : false;
      $this->{$this->model}->uploadFilesIfExists($element_id);

      if ($element_id) {
        $this->response(array("message" => $this->lang->line('success_crear_'.$this->language_tag), "id" => $element_id),self::HTTP_OK,self::CODE_SHOW_SUCCESS_MESSAGE);
      } else {
        $this->response(array("message" => $this->lang->line('error_crear_'.$this->language_tag)),self::HTTP_OK,self::CODE_BAD);
      }
    }
  }

  public function update_post() {
    if($this->model != null) {
      $element = $this->post("data") != null ? $this->post("data") : $this->post();

      if(is_string($element)) $element = json_decode($element);
      else $element = json_decode(json_encode($element));

      $element_id = $element->id;

      if(isset($element->id)) unset($element->id);
      if($this->store_user_id){
        $element->usuario_id = $this->token->get_user_id($this->api_token);
      }

      $success = count((array)$element) ? $this->{$this->model}->update($element_id,$element) : true;
      $this->{$this->model}->uploadFilesIfExists($element_id);

      if ($success) {
        $this->response(array("message" => $this->lang->line('success_actualizar_'.$this->language_tag), "id" => $element_id),self::HTTP_OK,self::CODE_SHOW_SUCCESS_MESSAGE);
      } else {
        $this->response(array("message" => $this->lang->line('error_actualizar_'.$this->language_tag)),self::HTTP_OK,self::CODE_SHOW_ERROR_MESSAGE);
      }
    }
  }

  public function delete_post() {
    if($this->model != null) {
      $id = $this->post("id");
      $success = $this->{$this->model}->delete($id);

      if ($success) {
        $this->response(array("message" => $this->lang->line('success_eliminar_'.$this->language_tag)),self::HTTP_OK,self::CODE_SHOW_SUCCESS_MESSAGE);
      } else {
        $this->response(array("message" => $this->lang->line('error_eliminar_'.$this->language_tag)),self::HTTP_OK,self::CODE_BAD);
      }
    }
  }

  public function dropdown_get() {
    $dropdown = $this->{$this->model}->dropdownVue($this->dropdownLabel,$this->dropDownValue);

    if ($dropdown != null){
      $this->response($dropdown,self::HTTP_OK,self::CODE_OK);
    } else {
      $this->response(array("message" => $this->lang->line('error_dropdown')),self::HTTP_OK,self::CODE_BAD);
    }
  }

  public function data_get($element_id) {
    $element = $this->{$this->model}->with_everything()->get($element_id);
    $this->response($element,self::HTTP_OK,self::CODE_OK);
  }

  public function uploadFile_post() {
    $elementId = $this->post("id");
    $field = $this->post("field");

    if($this->{$this->model}->uploadFile($elementId,$field)) {
      $this->response(array("message" => $this->lang->line('success_subida_'.$this->language_tag)),self::HTTP_OK,self::CODE_SHOW_SUCCESS_MESSAGE);
    } else {
      $this->response(array("message" => $this->lang->line('error_subida_'.$field)),self::HTTP_OK,self::CODE_SHOW_ERROR_MESSAGE);
    }
  }

	public function dropdownWhere_get($dropdownWhereKey, $dropdownWhereValue) {
  	$dropdown = $this->{$this->model}->dropdownVue($this->dropdownLabel,$this->dropDownValue);
	 	if ($dropdown != null) {
    	for ($i=count($dropdown); $i > 0 ; $i--) {
      	if($dropdown[$i - 1]->$dropdownWhereKey !== floatval($dropdownWhereValue)) {
        	array_splice($dropdown, ($i - 1), 1);
      	}
    	}
    	$this->response($dropdown,self::HTTP_OK,self::CODE_OK);
  	} else {
    	$this->response(array("message" => $this->lang->line('error_actualizar_'.$this->language_tag)),self::HTTP_OK,self::CODE_BAD);
  	}
	}

  public function file_get($token, $field, $file_name) {
		error_reporting(0);
		$record = json_decode(json_encode($this->{$this->model}->get_by(array($field => $file_name))), true);
		$nombre_archivo_descarga = $record[$this->file_name_field];

    if($this->auth_enabled == FALSE || $this->token->check_token($token) > 0) {
      $this->load->helper('download');
      $file_name = urldecode($file_name);
      $content = file_get_contents($this->{$this->model}->get_folder($field).$file_name);
      $ext = pathinfo($file_name, PATHINFO_EXTENSION);
      if($ext == "jpg" || $ext == "jpeg") {
        header("Content-Type: image/jpeg");
      } else if($ext == "png") {
        header("Content-Type: image/png");
      } else {
				// TODO: buscar más posibilidades para descargar (excel, word, ...) según sean necesarias
				if ($ext == 'pdf') {
					header("Content-type:application/pdf");
				}
				// header('Content-Disposition: attachment; filename="' . $this->{$this->model}->customAttachedName($ext) . '"');
				header('Content-Disposition: attachment; filename="' . $this->{$this->model}->customAttachedName($ext, $nombre_archivo_descarga) . '"');
				readfile($content.'/'.$file_name);
        // force_download($file_name, $content);
      }
      echo $content;
    } else {
      echo null;
    }
  }

  public function filteredDropdown_post() {
    $filter = $this->post("data");
    $dropdown_keys = $this->dropdownLabel;
    $dropdown_value = $this->dropDownValue;
    $separator=" ";
    $dropdown_keys = is_array($dropdown_keys) ? $dropdown_keys : array($dropdown_keys);
    $where = [];
    foreach ($filter as $key => $value) {
      if(date_parse($value)['year']){ // si el filtro es una fecha
          $where[$key] = $value;
      } else if(is_string($value)) { //si el filtro es una string
          $where[$key." LIKE"] = "%$value%";
      } else {
          $where[$key] = $value;
      }
    }
		$elements = $this->{$this->model}->order_by($dropdown_keys[0])->get_many_by($where);
		$dropdown = array();
		foreach ($elements as $element) {
			$element->label = $element->{$dropdown_keys[0]};
			for ($i=1; $i < sizeof($dropdown_keys); $i++) {
				$element->label = $element->label.$separator.$element->{$dropdown_keys[$i]};
			}
			$element->value = $element->{$dropdown_value};
			$dropdown[] = $element;
		}

    if ($dropdown != null){
      $this->response($dropdown,self::HTTP_OK,self::CODE_OK);
    } else {
      $this->response($this->lang->line('error_dropdown_'.$this->model),self::HTTP_OK,self::CODE_SHOW_WARNING_MESSAGE);
    }
  }

  public function dropdownById_get($id) {
    $dropdown_keys = $this->dropdownLabel;
    $dropdown_value = $this->dropDownValue;
    $separator=" ";
    $dropdown_keys = is_array($dropdown_keys) ? $dropdown_keys : array($dropdown_keys);
    $element = $this->{$this->model}->order_by($dropdown_keys[0])->get_by(array('id' => $id));
    $dropdown = array();
    $element->label = $element->{$dropdown_keys[0]};
    for ($i=1; $i < sizeof($dropdown_keys); $i++) {
      $element->label = $element->label.$separator.$element->{$dropdown_keys[$i]};
    }
    $element->value = $element->{$dropdown_value};
    $dropdown[] = $element;

    if ($dropdown != null){
      $this->response($dropdown,self::HTTP_OK,self::CODE_OK);
    } else {
      $this->response($this->lang->line('error_dropdown_'.$this->model),self::HTTP_OK,self::CODE_SHOW_WARNING_MESSAGE);
    }
  }

  protected function notificar($user_id, $titulo, $mensaje) {
    if(!is_array($user_id)){
      $user_id = array($user_id);
    }
    foreach ($user_id as $key => $id) {
      $user_tokens = $this->notificacion_token->getTokensForThisUserIds($id);
      $this->gcm->clearPayload();
      $this->gcm->setTitleAndMessage($titulo, $mensaje);
      $data = new stdClass();
      $data->TEXTO = $titulo;
      $this->gcm->setData($data);
      $this->gcm->setTtl(7200); //2h live

      foreach ($user_tokens as $token) {
          $this->gcm->addRecepient($token["token"]);
      }

      $this->gcm->send();
    }
  }
}
