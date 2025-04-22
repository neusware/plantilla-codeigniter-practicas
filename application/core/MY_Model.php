<?php

/**
 * Custom model based on "CodeIgniter Base Model":
 * https://github.com/jamierumbelow/codeigniter-base-model
 */

require APPPATH . "core/Base_Model.php";

class MY_Model extends Base_Model
{

	// Override variables from Base_Model
	public $before_get = array('callback_before_get');
	public $after_get = array('callback_after_get');

	// Variables from CI Bootstrap (see demo repo for examples)
	protected $where = array();
	protected $order_by = array();
	protected $singular_name = '';
	protected $plural_name = '';
	public $upload_fields = array();
	// public $custom_attached_name = $this->customAttachedName();

	protected $upload_file_config = array(
		'allowed_types'    => '*', //'jpg|png|jpeg',
		'overwrite'        => true,
		'file_ext_tolower' => true,
		'encrypt_name'     => true
	);


	/**
	 * Extra functions on top of Base_Model
	 */

	// Select specific fields only
	// Usage: $this->article_model->select('id, title')->get_all();
	// Reference: https://github.com/jamierumbelow/codeigniter-base-model/issues/217
	public function select($fields = '*', $escape = true)
	{
		if (is_array($fields))
			$fields = implode(',', $fields);

		$this->_database->select($fields, $escape);
		return $this;
	}

	// Get a field value from single result (by ID)
	public function get_field($id, $field)
	{
		$this->db->select($field);
		$record = $this->get($id);
		return (empty($record) || empty($record->$field)) ? NULL : $record->$field;
	}

	// update a field value
	public function update_field($id, $field, $value, $escape = TRUE)
	{
		// note: use CodeIgniter Query Builder instead of Base_model update() function, which does not allow escape set as FALSE
		$this->db->set($field, $value, $escape);
		$this->db->where($this->primary_key, $id);
		return $this->db->update($this->_table);
	}

	// increment a field value
	public function increment_field($id, $field, $diff = 1)
	{
		return $this->update_field($id, $field, $field . '+' . $diff, FALSE);
	}

	// decrement a field value
	public function decrement_field($id, $field, $diff = 1)
	{
		return $this->update_field($id, $field, $field . '-' . $diff, FALSE);
	}

	// Get multiple records with pagination
	public function paginate($page = 1, $where = array(), $limit = 10)
	{
		// get filtered results
		$where = array_merge($where, $this->where);
		$offset = ($page <= 1) ? 0 : ($page - 1) * $limit;
		$this->db->limit($limit, $offset);

		$results = parent::get_many_by($where);

		// get counts (e.g. for pagination)
		$count_results = count($results);
		$count_total = parent::count_by($where);
		$total_pages = ceil($count_total / $limit);
		$counts = array(
			'from_num'		=> ($count_results == 0) ? 0 : $offset + 1,
			'to_num'		=> ($count_results == 0) ? 0 : $offset + $count_results,
			'total_num'		=> $count_total,
			'curr_page'		=> intval($page),
			'total_pages'	=> ($count_results == 0) ? 1 : $total_pages,
			'limit'			=> $limit,
		);

		return array('data' => $results, 'counts' => $counts);
	}

	/**
	 * Callback functions
	 */
	protected function callback_before_get($result)
	{
		// default filter
		if (!empty($this->where))
			$this->db->where($this->where);

		// default order
		switch (count($this->order_by)) {
			case 1:
				$this->db->order_by($this->order_by[0]);
				break;
			case 2:
				$this->db->order_by($this->order_by[0], $this->order_by[1]);
				break;
			case 3:
				$this->db->order_by($this->order_by[0], $this->order_by[1], $this->order_by[2]);
				break;
		}
	}

	protected function callback_after_get($result)
	{
		// prepend folder path to upload assets
		// if ( !empty($this->upload_fields) )
		// {
		// 	foreach ($this->upload_fields as $key => $folder)
		// 	{
		// 		if ( !empty($result->$key) )
		// 		{
		// 			$result->$key = base_url($folder.'/'.$result->$key);
		// 		}
		// 	}
		// }

		return $result;
	}

	//Returns every has_may and belongs to from model less the $less array
	public function with_everything($less = array(), $belongs = true)
	{
		$less = is_array($less) ? $less : array();
		foreach ($this->has_many as $key => $value) {
			if (!in_array($key, $less))
				$this->with($key);
		}

		if ($belongs)
			foreach ($this->belongs_to as $key => $value) {
				if (!in_array($key, $less))
					$this->with($key);
			}

		return $this;
	}

	//adapted dropdown for vue element
	public function dropdownVue($dropdown_keys, $dropdown_value, $separator = " ", $get_many_by = array())
	{
		$dropdown_keys = is_array($dropdown_keys) ? $dropdown_keys : array($dropdown_keys);

		$elements = $this->order_by($dropdown_keys[0])->get_many_by($get_many_by);
		$dropdown = array();
		foreach ($elements as $element) {
			$element->label = $element->{$dropdown_keys[0]};
			for ($i = 1; $i < sizeof($dropdown_keys); $i++) {
				$element->label = $element->label . $separator . $element->{$dropdown_keys[$i]};
			}
			$element->value = $element->{$dropdown_value};
			$dropdown[] = $element;
			// $dropdown[] = array('label' => $key, 'value' => intval($value));
		}

		return $dropdown;
	}

	public function or_like_where($keys, $where_array, $separation = "AND", $needsSetWhere = false)
	{
		if (!is_array($keys)) $keys = array($keys);
		if (!is_array($where_array)) $where_array = array($where_array);

		$where = array();
		if (sizeof($where_array) > 0) {
			$or_like = "((";
			//Primer elemento
			$or_like .= " $keys[0] LIKE \"%$where_array[0]%\"";
			for ($j = 1; $j < sizeof($keys); $j++) {
				$or_like .= " OR $keys[$j] LIKE \"%$where_array[0]%\"";
			}
			$or_like .= ")";

			//Resto de elementos
			for ($i = 1; $i < sizeof($where_array); $i++) {
				$or_like .= " " . $separation . " ( $keys[0] LIKE \"%$where_array[$i]%\"";
				for ($j = 1; $j < sizeof($keys); $j++) {
					$or_like .= " OR $keys[$j] LIKE \"%$where_array[$i]%\"";
				}
				$or_like .= ")";
			}

			$or_like .= ")";
			$where[$or_like] = null;
		}
		$this->where = array_merge($where, $this->where);
		if ($needsSetWhere) $this->_set_where($this->where);
		return $this;
	}

	// $id => es el id del dato que se va a borrar     $restrictArray => es un array compuesto de los nombres del has_many que quieres controlar
	// Ej: $restrictArray = ['users', 'groups'];
	// devuelve un array de strings con la cantidad de cada model que se inserte en "$restrictArray", y si no hay ninguna coincidencia devuelve "false"
	// Ej de lo que devuelve:  array('un usuario', '2 grupos')
	// para poner el nombre (si contiene datos) hay que poner en los model (para cada uno el suyo) :   $this->singular_name = 'usuario';   $this->plural_name = 'usuarios';

	// viene a checkear si un registro esta relacionado con algunos de los modelos que se le pasan por parámetro, devuelve un mensaje tipo ["un usuario", "2 grupos"] o false, usando variables en función del sizeof para construir el mensaje de response. Esas variables se suelen definir en el modelo
	public function checkRestrict($id, $restrictArray)
	{
		$result = array();
		foreach ($restrictArray as $key => $value) {
			$this->load->model($this->has_many[$value]['model']);
			$v = $this->{$this->has_many[$value]['model']}->get_many_by([$this->has_many[$value]['primary_key'] => $id]);
			if (sizeof($v) > 0) {
				if (sizeof($v) == 1) {
					array_push($result, 'un ' . $this->{$this->has_many[$value]['model']}->singular_name);
				} else {
					array_push($result, sizeof($v) . ' ' . $this->{$this->has_many[$value]['model']}->plural_name);
				}
			}
		}

		if (sizeof($result) > 0) {
			return $result;
		} else {
			return false;
		}
	}

	// Get a field value from single result (by ID)
	public function get_id_array($where, $id = "id")
	{
		$this->db->select($id);
		$result = $this->get_many_by($where);
		$return = array();
		foreach ($result as $value) {
			$return[] = $value->{$id};
		}

		return $return;
	}


	public function upload_file($upload_field, $file_name, $options = array())
	{
		$destiny_folder = $this->get_folder($upload_field);

		if ($file_name !== null) {
			$this->upload_file_config['file_name'] = $file_name;
			$this->upload_file_config['upload_path'] = $destiny_folder;
			$config_temp = array_replace($this->upload_file_config, $options);
			$this->load->library('upload', $config_temp);
			$this->upload->set_upload_path($destiny_folder);

			if ($this->upload->do_upload($upload_field)) {
				return $this->upload->data('file_name');
			} else {
				return null;
			}
		} else {
			return null;
		}
	}

	public function get_folder($upload_field)
	{
		if (!file_exists(APPPATH . "uploads/" . $this->_table)) {
			mkdir(APPPATH . "uploads/" . $this->_table, 0777);
		}
		if (!file_exists(APPPATH . "uploads/" . $this->_table . "/" . $this->upload_fields[$upload_field] . "/")) {
			mkdir(APPPATH . "uploads/" . $this->_table . "/" . $this->upload_fields[$upload_field] . "/", 0777);
		}

		return APPPATH . "uploads/" . $this->_table . "/" . $this->upload_fields[$upload_field] . "/";
	}

	public function delete_file($id, $field)
	{
		$element = $this->get($id);
		if ($this->delete_file_from_element($element, $field))
			$this->update($id, array($field => NULL));
	}

	public function delete_file_from_element($element, $field)
	{
		if ($element != null) {
			if (property_exists($element, $field)) {
				if ($element->{$field} != null) {
					if (file_exists($this->get_folder($field) . $element->{$field})) {
						unlink($this->get_folder($field) . $element->{$field});
					}
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	// Search in upload_fields and upload files that exist in $_FILES
	public function uploadFilesIfExists($element_id, $options = array())
	{
		$correcto = true;
		foreach ($this->upload_fields as $key => $value) {
			if ($key) {
				foreach (array_reverse($_FILES) as $keyFiles => $value) {
					if ($key == $keyFiles) {
						if (!$this->uploadFile($element_id, $key, $options))
							$correcto = false;
					}
				}
			}
		}

		return $correcto;
	}

	public function uploadFile($elementId, $field, $options = array())
	{
		$element = $this->get($elementId);

		if ($elementId != null) {
			//try to delete file
			$element = $this->get($elementId);

			if (property_exists($element, $field)) {
				$oldFile = $element->{$field} ?? NULL;
				// $this->delete_file($elementId,$field);
				if ($oldFile != NULL && file_exists($this->get_folder($field) . $oldFile)) {
					unlink($this->get_folder($field) . $oldFile);
				}
				$imageUploaded = $this->upload_file($field, $elementId, $options);
				if ($imageUploaded != null) {
					$updated = $this->update($elementId, array($field => $imageUploaded));

					return $updated;
				} else {
					return false;
				}

				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function customAttachedName($extension = 'pdf', $nombre_archivo = null)
	{
		return ($nombre_archivo != null) ? $nombre_archivo . '.' . $extension : 'archivo.' . $extension;
	}

	public function delete($id)
	{
		$this->trigger('before_delete', $id);
		$element = $this->get($id);

		$this->_database->where($this->primary_key, $id);

		if ($this->soft_delete) {
			$result = $this->_database->update($this->_table, array($this->soft_delete_key => TRUE));
		} else {
			$result = $this->_database->delete($this->_table);

			if ($result) {
				foreach ($this->upload_fields as $nombre_field_adjunto => $value) {
					$this->delete_file_from_element($element, $nombre_field_adjunto);
				}
			}
		}

		$this->trigger('after_delete', $result);

		return $result;
	}

	public function join_with_filters($table, $on, $type = null, $original_table = null, $custom_select = '', $custom_select_from_original_table = null)
	{
		$select_from_original_table = "$original_table.*";
		if ($custom_select_from_original_table != null) {
			$select_from_original_table = $custom_select_from_original_table;
		}
		$this->_database->select("$select_from_original_table, $custom_select");
		$this->_database->join($table, $on, $type);

		return $this;
	}

	public function group_by($group_by_parameter)
	{
		$this->_database->group_by($this->_table . ".$group_by_parameter");

		return $this;
	}
}
