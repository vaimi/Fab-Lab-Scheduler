<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class User extends CI_Controller
{
	public function __construct() {
		parent::__construct();
		$this->load->model('User_model');
	}

	public function registration() 
	{

		$this->load->library("Aauth");
		if ($this->input->method() == 'post')
		{
			$post_data = array
			(
				'username' => $this->input->post('username'),
				'password' => $this->input->post('password'),
				'surname' => $this->input->post('surname'),
				'email' => $this->input->post('email'),
				'address_street' => $this->input->post('address_street'),
				'address_postal_code' => $this->input->post('address_postal_code'),
				'phone_number' => $this->input->post('phone_number'),
				'company' => $this->input->post('company'),
				'student_number' => $this->input->post('student_number')
			);
			if ($this->verify_registration_data($post_data))
			{
				$this->create_user($post_data);
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
				//Call db query from model
 				$row = $this->User_model->get_extended_user_data($this->session->userdata('id')); 
				// set extended user information into session 
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
		else //If get request.
		{
			$this->load->view('login_form', array('email' => '', 'data'=>array()));
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

	public function profile()
	{
		if (!$this->aauth->is_loggedin())
		{
			$this->load->view('page_not_found_404');
			return;
		}
		
		$this->load->view('partials/header');
		$this->load->view('partials/menu');
		$jdata['title'] = $this->session->userdata('surname') . "'s Profile";
		$jdata['message'] = "";
		$this->load->view('partials/jumbotron', $jdata);
		$this->load->view('user/profile');
		$this->load->view('partials/footer');
	}
	
	public function update_profile()
	{
		if (!$this->aauth->is_loggedin())
		{
			$this->load->view('page_not_found_404');
			return;
		}
		
		$new_user_info = array(
			'name'	=> $this->input->post('name'),
			'surname'	=> $this->input->post('surname'),
			'phone_number'	=> $this->input->post('phone_number'),
			'address_street'	=> $this->input->post('address_street'),
			'address_postal_code'	=> $this->input->post('address_postal_code'),
			'student_number'	=> $this->input->post('student_number')
		);
		
		$this->User_model->update_user($new_user_info);
		
		$this->session->set_userdata('name', $new_user_info['name']);
		$this->session->set_userdata('surname', $new_user_info['surname']);
		$this->session->set_userdata('phone_number', $new_user_info['phone_number']);
		$this->session->set_userdata('address_street', $new_user_info['address_street']);
		$this->session->set_userdata('address_postal_code', $new_user_info['address_postal_code']);
		$this->session->set_userdata('student_number', $new_user_info['student_number']);
		
		$this->load->view('user/profile_form');
	}
	
	public function get_user_profile()
	{
		if (!$this->aauth->is_loggedin())
		{
			$this->load->view('page_not_found_404');
			return;
		}
		$this->load->view('user/profile_form');
	}
	
	// Helpers 

	private function verify_registration_data($post_data)
	{
		$error = array();
		// , $surname, $address_street, $address_postal_number, $phone_number, $company, $student_number
		// validate inputed data
		if (empty($post_data['username']))
		{
			$error[] = $this->aauth->CI->lang->line('aauth_error_username_required');
		}
		if ($this->aauth->user_exist_by_name($post_data['username'])) {
			$error[] = $this->aauth->CI->lang->line('aauth_error_username_exists');
		}
		if ($this->aauth->user_exist_by_email($post_data['email'])) {
			$error[] = $this->aauth->CI->lang->line('aauth_error_email_exists');
		}
		$this->load->helper('email');
		if (!valid_email($post_data['email'])){
			$error[] = $this->aauth->CI->lang->line('aauth_error_email_invalid');
		}
		if ( strlen($post_data['password']) < $this->aauth->config_vars['min'] OR strlen($post_data['password']) > $this->aauth->config_vars['max'] ){
			$error[] = $this->aauth->CI->lang->line('aauth_error_password_invalid');
		}
		if ($post_data['username'] !='' && !ctype_alnum(str_replace($this->aauth->config_vars['valid_chars'], '', $post_data['username']))){
			$error[] = $this->aauth->CI->lang->line('aauth_error_username_invalid');
		}
		if ($post_data['surname'] == ''){
			$error[] = $this->aauth->CI->lang->line('aauth_error_surname_invalid');
		}
		
		if (count($error) > 0)
		{
			$post_data['success'] = false;
			$post_data['errors'] = $error;
			$this->load->view('registration_form', $post_data);
			return false;
		}
		return true;
	}

	private function authorize_priviledged_email($user_id, $email)
	{
		// delete user from default group
		//$this->aauth->remove_member($user_id, )
		//$sql = "delete from aauth_user_to_group where user_id=?";
		//$db->query($sql, array($user_id));
		// set default group
		$user_group = 2; //public group
		// get prefix of the user's email
		$email_prefix = explode( '@', $email )[1];
		// get prefixes of all groups
		$query = $this->User_model->get_email_prefixes();
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
		return true;
	}

	private function create_user($post_data) {
		$user_id = $this->aauth->create_user($post_data['email'], $post_data['password'], $post_data['username']);
		$db = $this->aauth->aauth_db;
		
		$extended_data = array
		(
			'id' => $user_id,
			'surname' => $post_data['surname'],
			'company' => $post_data['company'],
			'address_street' => $post_data['surname'],
			'address_postal_code' => $post_data['address_postal_code'],
			'phone_number' => $post_data['phone_number'],
			'student_number' => $post_data['student_number'],
			'quota' => 0 //TODO this should be gotten from general settings.
		);
		// insert extended user information
		$this->User_model->insert_extended_user_data($extended_data);
		// authorize priviledged email addresses
		$this->authorize_priviledged_email($user_id, $post_data['email']);
		$post_data['success'] = true;
		$this->load->view('registration_success', $post_data);
		return $user_id;
	}

}