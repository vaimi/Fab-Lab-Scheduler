<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class User extends MY_Controller
{
	public function __construct() {
		parent::__construct();	
		$this->load->model('User_model');
		$this->load->library("Aauth");
		$this->load->library('form_validation');
	}
	
	public function reset_password($user_id, $verification_code)
	{
		if ($this->aauth->reset_password($user_id, $verification_code))
		{
			$this->load->view('user/reset_password_success');
			$this->output->set_status_header('200');
			return;
		}
		else
		{
			$this->output->set_status_header('404');
			$this->load->view('user/reset_password_wrong_link');
			return;
		}
	}
	
	public function forget_password()
	{
		
		if ($this->input->method() == 'post')
		{
			//$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
			$email = $this->input->post('email');
			
			
			if ($this->aauth->remind_password($email) == false)
			{
				$this->load->view('user/user_not_found');
				return;
			}
			else 
			{
				$data = array('email' => $email);
				// display notification that email has been sent
				$this->load->view('user/forget_pass_email_sent', $data);
				return;
			}
		}
		else
		{
			$this->load->view('user/forget_password');
			return;
		}
	}

	public function registration() 
	{
		if ($this->input->method() == 'post')
		{
			//TODO: need to discuss about these
			$this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[5]|max_length[100]|is_unique[aauth_users.name]');
			$this->form_validation->set_rules('first_password', 'First password', 'required|matches[second_password]');
			$this->form_validation->set_rules('second_password', 'Password Confirmation', 'required');
			$this->form_validation->set_rules('first_name', 'First name', 'trim|required');
			$this->form_validation->set_rules('surname', 'Last name', 'trim|required');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[aauth_users.email]');
			$this->form_validation->set_rules('address_street', 'Street address', 'trim');
			$this->form_validation->set_rules('address_postal_code', 'Zip code', 'trim');
			$this->form_validation->set_rules('phone_number', 'Phone number', 'required|is_natural');
			$this->form_validation->set_rules('company', 'Company', 'trim');
			$this->form_validation->set_rules('student_number', 'Student number', 'trim');
			
			if ($this->form_validation->run() == FALSE)
			{
				echo validation_errors();
				return;
			}
			
			$post_data = array
			(
				//TODO validation
				'username' => $this->input->post('username'),
				'first_password' => $this->input->post('first_password'),
				'second_password' => $this->input->post('second_password'),
				'first_name' => $this->input->post('first_name'),
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
						'first_password' => '',
						'second_password' => '',
						'first_name' => '',
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
		if ($this->aauth->is_loggedin())
		{
			redirect(base_url(), 'refresh');
			return;
		}
		if ($this->input->method() == 'post')
		{
			//TODO validation
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
 				$this->session->set_userdata('first_name', $row->first_name);
				$this->session->set_userdata('surname', $row->surname);
				$this->session->set_userdata('company', $row->company);
				$this->session->set_userdata('address_street', $row->address_street);
				$this->session->set_userdata('address_postal_code', $row->address_postal_code);
				$this->session->set_userdata('phone_number', $row->phone_number);
				$this->session->set_userdata('student_number', $row->student_number);
				$this->session->set_userdata('quota', $row->quota);
				$pos_logout = strpos($current_url, 'user/logout');
				$pos_login = strpos($current_url, 'user/login');
				if ($pos_logout == false || $pos_login == false)
					redirect($current_url, 'refresh');
				else 
					redirect(base_url(), 'refresh');
				
			}
			else //login fail, go to login page with fail information
			{
				$this->output->set_status_header('200');
				$this->load->view('login_form', array('email' => $email, 'data'=>$this->aauth->errors));
			}
		}
		else //If get request.
		{
			$this->output->set_status_header('400');
			$this->load->view('login_form', array('email' => '', 'data'=>array()));
		}
	}
	
	public function logout()
	{
		if (!$this->aauth->is_loggedin())
		{
			redirect('home/index');
		}
		else
		{
			$this->aauth->logout();
			redirect(base_url().'user/logout', 'refresh');
		}
			
	}
	
	public function verification($user_id, $verification)
	{
		//TODO validation?
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
		$jdata['title'] = $this->session->userdata('first_name') . " " . $this->session->userdata('surname') . "'s Profile";
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
		// validation
		//$this->form_validation->set_rules('name', 'Username', 'required|trim|min_length[5]|max_length[12]');
		$this->form_validation->set_rules('first_password', 'First password', 'trim|matches[second_password]');
		$this->form_validation->set_rules('second_password', 'Password Confirmation', 'trim');
		$this->form_validation->set_rules('first_name', 'First name', 'trim|required');
		$this->form_validation->set_rules('surname', 'Last name', 'trim|required');
		$this->form_validation->set_rules('address_street', 'Street address', 'trim');
		$this->form_validation->set_rules('address_postal_code', 'Zip code', 'trim');
		$this->form_validation->set_rules('phone_number', 'Phone number', 'required|is_natural');
		$this->form_validation->set_rules('student_number', 'Student number', 'trim');
			
		if ($this->form_validation->run() == FALSE)
		{
			echo validation_errors();
			return;
		}
		$new_user_info = array(
			//FIXME: Username is not allowed to update?
			//'name'	=> $this->input->post('name'),
			'first_name' => $this->input->post('first_name'),
			'surname'	=> $this->input->post('surname'),
			'phone_number'	=> $this->input->post('phone_number'),
			'address_street'	=> $this->input->post('address_street'),
			'address_postal_code'	=> $this->input->post('address_postal_code'),
			'student_number'	=> $this->input->post('student_number')
		);
		if ($this->input->post('first_password') === $this->input->post('second_password') && $this->input->post('first_password') !== "")
		{
			$this->aauth->update_user($this->session->userdata['id'], false, $this->input->post('first_password'), false);
		}
		
		$this->User_model->update_user($new_user_info, $this->session->userdata['id']);
		
		//$this->session->set_userdata('name', $new_user_info['name']);
		$this->session->set_userdata('first_name', $new_user_info['first_name']);
		$this->session->set_userdata('surname', $new_user_info['surname']);
		$this->session->set_userdata('phone_number', $new_user_info['phone_number']);
		$this->session->set_userdata('address_street', $new_user_info['address_street']);
		$this->session->set_userdata('address_postal_code', $new_user_info['address_postal_code']);
		$this->session->set_userdata('student_number', $new_user_info['student_number']);
	
		$this->load->view('user/profile_form', $this->User_model->get_user_data($this->session->userdata('id')));
	}
	
	public function get_user_profile()
	{
		if (!$this->aauth->is_loggedin())
		{
			$this->load->view('page_not_found_404');
			return;
		}
		$this->load->view('user/profile_form', $this->User_model->get_user_data($this->session->userdata('id')));
	}
	
	public function get_machine_levels()
	{
		if (!$this->aauth->is_loggedin())
		{
			$this->load->view('page_not_found_404');
			return;
		}

		//Contains info about machines and levels.
		$data['results'] = $this->User_model->get_machine_levels_and_info($this->session->userdata('id'));
		$this->load->view('user/machine_levels_list', $data);
	}
	
	public function get_conversations()
	{
		$this->load->model('Message_model');
		$conversations = $this->Message_model->get_conversations($this->session->userdata('id'));
		$this->load->view('user/message_panel', array('conversations' => $conversations));
	}
	
	public function get_conversation($other_user_id)
	{
		if (!$this->aauth->is_loggedin())
		{
			$this->load->view('page_not_found_404');
			return;
		}
	
		$this->load->model('Message_model');
		$texts = $this->Message_model->get_conversation($this->session->userdata('id'), $other_user_id);
		$this->load->view('user/message_content', array('texts' => $texts));
	}
	
	public function get_reservations()
	{
		if (!$this->aauth->is_loggedin())
		{
			$this->load->view('page_not_found_404');
			return;
		}
		//Contains info about reservations
		$reservations = $this->User_model->get_reservations($this->session->userdata('id'));
		//Sort array by start time.
		usort($reservations, function($a, $b)
		{
			return $a['StartTime'] < $b['StartTime'];
		});
		$data['results'] = $reservations; 
		$this->load->view('user/reservations_list', $data);
	}
	
	function delete_reservation($reservation_id)
	{
		if (!$this->aauth->is_loggedin())
		{
			$this->load->view('page_not_found_404');
			return;
		}
		$this->load->model('Reservations_model');
		$reservation = $this->Reservations_model->get_reservation_by_id($reservation_id);
		if ($reservation == null)
		{
			set_status_header(404);
			echo json_encode(array('success' => false, 'message' => 'Reservation not found!'));
			return;
		}
		
		if (!$this->aauth->is_admin() && $this->session->userdata('id') != $reservation->aauth_usersID)
		{
			set_status_header(401);
			echo json_encode(array('success' => false, 'message' => 'You are not authorized to cancel the reservation!'));
			return;
		}
		if ($reservation->StartTime >= date())
		{
			set_status_header(400);
			echo json_encode(array('success' => false, 'message' => 'Cannot cancel past or ongoing reservation'));
			return;
		}
		
		$result = $this->Reservations_model->delete_reservation($reservation_id);
		if (!$result)
		{
			set_status_header(400);
			echo json_encode(array('success' => false, 'message' => 'Error when deleting the reservation, please contact admin for support!'));
			return;
		}
		else 
		{
			//send email
			
			set_status_header(200);
			echo json_encode(array('success' => true, 'message' => 'Reservation deleted!'));
			return;
		}
	}
	// Helpers 

	private function verify_registration_data($post_data)
	{
		$error = array();
		// , $surname, $address_street, $address_postal_number, $phone_number, $company, $student_number
		// validate inputed data
		// TODO use codeigniter form_validation
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
		if ( strlen($post_data['first_password']) < $this->aauth->config_vars['min'] OR strlen($post_data['first_password']) > $this->aauth->config_vars['max'] ){
			$error[] = $this->aauth->CI->lang->line('aauth_error_password_invalid');
		}
		if ($post_data['username'] !='' && !ctype_alnum(str_replace($this->aauth->config_vars['valid_chars'], '', $post_data['username']))){
			$error[] = $this->aauth->CI->lang->line('aauth_error_username_invalid');
		}
		if ($post_data['surname'] == ''){
			$error[] = $this->aauth->CI->lang->line('aauth_error_surname_invalid');
		}
		if ($post_data['first_password'] != $post_data['second_password']){
			$error[] = 'Password does not match';
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
		$user_id = $this->aauth->create_user($post_data['email'], $post_data['first_password'], $post_data['username']);
		$db = $this->aauth->aauth_db;
		
		$extended_data = array
		(
			'id' => $user_id,
			'first_name' => $post_data['first_name'],
			'surname' => $post_data['surname'],
			'company' => $post_data['company'],
			'address_street' => $post_data['address_street'],
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