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

  // Trazabilidad de los registros creados. Qué usuario solicita crear el registro (store en este caso)
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

  // Autenticación

  protected function _check_token () {
    // checkea que no esté en las listas de tokens no validos
    if(!(in_array($this->router->method, $this->manualTokenValidation) || in_array($this->router->method,$this->mustTokenValidation))) {
      // checkea que el token venga en los args del request
      if (!empty($this->_args[$this->config->item('rest_token_name')])) {
        // lo extrae y asigna
        $this->api_token = $this->_args[$this->config->item('rest_token_name')];
        // 
        $this->token_validation();
        $this->_check_rol();
      } else {
        $this->response(array("message" => $this->lang->line('unauthorized')),self::HTTP_UNAUTHORIZED,self::CODE_BAD);
      }
    }
  }

  // 3. verifica si con el rol del usuario, tiene acceso al metodo
  protected function _check_rol () {
    // evaluo el token
    if ($this->api_token) {
      // verifico si hay restricciones y si el metodo está en la lista de restricciones
      if(count($this->restrictions) > 0 && array_key_exists($this->router->method, $this->restrictions)) {
        // compruebo que el usuario pertenezca al grupo adecuado para darle acceso al método 

        // extraigo el id de usuario
        $user_id = $this->token->get_user_id($this->api_token);
        // extraigo los grupos asociados al usuario
        $user_groups = $this->user_group->with('grupo')->get_many_by(array('user_id' => $user_id));
        foreach ($user_groups as $key => $group) {
          // recorro cada grupo del usuario y verifico si se encuentra en groups_allowed, definido en el controlador de la entidad x
          if (in_array($group->grupo->name, $this->restrictions[$this->router->method]["groups_allowed"])) {
            return true; //concedo acceso
          }
        }
        // sin acceso
        $this->response(array("message" => $this->lang->line('unauthorized')),self::HTTP_UNAUTHORIZED,self::CODE_BAD);
      }
    } else {
      // sin acceso
      $this->response(array("message" => $this->lang->line('unauthorized')),self::HTTP_UNAUTHORIZED,self::CODE_BAD);
    }
  }

  // 2. verifica el estado del token
  protected function token_validation() {
    // con el tocken extraido en _check_token le pasa otro metodo check_token para verificar su estado
    $token_status = $this->token->check_token($this->api_token);

    // mete el valor del estado (FLAG) en un switch
    switch ($token_status) {
      case Token_model::TOKEN_OK :
        // checkea la configuracion para renovar el tocken 
        if($this->config->item('renew_token_every_call')){
          // de ser true va a renovarlo cada vez que la petición llegue aqui
          $this->token->renew_token($this->api_token); //Renew token
        }
        // checkea un valor de la base de datos, que indica si el sistema está activo o en mantenimiento 1-0-null?
        $status = $this->server_configuration->get_id_array(['id' => 1], 'active');
        $roles_can_access = 0;

        // dos restricciones para verificar que el sistema esté activo
        if($status[0] != null) {
          if($status[0] == 1) {
            // retorna el id de usuario que tiene el tocken  (tockeanble id)
            $user_id = $this->token->get_user_id($this->api_token);
            // si la consulta ha retornado algo
            if($user_id != null) {
              // obtiene los grupos a los que pertenece el usuario
              $this->mUserGroups = $this->ion_auth_model->get_users_groups($user_id)->result();
              // si pertenece a almenos un grupo, contando el tamaño del array
              if(sizeof($this->mUserGroups) > 0) {
                // verifica alguno de los grupos tiene acceso al sistema en modo 
                $gruops_can_access = $this->group->get_id_array(['id' => $this->mUserGroups[0]->id], 'maintenance_access');
                if($gruops_can_access[0] == 0) {
                  // si el grupo al que pertenece no tiene acceso va detener el flujo con un response
                  $this->response(null, self::HTTP_OK, self::CODE_SHOW_MAITENANCE_MESSAGE);
                }
              }
            }
          }
        }
        break;

      // retorna responses en caso de una flag de token no valido
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

  // Devuelve todos los registros y sus asociados
  public function all_get() {
    $elements = $this->{$this->model}->with_everything()->get_all();

    $this->response($elements,self::HTTP_OK,self::CODE_OK);
  }

  // buscador nunca lo usa, ni incorpora la lógica, solo devuelve registros paginados
  public function page_get($pagina = 1,$buscador = null) {
    $elements = $this->{$this->model}->with_everything()->paginate($pagina, array(), 10);

    $this->response($elements,self::HTTP_OK,self::CODE_OK);
  }

  /** Create_post()
   *
   * 1. Comprueba modelo
   * 2. Extrae datos del request (solo par 'data' o payload entero)
   * 3. Transforma el tipo de dato que llega a array asociativo
   * 4. Si existe par clave 'id' lo elimina, el id se gestiona en db (autoincremental)
   * 5. Trazabilidad: A partir la flag 'store_user_id' se relaciona el registro creado con el usuario que lo crea, a través del id que se extrae del token
   * 6. Insert(): Convierte el array asociativo en array, y hace un count para verificar que haya elementos antes de hacer el insert. La operación devuelve un boolean.
   * 7. Response: Comprueba el resultado de la operación con la db y determina una response u otra.
   *
   */
  public function create_post() {
    // lo va coger desde el controlador? ha de ser propiedad definida
    if($this->model != null) {
      // accede al request en "data" para extraer el valor de la clave o todo el payload del request
      $element = $this->post("data") != null ? $this->post("data") : $this->post();

      // transpila datos a array asocitivo
      if(is_string($element)) $element = json_decode($element);
      else $element = json_decode(json_encode($element));

      //try acceder clave id para eliminar el par, ya que seguramente sea autoincremental en la db
      if(isset($element->id)) unset($element->id);
      // trazabilidad, asociar el registro creado con el usuario que lo crea. store_user_id se usa como flag boolean,


      // trazabilidad
      if($this->store_user_id)
      // extrae el id de usuario que realiza la petición del token, y lo asigna a una clave de la estructura de datos
        $element->usuario_id = $this->token->get_user_id($this->api_token);

      //! Operación insert
      // Casting explicito a array y count para verificar si el array contiene datos [element->toArray->count()] | De haber elementos, inserta los datos a partir de ese array, a través del modelo y del método insert
      $element_id = count((array)$element) ? $this->{$this->model}->insert($element) : false;

      // Si existen archivos asociados los inserta
      $this->{$this->model}->uploadFilesIfExists($element_id);

      // La operación devolverá un boolean que determinará la response.
      if ($element_id) {
        $this->response(array("message" => $this->lang->line('success_crear_'.$this->language_tag), "id" => $element_id),self::HTTP_OK,self::CODE_SHOW_SUCCESS_MESSAGE);
      } else {
        $this->response(array("message" => $this->lang->line('error_crear_'.$this->language_tag)),self::HTTP_OK,self::CODE_BAD);
      }
    }
  }

  /** update_post()
   *
   * 1. Comprueba el modelo
   * 2. Trata de extraer el par en clave 'data' o el payload completo
   * 3. Transpila estructura de datos que recibe a array asociativo
   * 4. Almacena el id para seleccionar registro a actualizar
   * 5. Elimina el par en clave 'id' del array asoc, pq despues lo va usar para actualizar el registro
   * 6. Evalúa llevar a cabo la lógica para trazabilidad (boolen flag)
   * 7. Actualiza -> convierte el array asociativo a array puro y lo contea, si hay elementos, hace uso del método update(id, datos) pasándole el id para seleccionar el registro y los datos con los que editarlo.
   * 8. Evalúa el resultado de la operación, para elaborar y enviar la response
  */
  public function update_post() {
    // Comprueba el modelo
    if($this->model != null) {
      // Extrae el par en clave data o el payload completo
      $element = $this->post("data") != null ? $this->post("data") : $this->post();

      // Transpila a array asociativo
      if(is_string($element)) $element = json_decode($element);
      else $element = json_decode(json_encode($element));

      // Almacena el valor en clave id, para seleccionar el registro en el update()
      $element_id = $element->id;

      // Si existe el par, lo elimina del array asociativo
      if(isset($element->id)) unset($element->id);
      // Lógica para la trazabilidad
      if($this->store_user_id){
        $element->usuario_id = $this->token->get_user_id($this->api_token);
      }

      //! Update -> casting explícito convierte asociativo a arraypuro, lo contea, de haber elementos actualiza
      $success = count((array)$element) ? $this->{$this->model}->update($element_id,$element) : true;

      // Lógica para archivos asociados
      $this->{$this->model}->uploadFilesIfExists($element_id);

      // Evalúa la respuesta booleana de la operación determinando que response enviar
      if ($success) {
        $this->response(array("message" => $this->lang->line('success_actualizar_'.$this->language_tag), "id" => $element_id),self::HTTP_OK,self::CODE_SHOW_SUCCESS_MESSAGE);
      } else {
        $this->response(array("message" => $this->lang->line('error_actualizar_'.$this->language_tag)),self::HTTP_OK,self::CODE_SHOW_ERROR_MESSAGE);
      }
    }
  }

  /** delete_post()
   *
   * 1. Verifica el modelo
   * 2. Extrae el id del request en clave 'id' para eliminar el registro
   * 3. Elabora la consulta a partir del modelos, llama al método delete() pasándole el id para eliminar el registro.
   * 4. Evalúa la respuesta booleana de la operación determinando que response enviar, con mensajes  personalizados a partir de los langs.
   */
  public function delete_post() {
    if($this->model != null) {

      // extraigo valor en clave 'id' del request
      $id = $this->post("id");

      // !Operación delete
      // hago uso del modelo y método, para eliminar el registro por id
      $success = $this->{$this->model}->delete($id);

      // elaboro la response en función del valor boolean resultado de la operación
      if ($success) {
        $this->response(array("message" => $this->lang->line('success_eliminar_'.$this->language_tag)),self::HTTP_OK,self::CODE_SHOW_SUCCESS_MESSAGE);
      } else {
        // lang->line es una variable definida en general_lang.php a la que se le concatena el tag, aqui es null pero puede cogerlo del modelo con el que opera o del controller si está especificado
        $this->response(array("message" => $this->lang->line('error_eliminar_'.$this->language_tag)),self::HTTP_OK,self::CODE_BAD);
      }

    //   if ($success) {
    //     $message = $this->lang->line('success_eliminar_' . $this->language_tag) ?: 'El registro se eliminó correctamente.';
    //     $this->response(array("message" => $message), self::HTTP_OK, self::CODE_SHOW_SUCCESS_MESSAGE);
    // } else {
    //     $message = $this->lang->line('error_eliminar_' . $this->language_tag) ?: 'Hubo un error al eliminar el registro.';
    //     $this->response(array("message" => $message), self::HTTP_OK, self::CODE_BAD);
    // }
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
