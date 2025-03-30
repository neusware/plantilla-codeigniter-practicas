<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Auth
 * @property Ion_auth|Ion_auth_model $ion_auth        The ION Auth spark
 * @property CI_Form_validation      $form_validation The form validation library
 */
class External extends MY_Controller {

	public function __construct() {
		$this->auth_enabled = FALSE; // Not necesary authentication in this controller
		parent::__construct();
		$this->load->database();
		$this->load->library(array('ion_auth', 'form_validation'));
		$this->load->helper(array('url', 'language'));

		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

		$this->lang->load('auth');
	}

	/**
	 * Log the user in
	 */
	public function login_post() {
		// validate form input
		$this->form_validation->set_data($this->post());
		$this->form_validation->set_rules('identity', str_replace(':', '', $this->lang->line('login_identity_label')), 'required');
		$this->form_validation->set_rules('password', str_replace(':', '', $this->lang->line('login_password_label')), 'required');

		load_class('Security', 'core');

		if ($this->form_validation->run() === TRUE) {
			// check to see if the user is logging in
			// check for "remember me"
			$remember = (bool)$this->post('remember');

			if ($this->ion_auth->login($this->post('identity'), $this->post('password'), $remember)) {

				$token = $this->token->create_token($this->ion_auth->get_user_id());
				//if the login is successful
				$this->response(array("token" => $token),self::HTTP_OK,self::CODE_OK);
			} else {
				// if the login was un-successful
				$this->response(array("error" => $this->ion_auth->errors()),self::HTTP_OK,self::CODE_BAD);
			}
		} else {
			// the user is not logging in so display the login page
			// set the flash data error message if there is one
			$message = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$this->response($this->lang->line('error_login'), self::HTTP_OK, self::CODE_SHOW_ERROR_MESSAGE);
		}
	}

	public function forgotPassword_post() {
		$identity = $this->input->post("identity");
		if($identity) {
			if($this->ion_auth->forgotten_password($identity)) {
				$this->response(['message' => "Email enviado correctamente"], self::HTTP_OK, self::CODE_SHOW_SUCCESS_MESSAGE);
			} else {
				$this->response(['message' => $this->ion_auth->errors()], self::HTTP_OK, self::CODE_SHOW_ERROR_MESSAGE);
			}
		} else {
			$this->response(['message' => 'No se ha proporcionado un email válido'], self::HTTP_OK, self::CODE_SHOW_ERROR_MESSAGE);
		}
	}

	public function changePassword_post() {
		$data = $this->post("data") ?? $this->post();
		if(is_string($data)) $data = json_decode($data);
		else $data = json_decode(json_encode($data));

		if($data->password == $data->repeatPassword) {
			if($this->ion_auth_model->forgotten_password_complete(base64_decode($data->forgotten_password_code), $data->password)) {
				$this->response(array('message' => "Contraseña cambiada correctamente"),self::HTTP_OK,self::CODE_SHOW_SUCCESS_MESSAGE);
			} else {
				$this->response("No ha sido posible cambiar la contraseña",self::HTTP_OK,400);
			}
		} else {
			$this->response("No ha sido posible cambiar la contraseña",self::HTTP_OK,400);
		}
	}
}
