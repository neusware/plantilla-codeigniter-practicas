<?php
defined('BASEPATH') or exit('No direct script access allowed');

// herencia de clase base
class User extends MY_Controller
{

  // constructor
  public function __construct()
  {

    $this->dropdownLabel = array("first_name", "last_name");
    // referencia al modelo
    $this->model = "user";
    $this->upload_fields = 'imagen';

    // restricciones para usar metodos, checkea _check_rol en MY_Controller
    $this->restrictions = array(
      "getFilteredUsers" => array(
        "groups_allowed" => ["admin"]
      )
    );
    // $this->auth_enabled = false;
    // inicializa instancia con el constructor de la clase padre
    parent::__construct();
  }

  //getPaginateUsers (userapi)?
  public function getAllUsers_get($pagina)
  {

    //  declaro restricción para consulta
    $where = [
      'is_hidden' => false
    ];

    // consulta, cascade para tablas N:N
    $elements = $this->user->with_everything()->cascade('grupo')->paginate($pagina, $where, 10);
    // elabora response ->  response($data = NULL, $http_code = NULL, $code = NULL, $continue = FALSE
    $this->response($elements, self::HTTP_OK, self::CODE_OK);
  }

  // se corresponde con el metodo de mismo nombre userAPI.js -
  public function getFilteredUsers_post()
  {
    // obtiene el filtro, par key-value [buscador]
    $buscador = $this->post('filter')['buscador'];
    // consulta obtiene la paginación
    $page = $this->post('page');
    // consulta generica, a partir del modelo, el uso de metodos asume que los incorpora
    $query = $this->user->with_everything()->cascade('grupo');
    // divido el filtro en múltiples palabras clave para optimizar la consulta
    $explodedBuscador = $buscador ? explode("%20", $buscador) : null;

    // clausula de la consulta, igual para soft deletes
    $where = [
      'is_hidden' => false
    ];

    // si hay buscador
    if ($buscador !== null) {
      // $query = $query->or_like_where(["username","first_name","last_name"], $explodedBuscador, $separation= "OR");
      //  consulta a la db con clausulas LIKE o OR (separation), para query por valor en buscador. Concatenando nombre,apellido (nombrecompleto), telefono y email
      $query = $query->or_like_where(["concat(first_name, ' ', last_name)", "phone", "email"], $explodedBuscador, $separation = "OR");
    }

    // paginacón de la query
    $elements = $query->paginate($page, $where, 10);

    // retorna la response
    $this->response($elements, self::HTTP_OK, self::CODE_OK);
  }

  public function create_post()
  {
    // obtiene los datos
    $data = $this->post("data") != null ? $this->post("data") : $this->post();
    // evalua el tipado, asumiendo que es una cadena string JSON
    // si es así decodifica el json en un array asociativo (2o param true), para poder tratarlo
    if (is_string($data)) $data = json_decode($data, true);
    // si no es una cadena json, codifica los datos a json y los descodifica para convertirllos en array asociativo
    else $data = json_decode(json_encode($data), true);

    // viene a asegurar que los datos que entran siempre se traten aqui como array asociativo
    // json_encode Returns the JSON representation of a value, value encoded can be any type except resource
    /* function json_decode(
    string $json,
    bool $associative = null,
    int $depth = 512,
    int $flags = 0
): mixed {  */

    // extrae datos extra de la request
    $user_extra_data = [
      'phone' => $data['phone'],
      'first_name' => $data['first_name'],
      'last_name' => $data['last_name']
    ];


    $grupos = array($data["roles"]);

    // valida que esté autenticado, obteniendo algun dato ($id)
    $id = $this->ion_auth->register(
      strtolower($data["email"]),
      $data["password"],
      strtolower($data["email"]),
      $user_extra_data,
      $grupos
    );

    // en función del valor del $id
    if ($id) {
      //! ejecuto metodo subir base datos ?? y retorno respuesta, no entiendo pq le pasa el id??
      $return = $this->user->uploadFilesIfExists($id);
      $this->response(array("message" => $this->lang->line('success_crear_' . $this->language_tag), "id" => $id), self::HTTP_OK, self::CODE_SHOW_SUCCESS_MESSAGE);
    } else {
      $this->response(array("error" => $this->lang->line('error_crear_' . $this->language_tag)), self::HTTP_OK, self::CODE_BAD);
    }
  }

  public function update_post()
  {
    // Verifica que viene los datos en clave data y almacena
    $data = $this->post("data") != null ? $this->post("data") : $this->post();
    // convierte en array asociativo
    if (is_string($data)) $data = json_decode($data, true);
    else $data = json_decode(json_encode($data), true);

    // saca el id de usuario del request, para usarlo en el update del registro
    $id = $data["id"];

    // trata de comprobar si esta seteada la variable roles para almacenarla y eliminarla del array
    if (isset($data["roles"])) {
      $grupos = $data["roles"];
      unset($data["roles"]);
    }
    // elimina del array la calve id
    unset($data["id"]);

    // !actualiza a través de libreria que gestionará la autenticación de forma automática
    $updated = $this->ion_auth->update($id, $data);

    // devuelve un booleano
    if ($updated) {
      // se suben archivos asociados al registro
      $this->user->uploadFilesIfExists($id);

      // si hay grupos (extraidos anteriormente) se actualizan
      if (isset($grupos)) {
        $this->ion_auth->remove_from_group(NULL, $id);
        $this->ion_auth->add_to_group($grupos, $id);
      }

      // elabora la response, que no es mas que un mensaje de fb
      $this->response(array("message" => $this->lang->line('success_actualizar_' . $this->language_tag)), self::HTTP_OK, self::CODE_SHOW_SUCCESS_MESSAGE);
    } else {
      $this->response(array("error" => $this->lang->line('error_actualizar_' . $this->language_tag)), self::HTTP_OK, self::CODE_BAD);
    }
  }


// obtiene daatos formateados para poder tratarlos directamente en un dropdownVue o select
  public function dropdownUser_get($group_id)
  {
    $dropdown = $this->user->dropdownVueGrupoUsuario($this->dropdownLabel, $this->dropDownValue, " ", $group_id);

    if ($dropdown != null) {
      $this->response($dropdown, self::HTTP_OK, self::CODE_OK);
    } else {
      $this->response(array("error" => $this->lang->line('error_actualizar_' . $this->language_tag)), self::HTTP_OK, self::CODE_BAD);
    }
  }

  // devuelve el usuario con sus roles
  public function userWithRoles_get($id)
  {
    //hace la consulta
    $element = $this->user->with('users_groups')->cascade('group')->get($id);

    // elimina los pares en clave, correspondiente a datos no interesantes
    unset($element->password);
    unset($element->salt);
    unset($element->forgotten_password_code);
    unset($element->forgotten_password_time);
    unset($element->remember_code);

    // response
    $this->response($element, self::HTTP_OK, self::CODE_OK);
  }

  // actualiza valor tupla img en el registro asociado por id
  public function updateUserImage_post()
  {
    // saca datos
    $data = $this->post("data");
    // procesa para tratarlos
    if (is_string($data)) $data = json_decode($data, true);
    else $data = json_decode(json_encode($data), true);

    // saca el id
    $id = $data["id"];
    if ($id) {
      // logica update
      $updated_img = $this->{$this->model}->uploadFilesIfExists($id);
      if ($updated_img) {
        $this->response(['message' => $this->lang->line('success_actualizar_imagen_' . $this->language_tag)], self::HTTP_OK, self::CODE_SHOW_SUCCESS_MESSAGE);
      } else {
        $this->response(["error" => $this->lang->line('error_actualizar_imagen_' . $this->language_tag)], self::HTTP_OK, self::CODE_SHOW_ERROR_MESSAGE);
      }
    }
  }

  // Eliminar imagen de usuario por id usuario
  public function deleteUserImage_get($id_user)
  {
    // convierte array asociativo para tratar
    if (is_string($id_user)) $id_user = json_decode($id_user, true);
    else $id_user = json_decode(json_encode($id_user), true);

    // logica delete
    if ($id_user) {
      // $correcto = $this->{$this->model}->delete_file($id_user);
      $correcto = $this->{$this->model}->delete_file($id_user, $this->upload_fields);
    }

    // elaboro response
    if ($correcto == false) {
      $this->response(['message' => $this->lang->line('success_eliminar_imagen_' . $this->language_tag)], self::HTTP_OK, self::CODE_SHOW_SUCCESS_MESSAGE);
    } else {
      $this->response(["error" => $this->lang->line('error_eliminar_imagen_' . $this->language_tag)], self::HTTP_OK, self::CODE_SHOW_ERROR_MESSAGE);
    }
  }

  // obtiene un registro por id, como parametro
  public function data_get($element_id)
  {
    $element = $this->{$this->model}->with_everything()->cascade('grupo')->get($element_id);
    $this->response($element, self::HTTP_OK, self::CODE_OK);
  }

  // elimina un registro por id, sacado del request
  public function delete_post()
  {
    if ($this->model != null) {
      // saca el id k-v
      $id = $this->post("id");

      //---------
      // Ej. de como utilizar checkRestrict:
      // $result = $this->{$this->model}->checkRestrict($id, ['checkins']);
      // if ($result) {
      //   $this->response(array("message" => sprintf($this->lang->line('error_eliminar_restrict_'.$this->language_tag), implode(', ', $result))), self::HTTP_OK, self::CODE_SHOW_WARNING_MESSAGE);
      // }
      //---------

      // elimina
      $success = $this->{$this->model}->delete($id);

      // resultado consulta, elaboro response
      if ($success) {
        $this->response(array("message" => $this->lang->line('success_eliminar_' . $this->language_tag)), self::HTTP_OK, self::CODE_SHOW_SUCCESS_MESSAGE);
      } else {
        $this->response(array("message" => $this->lang->line('error_eliminar_' . $this->language_tag)), self::HTTP_OK, self::CODE_BAD);
      }
    }
  }
  public function phpVersion()
  {
      $this->response(['php_version' => PHP_VERSION], self::HTTP_OK, self::CODE_OK);
  }


}
