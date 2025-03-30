<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller {

  public function __construct() {
    $this->dropdownLabel = array("first_name","last_name");
    $this->model = "user";
    $this->upload_fields = 'imagen';

    $this->restrictions = array(
      "getFilteredUsers" => array(
        "groups_allowed" => ["admin"]
      )
    );
    // $this->auth_enabled = false;
    parent::__construct();
  }

  public function getAllUsers_get($pagina) {
    $where = [
      'is_hidden' => false
    ];

    $elements = $this->user->with_everything()->cascade('grupo')->paginate($pagina, $where, 10);
    $this->response($elements,self::HTTP_OK,self::CODE_OK);
  }

  public function getFilteredUsers_post() {
    $buscador = $this->post('filter')['buscador'];
    $page = $this->post('page');
    $query = $this->user->with_everything()->cascade('grupo');
    $explodedBuscador = $buscador ? explode("%20", $buscador) : null;

    $where = [
      'is_hidden' => false
    ];

    if($buscador !== null) {
      // $query = $query->or_like_where(["username","first_name","last_name"], $explodedBuscador, $separation= "OR");
      $query = $query->or_like_where(["concat(first_name, ' ', last_name)", "phone", "email"], $explodedBuscador, $separation= "OR");
    }

    $elements = $query->paginate($page, $where, 10);

    $this->response($elements,self::HTTP_OK,self::CODE_OK);
  }

  public function create_post() {
    $data = $this->post("data") != null ? $this->post("data") : $this->post();
    if(is_string($data)) $data = json_decode($data,true);
    else $data = json_decode(json_encode($data),true);

    $user_extra_data = [
      'phone' => $data['phone'],
      'first_name' => $data['first_name'],
      'last_name' => $data['last_name']
    ];

    $grupos = array($data["roles"]);

    $id = $this->ion_auth->register(
      strtolower($data["email"]),
      $data["password"],
      strtolower($data["email"]),
      $user_extra_data,
      $grupos
    );

    if ($id) {
      $return = $this->user->uploadFilesIfExists($id);
      $this->response(array("message" => $this->lang->line('success_crear_'.$this->language_tag), "id" => $id),self::HTTP_OK,self::CODE_SHOW_SUCCESS_MESSAGE);
    } else {
      $this->response(array("error" =>$this->lang->line('error_crear_'.$this->language_tag)),self::HTTP_OK,self::CODE_BAD);
    }
  }

  public function update_post() {
    $data = $this->post("data") != null ? $this->post("data") : $this->post();
    if(is_string($data)) $data = json_decode($data,true);
    else $data = json_decode(json_encode($data),true);

    $id = $data["id"];

    if( isset($data["roles"])) {
      $grupos = $data["roles"];
      unset($data["roles"]);
    }
    unset($data["id"]);
    $updated = $this->ion_auth->update($id, $data);
    if ($updated) {
      $this->user->uploadFilesIfExists($id);

      if(isset($grupos)) {
        $this->ion_auth->remove_from_group(NULL,$id);
        $this->ion_auth->add_to_group($grupos,$id);
      }

      $this->response(array("message" => $this->lang->line('success_actualizar_'.$this->language_tag)),self::HTTP_OK,self::CODE_SHOW_SUCCESS_MESSAGE);
    } else {
      $this->response(array("error" => $this->lang->line('error_actualizar_'.$this->language_tag)),self::HTTP_OK,self::CODE_BAD);
    }
  }

  public function dropdownUser_get($group_id) {
    $dropdown = $this->user->dropdownVueGrupoUsuario($this->dropdownLabel,$this->dropDownValue," ",$group_id);

    if ($dropdown != null){
      $this->response($dropdown,self::HTTP_OK,self::CODE_OK);
    } else {
      $this->response(array("error" => $this->lang->line('error_actualizar_'.$this->language_tag)),self::HTTP_OK,self::CODE_BAD);
    }
  }

  public function userWithRoles_get($id) {
    $element = $this->user->with('users_groups')->cascade('group')->get($id);
    unset($element->password);
    unset($element->salt);
    unset($element->forgotten_password_code);
    unset($element->forgotten_password_time);
    unset($element->remember_code);

    $this->response($element,self::HTTP_OK,self::CODE_OK);
  }

  public function updateUserImage_post() {
    $data = $this->post("data");
    if(is_string($data)) $data = json_decode($data,true);
    else $data = json_decode(json_encode($data),true);

    $id = $data["id"];
    if ($id) {
      $updated_img = $this->{$this->model}->uploadFilesIfExists($id);
      if ($updated_img) {
        $this->response(['message' => $this->lang->line('success_actualizar_imagen_'.$this->language_tag)], self::HTTP_OK, self::CODE_SHOW_SUCCESS_MESSAGE);
      } else {
        $this->response(["error" => $this->lang->line('error_actualizar_imagen_'.$this->language_tag)], self::HTTP_OK, self::CODE_SHOW_ERROR_MESSAGE);
      }
    }
  }

  public function deleteUserImage_get($id_user) {
    if(is_string($id_user)) $id_user = json_decode($id_user, true);
    else $id_user = json_decode(json_encode($id_user),true);

    if ($id_user) {
      // $correcto = $this->{$this->model}->delete_file($id_user);
      $correcto = $this->{$this->model}->delete_file($id_user, $this->upload_fields);
    }

    if ($correcto == false) {
      $this->response(['message' => $this->lang->line('success_eliminar_imagen_'.$this->language_tag)], self::HTTP_OK, self::CODE_SHOW_SUCCESS_MESSAGE);
    } else {
      $this->response(["error" => $this->lang->line('error_eliminar_imagen_'.$this->language_tag)], self::HTTP_OK, self::CODE_SHOW_ERROR_MESSAGE);
    }
  }

	public function data_get($element_id) {
    $element = $this->{$this->model}->with_everything()->cascade('grupo')->get($element_id);
    $this->response($element,self::HTTP_OK,self::CODE_OK);
  }

  public function delete_post() {
    if($this->model != null) {
      $id = $this->post("id");

      //---------
      // Ej. de como utilizar checkRestrict:
      // $result = $this->{$this->model}->checkRestrict($id, ['checkins']);
      // if ($result) {
      //   $this->response(array("message" => sprintf($this->lang->line('error_eliminar_restrict_'.$this->language_tag), implode(', ', $result))), self::HTTP_OK, self::CODE_SHOW_WARNING_MESSAGE);
      // }
      //---------

      $success = $this->{$this->model}->delete($id);

      if ($success) {
        $this->response(array("message" => $this->lang->line('success_eliminar_'.$this->language_tag)),self::HTTP_OK,self::CODE_SHOW_SUCCESS_MESSAGE);
      } else {
        $this->response(array("message" => $this->lang->line('error_eliminar_'.$this->language_tag)),self::HTTP_OK,self::CODE_BAD);
      }
    }
  }
}
