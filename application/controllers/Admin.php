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
	 * @param int $user_id to be deleted
	 * @return bool Delete fails/succeeds
	 */
	public function delete_user() {
		$user_id = $this->input->post('user_id');
		return $this->aauth->delete_user($user_id);
	}

	/**
	 * Ban user
	 * Bans/deactivates user account
	 * @param AJAX int user_id to be banned
	 * @return bool Ban fails/succeeds
	 */
	public function ban_user() {
		$user_id = $this->input->post('user_id');
		return $this->aauth->ban_user($user_id);
	}

	/**
	 * Unban user
	 * Unbans/activates user account
	 * @param AJAX int user_id to be unlocked
	 * @return bool Unban fails/succeeds
	 */
	public function unban_user() {
		$user_id = $this->input->post('user_id');
		return $this->aauth->unban_user($user_id);
	}
	
	/**
	 * User search
	 * Search user by name, phone, email
	 * @param AJAX string search_data search term
	 * @return array(userid, firstname, lastname)
	 */
	 public function user_search() {
        $search_data = $this->input->post('search_data');
		$offset = $this->input->post('offset') ?: "0";
        $query = $this->Admin_model->get_autocomplete($search_data);
        foreach ($query->result() as $row):
            echo "<a class=\"list-group-item\" href='" . base_url('admin/user_data') . "/" . $row->id . "'>" . $row->name . " " . $row->surname . "</a>";
        endforeach;
	}
}