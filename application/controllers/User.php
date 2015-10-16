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
			
			
			$error = array();
			// , $surname, $address_street, $address_postal_number, $phone_number, $company, $student_number
			// validate inputed data
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
			
			if (count($error) > 0)
			{
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
				$user_id = $this->aauth->create_user($email, $password, $username);
				$db = $this->aauth->aauth_db;
				
				// insert extended user information
				$sql = "insert into extended_users_information 
						(`id`, `surname`, `company`, `address_street`, `address_postal_code`, `phone_number`, `student_number`, `quota`)
						values (?, ?, ?, ?, ?, ?, ?, ?)";
				$query = $db->query($sql, array($user_id, $surname, $company, $address_street, $address_postal_number, $phone_number, $student_number, 0));
				// delete user from default group
				$sql = "delete from aauth_user_to_group where user_id=?";
				$db->query($sql, array($user_id));
				// set default group
				$user_group = 2; //public group
				// get prefix of the user's email
				$email_prefix = explode( '@', $email )[1];
				// get prefixes of all groups
				$sql = "SELECT * FROM aauth_groups";
				$query = $db->query($sql);
				// compare user's email with each prefixes
				foreach ($query->result() as $group)
				{
					if ($group->email_prefixes == '')
						continue;
					$prefixes_in_group = explode( '|', $group->email_prefixes );
					foreach ($prefixes_in_group as $prefix)
					{
						if (fnmatch($prefix,$email_prefix))
						{
							$user_group = $group->id;
						}
					}
				}
				// save user to group
				$this->aauth->add_member($user_id, $user_group);
				
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
				// set extended user information into session 
				$sql = "select * from extended_users_information where id=?";
				$query = $this->db->query($sql, array($this->session->userdata('id')));
				$row = $query->row();
				$this->session->set_userdata('surname', $row->surname);
				$this->session->set_userdata('company', $row->company);
				$this->session->set_userdata('address_street', $row->address_street);
				$this->session->set_userdata('address_postal_code', $row->address_postal_code);
				$this->session->set_userdata('phone_number', $row->phone_number);
				$this->session->set_userdata('student_number', $row->student_number);
				$this->session->set_userdata('quota', $row->quota);
				
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
	
	public function logout()
	{
		if (!$this->aauth->is_loggedin())
		{
			$this->load->view('page_not_found_404');
		}
		else
		{
			$this->aauth->logout();
			$this->load->view('user/logout');
		}
			
	}
	
	public function verification($user_id, $verification)
	{
		$verify_result = $this->aauth->verify_user($user_id, $verification);
		if (!$verify_result)
		{
			$this->load->view('page_not_found_404');
		}
		else 
		{
			$this->load->view('user/verification_success');
		}
	}
}