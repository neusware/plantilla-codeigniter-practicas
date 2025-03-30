<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class My_form_validation extends CI_Form_validation
{
	/**
	 * Is Unique
	 *
	 * Check if the input value doesn't already exist
	 * in the specified database field.
	 *
	 * @param	string	$str
	 * @param	string	$field
	 * @return	bool
	 */
	public function is_unique($str, $field)
	{
		sscanf($field, '%[^.].%[^.].%[^.]', $table, $field, $primary_key_field);
        $data = empty($this->validation_data)
		? $_POST
		: $this->validation_data;
		$primary_key = $data[$primary_key_field] ?? null;
		if(isset($this->CI->db)) {
			$count = 0;
			if($primary_key) {
				$count = $this->CI->db->limit(1)->get_where($table, array($primary_key_field." !=" => $primary_key, $field => $str))->num_rows();
			} else {
				$count = $this->CI->db->limit(1)->get_where($table, array($field => $str))->num_rows();
			}
			return $count == 0;
		} else {
			return FALSE;
		}
	}
}