<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Admin extends CI_Controller
{
	public function __construct() {
		parent::__construct();
		$this->load->model('Admin_model');
		if (!$this->aauth->is_admin())
		{
			redirect('404');
		}
	}

	//
	// Sites
	//
	
	public function upload_image()
	{
		if (!$this->aauth->is_admin())
		{
			redirect('404');
		}
		if ($this->input->method() == 'post')
		{
			$uploaddir = 'F:/xampp/htdocs/Fab-Lab-Scheduler/assets/images/admin_uploads/';
			$file_extension = pathinfo($_FILES['fileToUpload']['name'], PATHINFO_EXTENSION);
			$file_name = random_string('alnum', 50).'.'.$file_extension;
			$uploadfile = $uploaddir . basename($file_name);
			if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $uploadfile))
			{
				$data = array('upload_file' => $file_name, 'errors' => array());
				$this->load->view('admin/upload_image', $data);
			} else 
			{
				$data = array('upload_file' => '', 'errors' => array());
				switch($_FILES['fileToUpload']['error'])
				{
					case UPLOAD_ERR_INI_SIZE:
						$data['errors'][] = 'File too big, please choose a smaller file';
						break;
					case UPLOAD_ERR_NO_FILE:
						$data['errors'][] = 'No file found, please upload again';
						break;
					default:
						$data['errors'][] = 'Error uploading file, please try again';
						break;
				}
				$this->load->view('admin/upload_image', $data);
			}
		}
		else 
		{
			$data = array('upload_file' => '', 'errors' => array());
			$this->load->view('admin/upload_image', $data);
		}
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
        $this->session->set_userdata('sv_fetch_time', time());
        $this->session->set_userdata('sv_unsaved_modified_items', array());
        $this->session->set_userdata('sv_unsaved_new_items', array());
        $this->session->set_userdata('sv_unsaved_deleted_items', array());
        $this->session->set_userdata('sv_saved_items', array());
		$this->load->view('partials/header');
		$this->load->view('partials/menu');
		$jdata['title'] = "Timetables";
		$jdata['message'] = "You can manage supervisor times and add supervisors to the timetables";
		$this->load->view('partials/jumbotron', $jdata);
		//Get admins (Supervisors) from db
		$data['admins'] = $this->Admin_model->get_admins()->result();
		$this->load->view('admin/timetable', $data);
		$this->load->view('partials/footer');
	}
	
	/**
	 * Manage users
	 * Manage users, modify their levels and groups 
	 *
	 * In users-view, also sub-view users_data is used to load the input forms
	 *
	 * Unit tested
	 *
	 * @access admin
	 * @see users_form.php
	 * @return echo html
	 */
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
				redirect('admin/moderate_machines', 'refresh');
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
	/**
	 * This is called when creating a new machine in moderate_machines.
	 */
	public function create_machine() 
	{
		if ($this->input->method() == 'post')
		{
			//Get post data
			if($this->input->post('needSupervisor')) {
				$needSupervision = true;
			}
			else {
				$needSupervision = false;
			}
			//Should load all of the time?
			$this->load->library('form_validation');
			
			$machinename = $this->input->post('machinename');
			$machine_group_id = $this->input->post('machineGroup');
			$manufacturer = $this->input->post('manufacturer');
			$model = $this->input->post('model');
			$desc = $this->input->post('desc');
			$this->form_validation->set_rules('machinename', 'Machine Name', 'required');
			// TODO Should also match in the db
			$this->form_validation->set_rules('machineGroup', 'Machine Group', 'required|is_natural');
			$this->form_validation->set_rules('manufacturer', 'Manufacturer', 'required');
			$this->form_validation->set_rules('model', 'Model', 'required');
			$this->form_validation->set_rules('desc', 'Description', 'required');
			if ($this->form_validation->run() == FALSE)
			{
				//invalid data
				echo validation_errors();
				die();
			}
			//insert machine into db.
			$this->Admin_model->create_new_machine( array(
				"MachineGroupID" => $machine_group_id,
				"MachineName" => $machinename,
				"Manufacturer" => $manufacturer,
				"Model" => $model,
				"NeedSupervision" => $needSupervision,
				"Description" => $desc
			));
		}
		redirect('admin/moderate_machines','refresh');
	}

	//
	// AJAX functions
	//
	
	// Timetable
    public function timetable_fetch_supervision_sessions() {
        // get calendar request
        $start_time = $this->input->get('start');
        $end_time = $this->input->get('end');
        // get supervision slots
        $slots = $this->Admin_model->timetable_get_supervision_slots($start_time, $end_time);
        $response = array();
        
        $modIDs = array_map(function($o) { return $o->id; }, $this->session->userdata('sv_unsaved_modified_items'));
        $modIDsSaved = array_map(function($o) { return $o->id; }, $this->session->userdata('sv_saved_items'));
        foreach($slots->result() as $slot)
		{
            if (!in_array($slot->SupervisionID, $modIDs))
            {
                $slot_array = array (
                    'id' => $slot->SupervisionID,
                    'title' => "uid: ". $slot->aauth_usersID. " sid: ". $slot->SupervisionID,
                    'assigned' => $slot->aauth_usersID,
                    'start' => $slot->StartTime,
                    'end' => $slot->EndTime
                );
                array_push($response, $slot_array);
                if (!in_array($slot->SupervisionID, $modIDsSaved))
                {
                //create saved slot
	                $current_saved_slots = $this->session->userdata('sv_saved_items');
	                $s = new stdClass();
	                $s->assigned = $slot->aauth_usersID;
	                $s->start = $slot->StartTime;
	                $s->end = $slot->EndTime;
	                $s->id = $slot->SupervisionID;
	                $current_saved_slots[$s->id] = $s;
	                $this->session->set_userdata('sv_saved_items', $current_saved_slots);
                }
            }
            
        }
        echo json_encode($response);
    }
    
    public function timetable_fetch_mod_and_new_sessions() {
        $start_time = $this->input->get('start');
        $end_time = $this->input->get('end');
        // get supervision slots
        $response = array_merge($this->session->userdata('sv_unsaved_new_items'), $this->session->userdata('sv_unsaved_modified_items'));
        echo json_encode($response);
    }
    
	/**
	 * Save events from session file to database
	 * @access admin
	 */
	public function timetable_save() {
        $new_slots = $this->session->userdata('sv_unsaved_new_items');
        $modified_slots = $this->session->userdata('sv_unsaved_modified_items');
        $deleted_slots = $this->session->userdata('sv_unsaved_deleted_items');
        
        $errors = array();
        
        foreach($new_slots as $slot)
        {
            $this->Admin_model->timetable_save_new($slot);
        }
        foreach($modified_slots as $slot)
        {
            $this->Admin_model->timetable_save_modified($slot);
        }
        foreach($deleted_slots as $slot)
        {
            if ($slot->id > 0)
            {
                $this->Admin_model->timetable_save_deleted($slot);
            }
        }
        $this->session->set_userdata('sv_unsaved_new_items', array());
        $this->session->set_userdata('sv_unsaved_modified_items', array());
        $this->session->set_userdata('sv_unsaved_deleted_items', array());
        echo json_encode(array("success" => 1, "errors" => $errors));
    }
    
    public function timetable_new_slot() {
        $assigned = $this->input->post("assigned");
        $start = $this->input->post("start");
        $end = $this->input->post("end");    
        
        $slot = new stdClass();
        $slot->assigned = $assigned;
        $slot->start = $start;
        $slot->end = $end;
        $slot->id = -1 - count($this->session->userdata('sv_unsaved_new_items'));
        $unsaved = $this->session->userdata('sv_unsaved_new_items');
        $unsaved[$slot->id] = $slot;
        $this->session->set_userdata('sv_unsaved_new_items', $unsaved);
        echo json_encode(array("id" => $slot->id));
    }
    
    public function timetable_remove_slot() {
        $id = $this->input->post("id"); 
        $assigned = $this->input->post("assigned");
        $start = $this->input->post("start");
        $end = $this->input->post("end");  
        
        if ($id < 0)
        {
            $slots = $this->session->userdata('sv_unsaved_new_items');
            foreach($slots as $key_id => $value)
            {
                if ($key_id == $id)
                {
                    $deleted = $this->session->userdata('sv_unsaved_deleted_items');
                    $deleted[$id] = $slots[$key_id];
                    $this->session->set_userdata('sv_unsaved_deleted_items', $deleted);
                    unset($slots[$key_id]);
                    $this->session->set_userdata('sv_unsaved_new_items', $slots);
                    break;
                }
            }
        } else {
            $found = false;
            $slots = $this->session->userdata('sv_unsaved_modified_items');
            foreach($slots as $key_id => $value)
            {
                if ($key_id == $id)
                {
                    $deleted = $this->session->userdata('sv_unsaved_deleted_items');
                    $deleted[$id] = $slots[$key_id];
                    $this->session->set_userdata('sv_unsaved_deleted_items', $deleted);
                    unset($slots[$key_id]);
                    $this->session->set_userdata('sv_unsaved_modified_items', $slots);
                    $found = true;
                    break;
                }
            }
            if (!$found)
            {
                $db_slot = $this->Admin_model->timetable_fetch_by_id($id);
                
                $slot = $db_slot->result()[0];
                $new_slot = new stdClass();
                $new_slot->id = $slot->SupervisionID;
                $new_slot->assigned = $slot->aauth_usersID;
                $new_slot->start = $slot->StartTime;
                $new_slot->end = $slot->EndTime;
                            
                $deleted = $this->session->userdata('sv_unsaved_deleted_items');
                $deleted[$id] = $new_slot;
                $this->session->set_userdata('sv_unsaved_deleted_items', $deleted);
            }
        }
        echo json_encode(array("success" => 1 ,$this->session->userdata('sv_unsaved_deleted_items')));
    }
                    
    public function timetable_restore_slot() {
        $id = $this->input->post("id"); 
        $assigned = $this->input->post("assigned");
        $start = $this->input->post("start");
        $end = $this->input->post("end");
        
        $slots = $this->session->userdata('sv_unsaved_deleted_items');
        foreach($slots as $key_id => $value)
        {
            if ($key_id == $id)
            {
                if ($id < 0)
                {
                    $new = $this->session->userdata('sv_unsaved_new_items');
                    $new[] = $slots[$key_id];
                    $this->session->set_userdata('sv_unsaved_new_items', $new);
                } 
                else 
                {
                    //TODO maybe check that it's really modified
                    $modified = $this->session->userdata('sv_unsaved_modified_items');
                    $modified[] = $slots[$key_id];
                    $this->session->set_userdata('sv_unsaved_modified_items', $modified);
                }
                unset($slots[$key_id]);
                $this->session->set_userdata('sv_unsaved_deleted_items', $slots);
                break;
            }
        }
    }
    
    public function timetable_modify_slot() {
        $id = $this->input->post("id"); 
        $assigned = $this->input->post("assigned");
        $start = $this->input->post("start");
        $end = $this->input->post("end");    
        
        if($id > 0)
        {
            $slot = new stdClass();
            $slot->assigned = $assigned;
            $slot->start = $start;
            $slot->end = $end;
            $slot->id = $id;
            $slots = $this->session->userdata('sv_unsaved_modified_items');
            $slots[$id] = $slot;
            $this->session->set_userdata('sv_unsaved_modified_items', $slots);
        } 
        else
        {
            $slot = new stdClass();
            $slot->assigned = $assigned;
            $slot->start = $start;
            $slot->end = $end;
            $slot->id = $id;
            $slots = $this->session->userdata('sv_unsaved_new_items');
            $slots[$id] = $slot;
            $this->session->set_userdata('sv_unsaved_new_items', $slots);
        }
        $modIDs = array_map(function($o) { return $o->id; }, $this->session->userdata('sv_saved_items'));
        //Delete if in saved array.
        if (in_array($id, $modIDs))
        {
        	$tmp = $this->session->userdata('sv_saved_items');
        	unset($tmp[$id]);
        	$this->session->set_userdata('sv_saved_items', $tmp);
        }
        echo json_encode(array("success" => 1));
    }
	//Schedules 
	
    /**
     * Copy schedules from database with offset.
     *
     * @access admin
     */
    public function schedule_copy() {
    	if ($this->input->server('REQUEST_METHOD') == 'POST') {
    		//If slots are modified or deleted.
    		if (count($this->session->userdata('sv_unsaved_modified_items')) > 0 
    		|| count($this->session->userdata('sv_unsaved_new_items')) > 0
    		|| count($this->session->userdata('sv_unsaved_deleted_items')) > 0)
    		{
    			echo json_encode(array("Error" => "Save timetable first."));
    			return;
    		}
    		
    		$startDate = $this->input->post("startDate");
    		$endDate = $this->input->post("endDate");
    		$copyStartDate = $this->input->post("copyStartDate");
    		$info = $this->Admin_model->schedule_copy($startDate, $endDate, $copyStartDate);
    		echo json_encode(array("affected rows" => count($info), "info" => $info ));
    	}
    	else {
    		// TODO: redirect or block bad request
    		redirect('400'); //Bad Request
    	}
    }
    /**
     * Delete schedules which is in between start and end time.
     *
     * @access admin
     */
    public function schedule_delete() {
    	if ($this->input->server('REQUEST_METHOD') == 'POST') {
    		$startDate = $this->input->post("startDate");
    		$endDate = $this->input->post("endDate");
    		$startDate = new DateTime($startDate);
    		$endDate = new DateTime($endDate);
    		//Set time to 23:59:59
    		$endDate->setTime(23,59,59);
    		
    		$slots_current_deleted = $this->session->userdata('sv_unsaved_deleted_items');
    		//saved
	    	$slots = $this->session->userdata('sv_saved_items');
	    	$tmp = $this->session->userdata('sv_saved_items');
    		foreach($slots as $slot)
    		{
    			$sDate = new DateTime($slot->start);
    			$eDate = new DateTime($slot->end);
    			if ($sDate > $startDate && $eDate < $endDate) 
    			{
    				unset($tmp[$slot->id]);
    				$slots_current_deleted[$slot->id] = $slot;
    			}
    		}
    		$this->session->set_userdata('sv_saved_items', $tmp);
    		//new
    		$slots = $this->session->userdata('sv_unsaved_new_items');
    		$tmp = $this->session->userdata('sv_unsaved_new_items');
    		foreach($slots as $slot)
    		{
    			$sDate = new DateTime($slot->start);
    			$eDate = new DateTime($slot->end);
    			if ($sDate > $startDate && $eDate < $endDate)
    			{
    				unset($tmp[$slot->id]);
    				$slots_current_deleted[$slot->id] = $slot;
    			}
    		}
    		$this->session->set_userdata('sv_unsaved_new_items', $tmp);
    		//modified
    		$slots = $this->session->userdata('sv_unsaved_modified_items');
    		$tmp = $this->session->userdata('sv_unsaved_modified_items');
    		foreach($slots as $slot)
    		{
    			$sDate = new DateTime($slot->start);
    			$eDate = new DateTime($slot->end);
    			if ($sDate > $startDate && $eDate < $endDate)
    			{
    				unset($tmp[$slot->id]);
    				$slots_current_deleted[$slot->id] = $slot;
    			}
    		}
    		$this->session->set_userdata('sv_unsaved_modified_items', $tmp);
    		
    		$this->session->set_userdata('sv_unsaved_deleted_items', $slots_current_deleted);
     		echo json_encode(array(
     		"deleted_ids" => array_map(function($o) { return $o->id; }, $this->session->userdata('sv_unsaved_deleted_items') ),
     		"modified_ids" => array_map(function($o) { return $o->id; }, $this->session->userdata('sv_unsaved_modified_items') ),
     		"new_ids" => array_map(function($o) { return $o->id; }, $this->session->userdata('sv_unsaved_new_items') ),
     		"saved_ids" => array_map(function($o) { return $o->id; }, $this->session->userdata('sv_saved_items') )
     		));
    	}
    	else {
    		// TODO: redirect or block bad request
    		redirect('400'); //Bad Request
    	}
    }
	// Users management

	/**
	 * Delete user
	 *
	 * Delete a user from db.
	 *
	 * Unit tested
	 *
	 * @access admin
	 * @uses Codeigniter-aauth to delete user from database and validate user_id
	 * @uses input::post $user_id to be deleted
	 * @return bool Delete fails/succeeds
	 */
	public function delete_user() {
		$user_id = $this->input->post('user_id');
		
		$response = "false";
		if ($this->aauth->get_user($user_id) != false)
		{
			$response = $this->aauth->delete_user($user_id);
		}
		echo json_encode($response);
	}

	/**
	 * Ban user
	 *
	 * Bans/deactivates user account
	 *
	 * Unit tested
	 *
	 * @access admin
	 * @uses Codeigniter-aauth to ban user and validate user_id
	 * @uses input::post int user_id to be banned
	 * @return bool Ban fails/succeeds
	 */
	public function ban_user() {
		$user_id = $this->input->post('user_id');
		
		$response = "false";
		if ($this->aauth->get_user($user_id) != false)
		{
			$response = $this->aauth->ban_user($user_id);
		}
		echo json_encode($response);
	}

	/**
	 * Unban user
	 *
	 * Unbans/activates user account
	 *
	 * Unit tested
	 *
	 * @access admin
	 * @uses Codeigniter-aauth to unban user and validate user_id
	 * @uses input::post int user_id to be unlocked
	 * @return bool Unban fails/succeeds
	 */
	public function unban_user() {
		$user_id = $this->input->post('user_id');
		
		$response = "false";
		if ($this->aauth->get_user($user_id) != false)
		{
			$response = $this->aauth->unban_user($user_id);
		}
		echo json_encode($response);
	}
	
	/**
	 * User search
	 *
	 * Search user by name, phone, email. You can set offset to paginate results. Max results amount is set to 10 in model.
	 *
	 * @access admin
	 * @uses input::post string search_data search term
	 * @return echo list of results as html
	 */
	 public function user_search() {
        $search_data = $this->input->post('search_data');
		$offset = $this->input->post('offset') ? $this->input->post('offset') : "0";
        $query = $this->Admin_model->get_autocomplete($search_data);
		if (count($query->result()) > 0) {
			foreach ($query->result() as $row) 
			{
				echo "<a class=\"list-group-item\" href=\"javascript:fetchUserData(" . $row->id . ");\">" . $row->name . " " . $row->surname . "</a>";
			}
		} 
		else 
		{
			echo "No results";
		}
	}
	
	/**
	 * Fetch user data
	 * Fetch user data by ajax call
	 * @access admin
	 * @uses input::post user_id user identification number
	 * @return echo result form as html
	 */
	 public function fetch_user_data() {
		// Fetch basic data
        $user_id = $this->input->post('user_id');
        $query = $this->Admin_model->get_user_data($user_id);
		$basic = $query->result()[0];
		// Fetch group data
		$groups = $this->get_groups($user_id);
		// Fetch user levels
		$levels = array();
		$machine_groups = $this->Admin_model->get_machine_groups();
		foreach($machine_groups->result() as $machine_group)
		{
			$levels[$machine_group->MachineGroupID] = array
			(
				'category' => $machine_group->Name,
				'machines' => $this->get_machine_group_levels($user_id, $machine_group->MachineGroupID)
			);
		}
		// Prepare array for view
		$response = array
		(
			'basic' => $basic,
			'groups' => $groups,
			'levels' => $levels
		);
		$this->load->view('admin/users_form', $response);
	}
	
	private function get_machine_group_levels($user_id, $machine_group_id) 
	{
		$machines = $this->Admin_model->get_machines($machine_group_id);
		$response = array();
		foreach($machines->result() as $machine)
		{
			$level = $this->Admin_model->get_levels($user_id, $machine->MachineID)->result();
			$level = (!empty($level)) ? intval($level[0]->Level) : 0;
			$response[$machine->MachineID] = array (
				'manufacturer' => $machine->Manufacturer,
				'model' => $machine->Model,
				'level' => $level
			);
		}
		return $response;
	}
	
	/**
	 * Save user data
	 * Accepts form as post, validates field and if no errors, saves data to database
	 * @access admin
	 * @uses input::post array containing form fields + user id
	 * @return echo {"success":"true"} or {"success":"false", "errors":array of strings}
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
		
		$groups = $this->aauth->list_groups();
		parse_str($this->input->post('groups'), $group_data);
		$user_groups = $this->aauth->get_user_groups($form_data['user_id']);
		$group_array = array_keys($group_data);
		foreach ($groups as $group) {
			if (in_array($group->id, $group_array)) {
				if (!in_array($group->id, array_map(function($o) { return $o->id; }, $user_groups))) 
				{
					$this->aauth->add_member($form_data['user_id'], $group->id);
				}
			} elseif (in_array($group->id, array_map(function($o) { return $o->id; }, $user_groups))) {
				if (!in_array($group->id, $group_array)) 
				{
					$this->aauth->remove_member($form_data['user_id'], $group->id);
				}
			
			}
		}
		
		parse_str($this->input->post('levels'), $level_data);
		foreach ($level_data as $machine => $level) {
			$level = ($level == "") ? 0 : $level; 
			$this->Admin_model->update_level_data(intval($form_data['user_id']), $machine, $level);
		}

		$errors = $this->verify_data($form_data);
		if (count($errors) != 0) {
			$message = array(
				'success' => 0,
				'errors' => $errors
			);
			echo json_encode($message);
		} else {
			// Save data to database
			// aauth requires that non-changed parameters are null
			$old_data = $this->aauth->get_user($form_data['user_id']);
			if ($form_data['email'] === $old_data->email) $form_data['email'] = null;
			if ($form_data['username'] === $old_data->name) $form_data['username'] = null;
			if (isset($form_data['email']) or isset($form_data['username'])) { 
				if (!$this->aauth->update_user($form_data['user_id'], $form_data['email'], null , $form_data['username'])) 
				{
					$message = array
					(
						'success' => 0,
						'errors' => array("Aauth raised an error on update!")
					);
					echo json_encode($message);
					exit();
				}
			}
			if ($this->Admin_model->update_user_data($form_data)) {
				$message = array
				(
					'success' => 1
				);
			} else {
				$message = array
				(
					'success' => 0,
					'errors' => array("Error while saving data to database")
				);
			}
			echo json_encode($message);
		}
	}
	
	/**
	 * Set user quota
	 * Sets user quota. Can be set manually, or use database default
	 * @access admin
	 * @uses input::post {'user_id: user id, amount: amount of hours'}. Amount is optional.
	 * @return echo json array containing error messages
	 */
	public function set_quota() 
	{
		$user_id = intval($this->input->post('user_id'));
		$amount = $this->input->post('amount');
		$amount = ($amount == -1) ? 10 : $amount; //TODO fetch from database the default. Check if amount is not set at all
		if ($this->Admin_model->set_user_quota($user_id, $amount)) {
			echo json_encode(array('success' => 1, 'amount' => round($amount, 1)));
		} else {
			echo json_encode(array('success' => 0));
		}
	}

	// Helper functions

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
	
	/**
	 * Get groups
	 * Gets all system groups and checks whether user is a member of those
	 * @access admin
	 * @input int user_id identification number for user
	 * @return array of group objects enchanced with in property
	 * 
	 */
	 private function get_groups($user_id) {
		$groups = $this->aauth->list_groups();
		$user_groups = $this->aauth->get_user_groups($user_id);
		$response = array();
		foreach($groups as $group) {
			if (in_array($group->id, array_map(function($o) { return $o->id; }, $user_groups))) {
				$group->in = 1;
			} else {
				$group->in = 0;
			}
		}
		return $groups;
	}
}