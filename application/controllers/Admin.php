<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Admin extends CI_Controller
{
	public function __construct() {
		parent::__construct();
		if (!$this->aauth->is_admin()) 
		{
			redirect(base_url('user/login'), 'refresh');
		}
		$this->load->model('Admin_model');
	}

	public function moderate_general() 
	{
		$this->load->view('partials/header');
		$this->load->view('partials/menu');
		$jdata['title'] = "Admin";
		$jdata['message'] = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Sed posuere interdum sem. Quisque ligula eros ullamcorper quis, lacinia quis facilisis sed sapien. Mauris varius diam vitae arcu.";
		$this->load->view('partials/jumbotron', $jdata);
		$this->load->view('admin/general');
		$this->load->view('partials/footer');
	}
	
	public function moderate_machines() 
	{
		$this->load->view('partials/header');
		$this->load->view('partials/menu');
		$jdata['title'] = "Admin";
		$jdata['message'] = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Sed posuere interdum sem. Quisque ligula eros ullamcorper quis, lacinia quis facilisis sed sapien. Mauris varius diam vitae arcu.";
		$this->load->view('partials/jumbotron', $jdata);
		$this->load->view('admin/machines');
		$this->load->view('partials/footer');
	}
	
	public function moderate_timetables() 
	{
		$this->load->view('partials/header');
		$this->load->view('partials/menu');
		$jdata['title'] = "Admin";
		$jdata['message'] = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Sed posuere interdum sem. Quisque ligula eros ullamcorper quis, lacinia quis facilisis sed sapien. Mauris varius diam vitae arcu.";
		$this->load->view('partials/jumbotron', $jdata);
		$this->load->view('admin/timetable');
		$this->load->view('partials/footer');
	}
	
	public function moderate_users() 
	{
		$this->load->view('partials/header');
		$this->load->view('partials/menu');
		$jdata['title'] = "Admin";
		$jdata['message'] = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Sed posuere interdum sem. Quisque ligula eros ullamcorper quis, lacinia quis facilisis sed sapien. Mauris varius diam vitae arcu.";
		$this->load->view('partials/jumbotron', $jdata);
		$this->load->view('admin/users');
		$this->load->view('partials/footer');
	}
	
	public function create_machine_group()
	{
		if ($this->input->method() == 'post')
		{
			$name = $this->input->post('name');
			$description = $this->input->post('description');
			$need_supervision = $this->input->post('need_supervision')?$this->input->post('need_supervision'):'';
			
			$errors = [];
			if (trim($name) == '')
				$errors[] = 'Name can not be blank';
			
			if (count($errors) > 0)
			{
				$data = array(
					'name' => $name,
					'description' => $description,
					'need_supervision' => $need_supervision,
					'errors' => array()
				);
				$this->load->view('admin/create_machine_group', $data);
			}
			else 
			{
				$need_supervision = $need_supervision==''?0:1;
				$sql = "insert into MachineGroup(Name, Description, NeedSupervision) values (?, ?, ?)";
				$this->db->query($sql, array($name, $description, $need_supervision));
				redirect('admin/machines', 'refresh');
			}
		}
		else
		{
			$data = array(
				'name' => '',
				'description' => '',
				'need_supervision' => '',
				'errors' => array()
			);
			$this->load->view('admin/create_machine_group', $data);
		}
	}
	
	public function create_machine($machine_group='') 
	{
		if ($this->input->method() == 'post')
		{
			//Get post data
			if($this->input->post('needSupervision')) {
				$needSupervision = true;
			}
			else {
				$needSupervision = false;
			}
			$machinename = $this->input->post('machinename');
			$manufacturer = $this->input->post('manufacturer');
			$model = $this->input->post('model');
			$desc = $this->input->post('desc');
			
			$this->moderate_machines();
		}
		else
		{
			$this->load->view('admin/create_machine');
		}
	}

	/**
	 * Delete user
	 * Delete a user from db
	 * @access admin
	 * @uses input::post $user_id to be deleted
	 * @return bool Delete fails/succeeds
	 */
	public function delete_user() {
		$user_id = $this->input->post('user_id');
		return $this->aauth->delete_user($user_id);
	}

	/**
	 * Ban user
	 * Bans/deactivates user account
	 * @access admin
	 * @uses input::post int user_id to be banned
	 * @return bool Ban fails/succeeds
	 */
	public function ban_user() {
		$user_id = $this->input->post('user_id');
		return $this->aauth->ban_user($user_id);
	}

	/**
	 * Unban user
	 * Unbans/activates user account
	 * @access admin
	 * @uses input::post int user_id to be unlocked
	 * @return bool Unban fails/succeeds
	 */
	public function unban_user() {
		$user_id = $this->input->post('user_id');
		return $this->aauth->unban_user($user_id);
	}
	
	/**
	 * User search
	 * Search user by name, phone, email
	 * @access admin
	 * @uses input::post string search_data search term
	 * @return list of results as html
	 */
	 public function user_search() {
        $search_data = $this->input->post('search_data');
		$offset = $this->input->post('offset') ?: "0";
        $query = $this->Admin_model->get_autocomplete($search_data);
        foreach ($query->result() as $row):
            echo "<a class=\"list-group-item\" href=\"javascript:fetchUserData(" . $row->id . ");\">" . $row->name . " " . $row->surname . "</a>";
        endforeach;
	}
	
	/**
	 * Fetch user data
	 * Fetch user data by ajax call
	 * @access admin
	 * @uses input::post user_id user identification number
	 * @return result form as html
	 */
	 public function fetch_user_data() {
        $user_id = $this->input->post('user_id');
        $query = $this->Admin_model->get_user_data($user_id);
		$data = $query->result()[0];
		$this->load->view('admin/users_form', array('data' => $data));
	}
	
	/**
	 * Save user data
	 * Accepts form as post, validates field and if no errors, saves data to database
	 * @access admin
	 * @uses input::post array containing form fields + user id
	 * @return {"success":"true"} or {"success":"false", "errors":array of strings}
	 */
	public function save_user_data() {
		$form_data = array (
			'user_id' => $this->input->post('user_id'),
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
		$errors = $this->verify_data($form_data);
		if (count($errors) != 0) {
			$message = array(
				'success' => 0,
				'errors' => $errors
			);
			echo json_encode($message);
		} else {
			// Save data to database
			$this->aauth->update_user($form_data['user_id'], $form_data['email'], null, $form_data['username']);
			if ($this->Admin_model->update_user_data($form_data)) {
				$message = array(
					'success' => 1
				);
			} else {
				$message = array(
					'success' => 0,
					'errors' => array("Error while saving data to database")
				);
			}
			echo json_encode($message);
		}
	}
	
	/**
	 * Check whether post data is valid 
	 * Check whether send data is valid, if not, gathers errors to array.
	 * @access admin
	 * @input array post_data array containing all the data_fields
	 * @return array containing error messages
	 */
	//TODO this needs to be enchanced
	private function verify_data($post_data)
	{
		$error = array();
		// , $surname, $address_street, $address_postal_number, $phone_number, $company, $student_number
		// validate inputed data
		if (empty($post_data['username']))
		{
			array_push($error, $this->aauth->CI->lang->line('aauth_error_username_required'));
		}
		// TODO DISCUSS
		//if ($this->aauth->user_exist_by_name($post_data['username'])) {
		//	$error[] = $this->aauth->CI->lang->line('aauth_error_username_exists');
		//}
		//if ($this->aauth->user_exist_by_email($post_data['email'])) {
		//	array_push($error, $this->aauth->CI->lang->line('aauth_error_email_exists'));
		//}
		$this->load->helper('email');
		if (!valid_email($post_data['email'])){
			array_push($error, $this->aauth->CI->lang->line('aauth_error_email_invalid'));
		}
		if ($post_data['username'] !='' && !ctype_alnum(str_replace($this->aauth->config_vars['valid_chars'], '', $post_data['username']))){
			array_push($error, $this->aauth->CI->lang->line('aauth_error_username_invalid'));
		}
		if ($post_data['surname'] == ''){
			array_push($error, $this->aauth->CI->lang->line('aauth_error_surname_invalid'));
		}
		return $error;
	}
}