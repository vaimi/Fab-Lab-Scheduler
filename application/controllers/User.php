<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class User extends CI_Controller
{
	public function __constructor() {
		parent::__constructor();
	}
	// this is the home page
	public function registration() 
	{

		$this->load->library("Aauth");
		if ($this->input->method() == 'post')
		{
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			$surname = $this->input->post('surname');
			$email = $this->input->post('email');
			$address_street = $this->input->post('address_street');
			$address_postal_number = $this->input->post('address_postal_code');
			$phone_number = $this->input->post('phone_number');
			$company = $this->input->post('company');
			$student_number = $this->input->post('student_number');
			$result = $this->aauth->create_user($email, $password, $username, $surname, $address_street, $address_postal_number, $phone_number, $company, $student_number);
			if ($result == False)
			{
				$error = array();
				if (empty($username))
				{
					$error[] = $this->aauth->CI->lang->line('aauth_error_username_required');
				}
				if ($this->aauth->user_exist_by_name($username)) {
					$error[] = $this->aauth->CI->lang->line('aauth_error_username_exists');
				}
				if ($this->aauth->user_exist_by_email($email)) {
					$error[] = $this->aauth->CI->lang->line('aauth_error_email_exists');
				}
				$this->load->helper('email');
				if (!valid_email($email)){
					$error[] = $this->aauth->CI->lang->line('aauth_error_email_invalid');
				}
				if ( strlen($password) < $this->aauth->config_vars['min'] OR strlen($password) > $this->aauth->config_vars['max'] ){
					$error[] = $this->aauth->CI->lang->line('aauth_error_password_invalid');
				}
				if ($username !='' && !ctype_alnum(str_replace($this->aauth->config_vars['valid_chars'], '', $username))){
					$error[] = $this->aauth->CI->lang->line('aauth_error_username_invalid');
				}
				if ($surname == ''){
					$error[] = $this->aauth->CI->lang->line('aauth_error_surname_invalid');
				}
				$data = array(
						'success' => false,
						'username' => $username,
						'password' => $password,
						'surname' => $surname,
						'email' => $email,
						'phone_number' => $phone_number,
						'company' => $company,
						'student_number' => $student_number,
						'address_street' => $address_street,
						'address_postal_code' => $address_postal_number,
						'errors' => $error
				);
				$this->load->view('registration_form', $data);
			}
			else 
			{
				$data = array(
						'success' => True,
						'username' => $username,
						'password' => $password,
						'surname' => $surname,
						'email' => $email,
						'phone_number' => $phone_number,
						'company' => $company,
						'student_number' => $student_number,
						'address_street' => $address_street,
						'address_postal_code' => $address_postal_number
				);
				$this->load->view('registration_success', $data);
			}
		}
		else // get action, when user use the link
		{
			$data = array(
						'success' => false,
						'username' => '',
						'password' => '',
						'surname' => '',
						'email' => '',
						'phone_number' => '',
						'company' => '',
						'student_number' => '',
						'address_street' => '',
						'address_postal_code' => '',
						'errors' => array()
				);
			$this->load->view('registration_form', $data);
		}
	}
	
	public function login()
	{
		$this->load->library("Aauth");
		if ($this->input->method() == 'post')
		{
			$password = $this->input->post('password');
			$email = $this->input->post('email');
			$remember = $this->input->post('remember')?true:false;
			$current_url = $this->input->post('current');
			$login_result = $this->aauth->login($email, $password, $remember);
			if ($login_result) //login success, refresh current page
			{
				redirect($current_url, 'refresh');
			}
			else //login fail, go to login page with fail information
			{
				$this->load->view('login_form', array('email' => $email, 'data'=>$this->aauth->errors));
			}
		}
		else
		{
			$this->load->view('login_form', array('email' => $email, 'data'=>array()));
		}
	}
}