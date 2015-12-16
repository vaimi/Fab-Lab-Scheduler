<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Controller extends CI_Controller
{
	public function __construct() {
		parent::__construct();
		$this->check_and_set_session_variable();
	}

	public function check_and_set_session_variable()
	{
		if (!isset($this->session->surname) or !isset($this->session->first_name))
		{
			if($this->aauth->is_loggedin())
			{
				$this->load->model('User_model');
				$session_variables = $this->User_model->get_session_data($this->session->id);
				$this->session->set_userdata('first_name', $session_variables->first_name);
				$this->session->set_userdata('surname', $session_variables->surname);
			}
		}
	}
}