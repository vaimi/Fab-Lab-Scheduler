<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Admin extends MY_Controller
{
	public function __construct() {
		parent::__construct();
		if (!$this->aauth->is_admin())
		{
			redirect('404');
		}
		$this->load->model('Admin_model');
		// TODO: Should load all of the time?
		$this->load->library('form_validation');
	}

	//
	// Sites
	//
	public function moderate_general() 
	{
		$this->load->view('partials/header');
		$this->load->view('partials/menu');
		$jdata['title'] = "General settings";
		$jdata['message'] = "You can manage general settings.";
		$data['settings'] = $this->Admin_model->get_general_settings();
		$this->load->view('partials/jumbotron', $jdata);
		$this->load->view('admin/general', $data);
		$this->load->view('partials/footer');
	}
	
	public function moderate_machines() 
	{
		$this->load->view('partials/header');
		$this->load->view('partials/menu');
		$jdata['title'] = "Admin";
		$jdata['message'] = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Sed posuere interdum sem. Quisque ligula eros ullamcorper quis, lacinia quis facilisis sed sapien. Mauris varius diam vitae arcu.";
		//Get machineGroups
		$mGroups = 	$this->Admin_model->get_machine_groups()->result();
		//Machines
		$ms = $this->Admin_model->get_machines()->result();
		$results = array();
		foreach ($mGroups as $mGroup) 
		{
			$tmp = array(
					"MachineGroupID" => $mGroup->MachineGroupID,
					"Name" => $mGroup->Name,
					'active' => $mGroup->active,
					'machines' => array()
// 					"Description" => $mGroup->Description,
// 					"NeedSupervision" => $mGroup->NeedSupervision
			);
			foreach ($ms as $m) 
			{
				if ($mGroup->MachineGroupID == $m->MachineGroupID) 
				{
					$tmp['machines'][] = $m;
				}
			}
			array_push($results,$tmp);
		}
		$d['machineGroups'] = $results;
		$d['machine_groups'] = $mGroups;
		$this->load->view('partials/jumbotron', $jdata);
		$this->load->view('admin/machines', $d);
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
		$data['groups'] = $this->aauth->list_groups();
		$this->load->view('admin/timetable', $data);
		$this->load->view('partials/footer');
	}

	public function moderate_reservations() 
	{
		$this->load->view('partials/header');
		$this->load->view('partials/menu');
		$jdata['title'] = "Reservations";
		$jdata['message'] = "Manage and add reservations";
		$this->load->view('partials/jumbotron', $jdata);
		//Get admins (Supervisors) from db
		$machines = $this->Admin_model->get_machines();
		$users = $this->Admin_model->get_users();
		$data['machines'] = $machines->result();
		$data['users'] = $users->result();
		$this->load->view('admin/reservations', $data);
		$this->load->view('partials/footer');
	}

	public function reservations_get_machines()
	{
		$this->load->model('Reservations_model');
		$response = $this->Reservations_model->reservations_get_machines(true);
		$this->output->set_output(json_encode($response));
	}

	public function reservations_cancel()
	{
		$this->load->model('Reservations_model');
		$reservation = $this->input->post('id');
		$this->Reservations_model->set_reservation_state($reservation, 3);
		$this->cancellation_email($reservation);
		$this->output->set_output(json_encode(array("success"=>1)));
	}

	private function cancellation_email($reservation_id){
		$reservation = $this->Reservations_model->get_reservation_email_info($reservation_id);
		$this->email->from( $this->aauth->config_vars['email'], $this->aauth->config_vars['name']);
		$this->email->to($reservation->email);
		$this->email->subject("Fab Lab session cancellation");
		$email_content = "Dear fabricator,<br>
		<br>
		We're sorry to inform that your reservation is cancelled. Here is your cancelled reservation details: <br>
		<br>
		Reservation id: " . $reservation->ReservationID . "<br>
		Machine: " . $reservation->Manufacturer . " " . $reservation->Model . "<br>
		Reservation starts: " . $reservation->StartTime . "<br>
		Reservation ends: " . $reservation->EndTime . "<br>
		<br>
		Sincerely,<br>" .
		$this->aauth->config_vars['name'];

		$this->email->message($email_content);
		$this->email->send();
	}

	public function reservations_reserve()
	{
		$this->load->model('Reservations_model');
		$user = $this->input->post('user');
		$machines = $this->input->post('machines');
		$start = $this->input->post('start');
		$end = $this->input->post('end');
		//0 no force
		//1 allow overlap
		//2 delete overlapping reservations
		$force = $this->input->post('force');

		if($force == 0) {
			// dry run
			$is_overlapping = false;
			foreach ($machines as $machine) 
			{
				$m_id = str_replace("mac_", "", $machine);
				$overlaps = $this->Reservations_model->is_reserved($start, $end, (int)($m_id));
				if ($overlaps)
				{
					$is_overlapping = true;
				}
			}
			if ($is_overlapping) {
				$response = array(
					"success" => 0,
					"errors" => array("Overlapping")
				);
				$this->output->set_output(json_encode($response));
				return;
			}
			$force = 1;
		}
		if($force == 2)
		{
			foreach ($machines as $machine)
			{
				$m_id = str_replace("mac_", "", $machine);
				$reservations = $this->Reservations_model->reservations_get_reserved_slots(strtotime($start), strtotime($end), (int)$machine);
				foreach ($reservations as $rs) {
					$this->Reservations_model->set_reservation_state($rs->ReservationID, 3);
					$this->cancellation_email($rs->ReservationID);
				}
			}
			//remove overlapping sessions
			$force = 1;
		}
		// allow overlap
		if($force == 1) 
		{
			foreach ($machines as $machine) 
			{
				$m_id = str_replace("mac_", "", $machine);
				$data = array(
						'MachineID' => (int)$m_id,
						'aauth_usersID' => (int)$user,
						'StartTime' => $start,
						'EndTime' => $end,
						'QRCode' => "",
						'PassCode' => ""
				);
				$reservation_id = $this->Reservations_model->set_new_reservation($data);
					
			}
			$response = array(
				"success" => 1,
				"errors" => array()
			);
			$this->output->set_output(json_encode($response));
			return;
		}



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
			//validate variables
			$this->form_validation->set_rules('name', 'Machine group name', 'required');
			$name = $this->input->post('name');
			// TODO: $this->form_validation->set_rules('user_id', 'User Id', 'required|is_natural');
			// TODO: xss_filtering?
			$description = $this->input->post('description');
			// TODO: ??
			$need_supervision = $this->input->post('need_supervision')?$this->input->post('need_supervision'):'';
			if ($this->form_validation->run() == FALSE)
			{
				//echo errors.
				echo validation_errors();
				return;
			}
			$errors = [];
			if (trim($name) == '')
			{
				$errors[] = 'Name can not be blank';
			}
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
				$data = array("Name" => $name , "Description" => $description, "NeedSupervision" => $need_supervision);
				$this->Admin_model->create_new_machine_group($data);
				redirect('admin/moderate_machines');
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
				//echo errors.
				echo validation_errors();
				die();
			}
			else {
				//insert machine into db.
				$this->Admin_model->create_new_machine( array(
						"MachineGroupID" => $machine_group_id,
						"MachineName" => $machinename,
						"Manufacturer" => $manufacturer,
						"Model" => $model,
						"NeedSupervision" => $needSupervision,
						"Description" => $desc
				));
				redirect('admin/moderate_machines');
			}
		}
		else
		{
			redirect('404');
		}
	}

	//
	// AJAX functions
	//
	
    /**
     * Supervision session fetching
     * 
     * Fetches supervision sessions from the database. If supervision session is already in one of the session variables, 
     * version in database is discarded. 
     * 
     * @uses input::post 'start' Events that starts after this time are fetched, format Y-m-d H:i:s
     * @uses input::post 'end'  Events that starts before this time are fetched, format Y-m-d H:i:s
     * @uses _SESSION['sv_unsaved_modified_items'] for removing duplicate entries
     * @uses _SESSION['sv_unsaved_deleted_items'] for removing duplicate entries
     * @uses _SESSION['sv_saved_items'] Fetched items are saved also to this variable
     * 
     * @access public
     * @return echo events in json array //TODO example
     */
    public function timetable_fetch_supervision_sessions() {
    	// Validate input
        $this->form_validation->set_data($this->input->get());
		$this->form_validation->set_rules('start', 'start day', 'required|exact_length[10]|regex_match[(\d{4}-\d{2}-\d{2})]');
		$this->form_validation->set_rules('end', 'end day', 'required|exact_length[10]|regex_match[(\d{4}-\d{2}-\d{2})]');
	    if ($this->form_validation->run() == FALSE)
		{
			//echo errors.
			echo json_encode(validation_errors());
			die();
		}

        // get calendar request
        $start_time = $this->input->get('start');
        $end_time = $this->input->get('end');
        
        // get supervision slots from db
        $slots = $this->Admin_model->timetable_get_supervision_slots($start_time, $end_time);
        
        $response = array();
        
        // Collect slot id's from arrays
        $modIDs = array_map(function($o) { return $o->id; }, $this->session->userdata('sv_unsaved_modified_items'));
        $modIDsDeleted = array_map(function($o) { return $o->id; }, $this->session->userdata('sv_unsaved_deleted_items'));
        $modIDsSaved = array_map(function($o) { return $o->id; }, $this->session->userdata('sv_saved_items'));
        
        // Go through database slots
        foreach($slots->result() as $slot)
		{
            // Check if we have already these in some session variable
            if (!in_array($slot->SupervisionID, $modIDs) and !in_array($slot->SupervisionID, $modIDsDeleted))
            {
                // Make array in output format
                $slot_array = array (
                    'id' => $slot->SupervisionID,
                    'title' => "uid: ". $slot->aauth_usersID. " sid: ". $slot->SupervisionID,
                    'group' => $slot->aauth_groupsID,
                    'assigned' => $slot->aauth_usersID,
                    'start' => $slot->StartTime,
                    'end' => $slot->EndTime,
                	'color' => $slot->aauth_groupsID === PUBLIC_GROUP_ID ? "#f0ad4e" : "#5cb85c" //public : saved color.
                );
                array_push($response, $slot_array);

                // If not already in sv_saved_items
                if (!in_array($slot->SupervisionID, $modIDsSaved))
                {
	                //create saved slot
	                $current_saved_slots = $this->session->userdata('sv_saved_items');
	                $s = new stdClass();
	                $s->assigned = $slot->aauth_usersID;
	                $s->group = $slot->aauth_groupsID;
	                $s->start = $slot->StartTime;
	                $s->end = $slot->EndTime;
	                $s->id = $slot->SupervisionID;
	                //Save another copy for discard changes.
	                $s->original = clone $s;
	                $s->original->list = "sv_saved_items";
	                $s->original->color = $s->group === PUBLIC_GROUP_ID ? "#f0ad4e" : "#5cb85c"; //public : saved color.
	                $current_saved_slots[$s->id] = $s;
	                $this->session->set_userdata('sv_saved_items', $current_saved_slots);
                }
            }          
        }
        echo json_encode($response);
    }
    
    /**
     * Modified/new session fetching
     * 
     * Fetches supervision sessions from the new/modified session variables. 
     * 
     * @uses input::post 'start' Events that starts after this time are fetched, format Y-m-d H:i:s.
     * @uses input::post 'end'  Events that starts before this time are fetched, format Y-m-d H:i:s.
     * @uses _SESSION['sv_unsaved_modified_items'] session variable for modified entries
     * @uses _SESSION['sv_unsaved_new_items'] session variable for new entries
     * 
     * @access public
     * @return echo events in json array //TODO example
     */
    public function timetable_fetch_mod_and_new_sessions() {
    	// Validate input
        $this->form_validation->set_data($this->input->get());
		$this->form_validation->set_rules('start', 'start day', 'required|exact_length[10]|regex_match[(\d{4}-\d{2}-\d{2})]');
		$this->form_validation->set_rules('end', 'end day', 'required|exact_length[10]|regex_match[(\d{4}-\d{2}-\d{2})]');
	    if ($this->form_validation->run() == FALSE)
		{
			//echo errors.
			echo json_encode(validation_errors());
			die();
		}

		// Get the request
        $start_time = $this->input->get('start');
        $end_time = $this->input->get('end');
        // Merge session variables for response
        $response = array_merge($this->session->userdata('sv_unsaved_new_items'), $this->session->userdata('sv_unsaved_modified_items'));
        echo json_encode($response);
    }
    
    /**
     * Deleted session fetching
     * 
     * Sessions aren't deleted before save is pressed. They are just marked as deleted before that. This functions fetches those events.
     * 
     * @uses input::post 'start' Events that starts after this time are fetched, format Y-m-d H:i:s. 
     * @uses input::post 'end'  Events that starts before this time are fetched, format Y-m-d H:i:s.
     * @uses _SESSION['sv_unsaved_deleted_items'] session variable for deleted entries
     * 
     * @access public
     * @return echo events in json array //TODO example
     */
    public function timetable_fetch_deleted_sessions() {
    	// Validate input
        $this->form_validation->set_data($this->input->get());
		$this->form_validation->set_rules('start', 'start day', 'required|exact_length[10]|regex_match[(\d{4}-\d{2}-\d{2})]');
		$this->form_validation->set_rules('end', 'end day', 'required|exact_length[10]|regex_match[(\d{4}-\d{2}-\d{2})]');
	    if ($this->form_validation->run() == FALSE)
		{
			//echo errors.
			echo json_encode(validation_errors());
			die();
		}

		// Get request
        $start_time = $this->input->get('start');
        $end_time = $this->input->get('end');
        
        $response = $this->session->userdata('sv_unsaved_deleted_items');
        //Re-create array indexes
        $response = array_values($response);
        echo json_encode($response);
    }
    
	/**
	 * Save events from session file to database
	 * @access public
	 */
	public function timetable_save() {
        $new_slots = $this->session->userdata('sv_unsaved_new_items');
        $modified_slots = $this->session->userdata('sv_unsaved_modified_items');
        $deleted_slots = $this->session->userdata('sv_unsaved_deleted_items');
        
        $errors = array();
        $emails = array();
        
        foreach($new_slots as $slot)
        {
            $this->Admin_model->timetable_save_new($slot);
        }
        foreach($modified_slots as $slot)
        {
            //Get all reservation which is in the modified slot
            $reservations = $this->Admin_model->get_reservations_by_slot($slot, $slot->original->start, $slot->original->end);
            //Delete the reservation if it fulfills the requirements (Modification does not affect to the reservation)
            foreach ($reservations as $key => $reservation)
            {
            	//get levels
            	 $user_machine_lvl = $this->Admin_model->get_levels($reservation['aauth_usersID'], $reservation['MachineID'] );
            	 $supervisor_machine_lvl = $this->Admin_model->get_levels($slot->assigned, $reservation['MachineID'] );
            	 $user_machine_lvl = isset($user_machine_lvl) ? $user_machine_lvl->row()->Level : 1;
            	 $supervisor_machine_lvl = isset($supervisor_machine_lvl) ? $supervisor_machine_lvl->row()->Level : 1;
            	 //If NOT user level 1 or 2 and supervisor lvl is 4 then delete reservation so modification msg is not sended.
            	 if( !(($user_machine_lvl == USER_UNSKILLED || $user_machine_lvl == USER_NEEDS_SUPERVISOR) &&
            	 	$supervisor_machine_lvl == SUPERVISOR_CAN_SUPERVISE) ) 
            	 { 
            	 	unset($reservations[$key]);
            	 }
            	 		
            }
            
            //Get all reservation user ids
            $user_ids = array_map(function($o) { return $o['aauth_usersID']; }, $reservations);
            //remove duplicates.
            $user_ids = array_unique($user_ids);
            //send email to every user.
            foreach ($user_ids as $user_id)
            {
            	$user = $this->Admin_model->get_user_data($user_id)->row();
            	$email = $user->email;
            	array_push($emails, "<br>" . $email);
            	$data['fullname'] = $user->surname;
            	$data['slot_start'] = $slot->start;
            	$data['slot_end'] = $slot->end;
            	// Send email to associated reservations.
            	$this->send_modified_email($email, $data);
            }
            $this->Admin_model->timetable_save_modified($slot);
        }
        foreach($deleted_slots as $slot)
        {
            if ($slot->id > 0) //slot is saved before ( fetched from db)
            {
            	//Get all reservation which is in the deleted slot
            	$reservations = $this->Admin_model->get_reservations_by_slot($slot, $slot->start, $slot->end);
            	//Get all reservation user ids
            	$user_ids = array_map(function($o) { return $o['aauth_usersID']; }, $reservations);
            	//remove duplicates.
            	$user_ids = array_unique($user_ids);
            	//send email to every user.
            	foreach ($user_ids as $user_id)
            	{
            		$user = $this->Admin_model->get_user_data($user_id)->row();
            		$email = $user->email;
            		array_push($emails, "<br>" . $email);
            		$data['fullname'] = $user->surname;
            		$data['slot_start'] = $slot->start;
            		$data['slot_end'] = $slot->end;
            		// Send email to associated reservations.
            		$this->send_cancel_email($email, $data);
            	}
                $this->Admin_model->timetable_save_deleted($slot);
            }
        }
        $this->session->set_userdata('sv_unsaved_new_items', array());
        $this->session->set_userdata('sv_unsaved_modified_items', array());
        $this->session->set_userdata('sv_unsaved_deleted_items', array());
        echo json_encode(array("success" => 1, "errors" => $errors , "emails_sent" => $emails ));
    }
    
     /**
     * New slot
     * 
     * Generate new slot for the calendar. New slots are assigned always with negative id.
     * 
     * @uses input::post 'start' Start of event, format Y-m-d H:i:s.
     * @uses input::post 'end'  End of event, format Y-m-d H:i:s.
     * @uses input::post 'assigned' int Assigned admin id.
     * @uses _SESSION['sv_unsaved_new_items'] session variable for new entries
     * 
     * @access public
     * @return echo events in json array //TODO example
     */
    public function timetable_new_slot() {
    	// Validate input
        $this->form_validation->set_rules('assigned', 'assigned admin', 'required|numeric');
		$this->form_validation->set_rules('start', 'start day', 'required|exact_length[19]|regex_match[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})]');
		$this->form_validation->set_rules('end', 'end day', 'required|exact_length[19]|regex_match[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})]');
	    if ($this->form_validation->run() == FALSE)
		{
			//echo errors.
			echo json_encode(validation_errors());
			die();
		}

		// Get request
        $assigned = $this->input->post("assigned");
        $start = $this->input->post("start");
        $end = $this->input->post("end");    
        
        // Make new slot
        $slot = new stdClass();
        $slot->assigned = $assigned;
        $slot->group = 3;
        $slot->start = $start;
        $slot->end = $end;
        $slot->id = -1 - count($this->session->userdata('sv_unsaved_new_items'));
        
        //Add original to slots for discarding changes.
        $slot->original = clone $slot;
        $slot->original->list = 'sv_unsaved_new_items';
        $slot->original->color = "#5bc0de";
        $unsaved = $this->session->userdata('sv_unsaved_new_items');
        $unsaved[$slot->id] = $slot;
        $this->session->set_userdata('sv_unsaved_new_items', $unsaved);
        echo json_encode(array("id" => $slot->id));
    }
    
    public function timetable_remove_slot() {
    	// Validate input
        $this->form_validation->set_data($this->input->get());
		$this->form_validation->set_rules('id', 'slot id', 'required|numeric');
	    if ($this->form_validation->run() == FALSE)
		{
			//echo errors.
			echo json_encode(validation_errors());
			die();
		}

        $id = $this->input->post("id"); 
        //$assigned = $this->input->post("assigned");
        //$group = $this->input->post("group");
        //$start = $this->input->post("start");
        //$end = $this->input->post("end");  
        
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
                    unset($_SESSION['sv_unsaved_new_items'][$key_id]);
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
                    unset($_SESSION['sv_unsaved_modified_items'][$key_id]);
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
                $new_slot->group = $slot->aauth_groupsID;
                $new_slot->start = $slot->StartTime;
                $new_slot->end = $slot->EndTime;
                $new_slot->original = clone $new_slot;
                $new_slot->original->list = "sv_saved_items";
                $new_slot->original->color = "#5cb85c";
                $deleted = $this->session->userdata('sv_unsaved_deleted_items');
                $deleted[$id] = $new_slot;
                $this->session->set_userdata('sv_unsaved_deleted_items', $deleted);
            }
        }
        echo json_encode(array("success" => 1 ,$this->session->userdata('sv_unsaved_deleted_items')));
    }
    
    /*    
    public function timetable_restore_slot_old() {
    	//TODO validation
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
    }*/
    
    public function timetable_restore_slot() {
    	$this->form_validation->set_data($this->input->get());
		$this->form_validation->set_rules('id', 'slot id', 'required|numeric');
	    if ($this->form_validation->run() == FALSE)
		{
			//echo errors.
			echo json_encode(validation_errors());
			die();
		}
    	$id = $this->input->post("id");

        $modIDs = array_map(function($o) { return $o->id; }, $this->session->userdata('sv_unsaved_modified_items'));
    	//Discard changes if in sv_unsaved_modified_items
    	if (in_array($id, $modIDs))
    	{
    		$tmp = $this->session->userdata('sv_unsaved_modified_items');
    		//get first slot list
    		$new_list = $tmp[$id]->original->list;
    		$tmp2 = $this->session->userdata($new_list);
    		$tmp2[$id] = $tmp[$id];
    		//Assign old values
    		$tmp2[$id]->assigned = $tmp[$id]->original->assigned;
    		$tmp2[$id]->group = $tmp[$id]->original->group;
    		$tmp2[$id]->start = $tmp[$id]->original->start;
    		$tmp2[$id]->end = $tmp[$id]->original->end;
    		//Delete slot in previous list
    		unset($tmp[$id]);
    		$this->session->set_userdata($new_list, $tmp2);
    		$this->session->set_userdata('sv_unsaved_modified_items', $tmp);
    		echo json_encode(array("success" => true, 
    				"assigned" => $tmp2[$id]->assigned,
    				"group" => $tmp2[$id]->group,
    				"start" => $tmp2[$id]->start, 
    				"end" => $tmp2[$id]->end, 
    				"color" => $tmp2[$id]->original->color));
    		return;
    	}
    	$modIDs = array_map(function($o) { return $o->id; }, $this->session->userdata('sv_unsaved_new_items'));
    	//Discard changes if in sv_unsaved_new_items
    	if (in_array($id, $modIDs))
    	{
    		$tmp = $this->session->userdata('sv_unsaved_new_items');
    		//get first slot list
    		$new_list = $tmp[$id]->original->list;
    		$tmp2 = $this->session->userdata($new_list);
    		$tmp2[$id] = $tmp[$id];
    		//Assign old values
    		$tmp2[$id]->assigned = $tmp[$id]->original->assigned;
    		$tmp2[$id]->group = $tmp[$id]->original->group;
    		$tmp2[$id]->start = $tmp[$id]->original->start;
    		$tmp2[$id]->end = $tmp[$id]->original->end;
    		$color = "#5bc0de";
    		//Delete slot in previous list
    		unset($tmp[$id]);
    		$this->session->set_userdata($new_list, $tmp2);
    		$this->session->set_userdata('sv_unsaved_new_items', $tmp);
    		echo json_encode(array("success" => true,
    				"assigned" => $tmp2[$id]->assigned,
    				"group" => $tmp2[$id]->group,
    				"start" => $tmp2[$id]->start,
    				"end" => $tmp2[$id]->end,
    				"color" => $tmp2[$id]->original->color));
    		return;
    	}
    	$modIDs = array_map(function($o) { return $o->id; }, $this->session->userdata('sv_unsaved_deleted_items'));
    	//Discard changes if in sv_unsaved_deleted_items
    	if (in_array($id, $modIDs))
    	{
    		$tmp = $this->session->userdata('sv_unsaved_deleted_items');
    		//get first slot list
    		$new_list = $tmp[$id]->original->list;
    		$tmp2 = $this->session->userdata($new_list);
    		$tmp2[$id] = $tmp[$id];
    		//Assign old values
    		$tmp2[$id]->assigned = $tmp[$id]->original->assigned;
    		$tmp2[$id]->group = $tmp[$id]->original->group;
    		$tmp2[$id]->start = $tmp[$id]->original->start;
    		$tmp2[$id]->end = $tmp[$id]->original->end;
    		$color = "#5bc0de";
    		//Delete slot in previous list
    		unset($tmp[$id]);
    		$this->session->set_userdata($new_list, $tmp2);
    		$this->session->set_userdata('sv_unsaved_deleted_items', $tmp);
    		echo json_encode(array("success" => true, 
    				"assigned" => $tmp2[$id]->assigned, 
    				"group" => $tmp2[$id]->group,
    				"start" => $tmp2[$id]->start, 
    				"end" => $tmp2[$id]->end, 
    				"color" => $tmp2[$id]->original->color));
    		return;
    	}
    	$modIDs = array_map(function($o) { return $o->id; }, $this->session->userdata('sv_saved_items'));
    	//Discard changes if in sv_saved_items
    	if (in_array($id, $modIDs))
    	{
    		echo json_encode(array("success" => false,
    				"Error" => "Nothing to discard."));
    		return;
    	}
    }
    
    public function timetable_modify_slot() {
    	// Validate input
        $this->form_validation->set_rules('assigned', 'assigned admin', 'required|numeric');
        $this->form_validation->set_rules('id', 'slot id', 'required|numeric');
        $this->form_validation->set_rules('group', 'target group', 'required|numeric');
		$this->form_validation->set_rules('start', 'start day', 'required|exact_length[19]|regex_match[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})]');
		$this->form_validation->set_rules('end', 'end day', 'required|exact_length[19]|regex_match[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})]');
	    if ($this->form_validation->run() == FALSE)
		{
			//echo errors.
			echo json_encode(validation_errors());
			die();
		}

        $id = $this->input->post("id"); 
        $assigned = $this->input->post("assigned");
        $group = $this->input->post("group");
        $start = $this->input->post("start");
        $end = $this->input->post("end");    
        $event = array();
        $modIDs = array_map(function($o) { return $o->id; }, $this->session->userdata('sv_unsaved_modified_items'));
        //Modify if in sv_unsaved_modified_items
        if (in_array($id, $modIDs))
        {
        	$tmp = $this->session->userdata('sv_unsaved_modified_items');
        	$event = $tmp[$id];
        	unset($tmp[$id]);
        	$this->session->set_userdata('sv_unsaved_modified_items', $tmp);
        }
        $modIDs = array_map(function($o) { return $o->id; }, $this->session->userdata('sv_unsaved_new_items'));
        //Modify if in sv_unsaved_new_items
        if (in_array($id, $modIDs))
        {
        	$tmp = $this->session->userdata('sv_unsaved_new_items');
        	$event = $tmp[$id];
        	unset($tmp[$id]);
        	$this->session->set_userdata('sv_unsaved_new_items', $tmp);
        }
        $modIDs = array_map(function($o) { return $o->id; }, $this->session->userdata('sv_unsaved_deleted_items'));
        //Modify if in sv_unsaved_deleted_items
        if (in_array($id, $modIDs))
        {
        	$tmp = $this->session->userdata('sv_unsaved_deleted_items');
        	$event = $tmp[$id];
        	unset($tmp[$id]);
        	$this->session->set_userdata('sv_unsaved_deleted_items', $tmp);
        }
        $modIDs = array_map(function($o) { return $o->id; }, $this->session->userdata('sv_saved_items'));
        //Modify if in sv_saved_items
        if (in_array($id, $modIDs))
        {
        	$tmp = $this->session->userdata('sv_saved_items');
        	$event = $tmp[$id];
        	unset($tmp[$id]);
        	$this->session->set_userdata('sv_saved_items', $tmp);
        }
        
        if($id > 0)
        {
            $slot = $event;
            $slot->assigned = $assigned;
            $slot->group = $group;
            $slot->start = $start;
            $slot->end = $end;
            $slot->id = $id;
            $slots = $this->session->userdata('sv_unsaved_modified_items');
            $slots[$id] = $slot;
            $this->session->set_userdata('sv_unsaved_modified_items', $slots);
        } 
        else
        {
            $slot = $event;
            $slot->assigned = $assigned;
            $slot->group = $group;
            $slot->start = $start;
            $slot->end = $end;
            $slot->id = $id;
            $slots = $this->session->userdata('sv_unsaved_new_items');
            $slots[$id] = $slot;
            $this->session->set_userdata('sv_unsaved_new_items', $slots);
        }
        echo json_encode(array("success" => 1));
    }
    public function timetable_confirm_slot() {
    	// Validate input
        $this->form_validation->set_rules('assigned', 'assigned admin', 'required|numeric');
        $this->form_validation->set_rules('id', 'slot id', 'required|numeric');
        $this->form_validation->set_rules('group', 'target group', 'required|numeric');
		$this->form_validation->set_rules('start', 'start day', 'required|exact_length[19]|regex_match[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})]');
		$this->form_validation->set_rules('end', 'end day', 'required|exact_length[19]|regex_match[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})]');
	    if ($this->form_validation->run() == FALSE)
		{
			//echo errors.
			echo json_encode(validation_errors());
			die();
		}
    	$id = $this->input->post("id");
    	$assigned = $this->input->post("assigned");
    	$group = $this->input->post("group");
    	$start = $this->input->post("start");
    	$end = $this->input->post("end");
    	$color = "#000000";
    	$modIDs = array_map(function($o) { return $o->id; }, $this->session->userdata('sv_unsaved_modified_items'));
    	//Modify if in sv_unsaved_modified_items
    	if (in_array($id, $modIDs))
    	{
    		$tmp = $this->session->userdata('sv_unsaved_modified_items');
    		$tmp[$id]->assigned = $assigned;
    		$tmp[$id]->group = $group;
    		$tmp[$id]->start = $start;
    		$tmp[$id]->end = $end;
    		$this->session->set_userdata('sv_unsaved_modified_items', $tmp);
    		$color = "#5bc0de";
    	}
    	$modIDs = array_map(function($o) { return $o->id; }, $this->session->userdata('sv_unsaved_new_items'));
    	//Modify if in sv_unsaved_new_items
    	if (in_array($id, $modIDs))
    	{
    		$tmp = $this->session->userdata('sv_unsaved_new_items');
    		$tmp[$id]->assigned = $assigned;
    		$tmp[$id]->group = $group;
    		$tmp[$id]->start = $start;
    		$tmp[$id]->end = $end;
    		$this->session->set_userdata('sv_unsaved_new_items', $tmp);
    		$color = "#5bc0de";
    	}
    	$modIDs = array_map(function($o) { return $o->id; }, $this->session->userdata('sv_unsaved_deleted_items'));
    	//Modify if in sv_unsaved_deleted_items
    	if (in_array($id, $modIDs))
    	{
    		$tmp = $this->session->userdata('sv_unsaved_deleted_items');
    		$tmp[$id]->assigned = $assigned;
    	    $tmp[$id]->group = $group;
    		$tmp[$id]->start = $start;
    		$tmp[$id]->end = $end;
    		$this->session->set_userdata('sv_unsaved_deleted_items', $tmp);
    		$color = "#d9534f";
    	}
    	$modIDs = array_map(function($o) { return $o->id; }, $this->session->userdata('sv_saved_items'));
    	//Modify if in sv_saved_items
    	if (in_array($id, $modIDs))
    	{
    		$tmp = $this->session->userdata('sv_saved_items');
    		//$event = array_filter($tmp, function ($e) use (&$id) { return $e->id === $id; } );
    		$tmp[$id]->assigned = $assigned;
    		$tmp[$id]->group = $group;
    		$tmp[$id]->start = $start;
    		$tmp[$id]->end = $end;
    		
    		$tmp2 = $this->session->userdata('sv_unsaved_modified_items');
    		$tmp2[$id] = $tmp[$id];
    		unset($tmp[$id]);
    		$this->session->set_userdata('sv_saved_items', $tmp);
    		$this->session->set_userdata('sv_unsaved_modified_items', $tmp2);
    		$color = "#5bc0de";
    	}
    	echo json_encode(array("success" => 1 , "assigned" => $assigned, "group" => $group,"start" => $start, "end" => $end, "color" => $color));
    }
	//Schedules 
	
    /**
     * Copy schedules from database with offset.
     *
     * @access admin
     */
    public function schedule_copy() {
    	// TODO what if slot are at the midnight??? should we copy it also
    	if ($this->input->server('REQUEST_METHOD') == 'POST') 
    	{
    		//If slots are modified or deleted.
    		if (count($this->session->userdata('sv_unsaved_modified_items')) > 0 
    		|| count($this->session->userdata('sv_unsaved_new_items')) > 0
    		|| count($this->session->userdata('sv_unsaved_deleted_items')) > 0)
    		{
    			echo json_encode(array("Error" => "Save timetable first."));
    			return;
    		}
    		$this->form_validation->set_rules('startDate', 'Start Date', 'required|exact_length[10]|regex_match[(\d{4}-\d{2}-\d{2})]');
    		$this->form_validation->set_rules('endDate', 'End Date', 'required|exact_length[10]|regex_match[(\d{4}-\d{2}-\d{2})]');
    		$this->form_validation->set_rules('copyStartDate', 'Copy Start Date', 'required|exact_length[10]|regex_match[(\d{4}-\d{2}-\d{2})]');

    		if ($this->form_validation->run() == FALSE)
    		{
    			//echo errors.
    			echo validation_errors();
    			die();
    		}
    		else 
    		{
    			$startDate = new DateTime($this->input->post("startDate"));
    			$endDate = new DateTime($this->input->post("endDate"));
    			$copyStartDate  = new DateTime($this->input->post("copyStartDate"));
    			//Set time to 23:59:59
    			$endDate->setTime(23,59,59);
    			$count_affected_rows = $this->Admin_model->schedule_copy($startDate, $endDate, $copyStartDate);
    			echo json_encode(array("affected" => $count_affected_rows));
    		}
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
    		$this->form_validation->set_rules('startDate', 'Start Date', 'required|exact_length[10]|regex_match[(\d{4}-\d{2}-\d{2})]');
    		$this->form_validation->set_rules('endDate', 'End Date', 'required|exact_length[10]|regex_match[(\d{4}-\d{2}-\d{2})]');
    		if ($this->form_validation->run() == FALSE)
    		{
    			//echo errors.
    			echo validation_errors();
    			die();
    		}
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
    			if ($sDate >= $startDate && $eDate <= $endDate) 
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
    			if ($sDate >= $startDate && $eDate <= $endDate)
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
    			if ($sDate >= $startDate && $eDate <= $endDate)
    			{
    				unset($tmp[$slot->id]);
    				$slots_current_deleted[$slot->id] = $slot;
    			}
    		}
    		$this->session->set_userdata('sv_unsaved_modified_items', $tmp);
    		
    		$this->session->set_userdata('sv_unsaved_deleted_items', $slots_current_deleted);
     		echo json_encode(array(
     		"deleted_ids" => array_map(function($o) { return $o->id; }, $this->session->userdata('sv_unsaved_deleted_items') ),
			"success" => true     		
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
		// Validate input
        /*$this->form_validation->set_rules('id', 'user id', 'required|numeric');
	    if ($this->form_validation->run() == FALSE)
		{
			//echo errors.
			echo json_encode(validation_errors());
			die();
		}*/
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
		//$this->form_validation->set_rules('user_id', 'User Id', 'required|is_natural');
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
		//$this->form_validation->set_rules('user_id', 'User Id', 'required|is_natural');
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
	 	//validation form??
        $search_data = $this->input->post('search_data');
		$offset = $this->input->post('offset') ? $this->input->post('offset') : "0";
        $query = $this->Admin_model->get_autocomplete($search_data);
		if (count($query->result()) > 0) {
			foreach ($query->result() as $row) 
			{
				echo "<a class=\"list-group-item\" href=\"javascript:fetchUserData(" . $row->id . ");\">" . $row->surname . "</a>";
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
	 	//$this->form_validation->set_rules('user_id', 'User Id', 'required|is_natural');
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
		$reservations = $this->Admin_model->get_reservations($user_id);
		$data['results'] = $reservations;
		$reservations_view = $this->load->view('user/reservations_list', $data, true);
		//Sort array by start time.
		usort($reservations, function($a, $b)
		{
			return $a['StartTime'] < $b['StartTime'];
		});


		// Prepare array for view
		$response = array
		(
			'basic' => $basic,
			'groups' => $groups,
			'levels' => $levels,
			'reservations_view' => $reservations_view
		);
		$this->load->view('admin/users_form', $response);
	}
	/**
	 * 
	 */
	public function get_reservations()
	{
		if (!$this->aauth->is_loggedin())
		{
			$this->load->view('page_not_found_404');
			return;
		}
		$id = $this->input->post('user_id');
		//Contains info about reservations
		$reservations = $this->Admin_model->get_reservations($id);
		//Sort array by start time.
		usort($reservations, function($a, $b)
		{
			return $a['StartTime'] < $b['StartTime'];
		});
		$data['results'] = $reservations;
		$this->load->view('user/reservations_list', $data);
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
	 * Save general settings.
	 * Accepts form as post, validates field and if no errors, saves data to database
	 * @access admin
	 * @uses input::post array containing form fields
	 */
	public function save_general_settings() {
		//if not post request
		if (!$this->input->server('REQUEST_METHOD') == 'POST') return;
		//If user not admin
		if ( !$this->aauth->is_admin() ) return;
		//validation
		$this->form_validation->set_rules('reservation_deadline', 'Reservation deadline', 'required|regex_match[(\d{2}:\d{2})]');
		$this->form_validation->set_rules('reservation_timespan', 'Reservation timespan', 'required|is_natural_no_zero');
		$this->form_validation->set_rules('default_tokens', 'Default tokens', 'required|is_natural_no_zero');
		$this->form_validation->set_rules('nightslot_pre_time', 'Nightslot preparation time', 'required|is_natural_no_zero');
		$this->form_validation->set_rules('nightslot_threshold', 'Nightslot threshold time', 'required|is_natural_no_zero');
		$this->form_validation->set_rules('interval', 'Interval (days, weeks or months)', 'required|callback_interval_check');
		//Send error msg
		if ($this->form_validation->run() == FALSE)
		{
			//echo errors.
			echo validation_errors();
			return;
		}
		//Take time to HH:mm format	
		$deadline = new DateTime( $this->input->post('reservation_deadline') );
		$deadline = $deadline->format("H:i");
		$settings = array();
		$settings['reservation_deadline'] = $deadline;
		$settings['reservation_timespan'] = $this->input->post('reservation_timespan');
		$settings['interval'] = $this->input->post('interval');
		$settings['nightslot_pre_time'] = $this->input->post('nightslot_pre_time');
		$settings['nightslot_threshold'] = $this->input->post('nightslot_threshold');
		$settings['default_tokens'] = $this->input->post('default_tokens');
		
		//put settings to the db
		$this->Admin_model->set_general_settings($settings);
		redirect("/admin/moderate_general", "refresh");
	}
	/**
	 * Save user data
	 * Accepts form as post, validates field and if no errors, saves data to database
	 * @access admin
	 * @uses input::post array containing form fields + user id
	 * @return echo {"success":"true"} or {"success":"false", "errors":array of strings}
	 */
	public function save_user_data() {
		//$this->form_validation->set_rules('user_id', 'User Id', 'required|is_natural');
		//$this->form_validation->set_rules('username', 'User name', 'required|alpha_numeric');
		//$this->form_validation->set_rules('password', 'Password', 'required|alpha_numeric');
		//$this->form_validation->set_rules('surname', 'Last name', 'required|alpha');
		//$this->form_validation->set_rules('password', 'Password', 'required|alpha_numeric');
		//$this->form_validation->set_rules('email', 'Email', 'required|alpha_numeric');
		//$this->form_validation->set_rules('address_street', 'Street address', 'alpha_numeric');
		//$this->form_validation->set_rules('address_postal_code', 'Zip code', 'numeric');
		//$this->form_validation->set_rules('phone_number', 'Phone number', 'numeric');
		//$this->form_validation->set_rules('company', 'Company', '');
		//$this->form_validation->set_rules('student_number', 'Student number', 'numeric');
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
		//$this->form_validation->set_rules('user_id', 'User Id', 'required|is_natural');
		$user_id = intval($this->input->post('user_id'));
		$amount = $this->input->post('amount');
		$amount = ($amount == -1) ? 10 : $amount; //TODO fetch from database the default. Check if amount is not set at all
		if ($this->Admin_model->set_user_quota($user_id, $amount)) {
			echo json_encode(array('success' => 1, 'amount' => round($amount, 1)));
		} else {
			echo json_encode(array('success' => 0));
		}
	}
	
	public function send_emails()
	{
		if (!$this->aauth->is_admin())
		{
			redirect('404');
		}
		$this->load->view('partials/header');
		$this->load->view('partials/menu');
		if ($this->input->method() != 'post')
		{
			
			$jdata = array();
			$data = array('email_content' => '', 'email_subject' => '', 'action' => 'form');
			$jdata['title'] = "Send emails to users";
			$jdata['message'] = "This function allow admins to send emails to all registered users.";
			$this->load->view('partials/jumbotron', $jdata);
			$this->load->view('admin/send_emails', $data);
			$this->load->view('partials/footer');
			return;
		}
		else //post
		{
			$email_subject = $this->input->post('email_subject');
			$email_content = $this->input->post('email_content');
			$action = $this->input->post('action');
			
			$jdata = array();
			$data = array('email_content' => $email_content, 'email_subject' => $email_subject, 'action' => $action);
			$jdata['title'] = "Send emails to users";
			$jdata['message'] = "This function allow admins to send emails to all registered users.";
			$this->load->view('partials/jumbotron', $jdata);
			$this->load->view('admin/send_emails', $data);
			$this->load->view('partials/footer');
			
			if ($action == 'test')
			{
				$this->email->from( $this->aauth->config_vars['email'], $this->aauth->config_vars['name']);
				$this->email->to($this->session->userdata('email'));
				$this->email->subject($email_subject);
				$this->email->message($email_content);
				$this->email->send();
			}
			else if ($action == 'confirmed')
			{
				$this->email->from( $this->aauth->config_vars['email'], $this->aauth->config_vars['name']);
				$this->email->subject($email_subject);
				$this->email->message($email_content);
				
				
				$this->load->model('User_model');
				$users = $this->User_model->get_all_users();
				foreach($users as $user)
				{
					$this->email->to($user['email']);
					$this->email->send();
				}
			}
			
			return;
		}
	}
	
	public function post_image()
	{
		if (!$this->aauth->is_admin())
		{
			redirect('404');
			return;
		}
		if ($_FILES['file']['name']) {
			if (!$_FILES['file']['error']) {
				$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
				$filename = random_string('alnum', 50) . '.' . $ext;
				$destination = 'assets/images/admin_uploads/' . $filename; //change this directory
				$location = $_FILES["file"]["tmp_name"];
				move_uploaded_file($location, $destination);
				echo trim(base_url(). 'assets/images/admin_uploads/' . $filename);//change this URL
			}
			else
			{
				echo  $message = 'Ooops!  Your upload triggered the following error:  '.$_FILES['file']['error'];
			}
		}
	}
	
	public function change_activation_status_machine($machine_id)
	{
		if (!$this->aauth->is_admin())
		{
			redirect('404');
			return;
		}
		$this->load->model('Machine_model');
		$result = $this->Machine_model->change_activation_status($machine_id);
		if ($result)
			echo '{"result": true}';
		else
			echo '{"result": false}';
	}
	public function change_activation_status_machine_group($machine_group_id)
	{
		if (!$this->aauth->is_admin())
		{
			redirect('404');
			return;
		}
		$this->load->model('Machinegroup_model');
		$result = $this->Machinegroup_model->change_activation_status($machine_group_id);
		if ($result)
			echo '{"result": true}';
		else
			echo '{"result": false}';
	}
	public function delete_machine_group($machine_group_id)
	{
		if (!$this->aauth->is_admin())
		{
			redirect('404');
			return;
		}
		$this->load->model('Machinegroup_model');
		$result = $this->Machinegroup_model->delete_machine_group($machine_group_id);
		if ($result)
			echo '{"result": true}';
		else
			echo '{"result": false}';
	}
	public function delete_machine($machine_id)
	{
		if (!$this->aauth->is_admin())
		{
			redirect('404');
			return;
		}
		$this->load->model('Machine_model');
		$result = $this->Machine_model->delete_machine($machine_id);
		if ($result)
			echo '{"result": true}';
		else
			echo '{"result": false}';
	}
	public function edit_machine($machine_id=0)
	{
		if (!$this->aauth->is_admin())
		{
			redirect('404');
			return;
		}
		$this->load->model('Machine_model');
		$machine = $this->Machine_model->get_machine($machine_id);
		if ($machine == null)
		{
			echo '{"result":false}';
			return;
		}
		
		$machine_name = $this->input->post('machine_name');
		$machine_group_id = $this->input->post('machine_group_id');
		$manufacturer = $this->input->post('manufacturer');
		$model = $this->input->post('model');
		$desc = $this->input->post('description');
		$need_supervision = ($this->input->post('need_supervision')=='yes')?true:false;
		if ($this->Machine_model->update_data($machine_id, $machine_name, $machine_group_id, $manufacturer, $model, $desc, $need_supervision))
		{
			echo '{"result":true}';
			return; 
		}
		else
		{
			echo '{"result":false}';
			return;
		}
	}
	
	public function edit_machine_group($machine_group_id=0)
	{
		if (!$this->aauth->is_admin())
		{
			redirect('404');
		}
		$this->load->model('Machinegroup_model');
		$machine_group = $this->Machinegroup_model->get_machine_group($machine_group_id);
		if ($machine_group == null)
		{
			redirect('404');
			return;
		}
		
		$this->load->view('partials/header');
		$this->load->view('partials/menu');
		if ($this->input->method() != 'post')
		{
			$this->load->view('admin/edit_machine_group', array('machine_group' => $machine_group,'action'=>'show'));
		}
		else
		{
			$name = $this->input->post('name');
			$description = $this->input->post('description');
			$need_supervision = ($this->input->post('need_supervision') != '')?true:false;
			$result = $this->Machinegroup_model->update_data($machine_group_id, $name, $description, $need_supervision);
			$machine_group = $this->Machinegroup_model->get_machine_group($machine_group_id);
			$this->load->view('admin/edit_machine_group', array('machine_group' => $machine_group,'action'=>'edit'));
		}
		$this->load->view('partials/footer');
	}
	
	// moderate groups
	public function groups()
	{
		if (!$this->aauth->is_admin())
		{
			redirect('404');
			return;
		}
		$this->load->model('Group_model');
		
		$groups = $this->Group_model->get_group_list();
		$this->load->view('partials/header');
		$this->load->view('partials/menu');
		
		$jdata['title'] = "Manage Groups";
		$jdata['message'] = "Insert, Update or Delete user groups in the system";
		$this->load->view('partials/jumbotron', $jdata);
		
		$this->load->view('admin/moderate_groups', array('groups' => $groups));
		$this->load->view('partials/footer');
	}
	
	// expect ajax post request
	public function get_group_list()
	{
		$filter_text = $this->input->post('group_detail');
		$this->load->model('Group_model');
		$groups = $this->Group_model->get_group_list($filter_text);
		
		echo json_encode($groups);
	}
	
	// expect ajax get request
	public function get_group_detail($group_id=0)
	{
		if ($group_id==0)
			return json_encode(array());
		
		$this->load->model('Group_model');
		$group = $this->Group_model->get_group($group_id);
		
		if ($group == null)
			echo json_encode(array());
		else
			echo json_encode($group);
	}
	
	// expect ajax post request
	public function update_group()
	{
		if (!$this->aauth->is_admin())
		{
			redirect('404');
		}
		$group_id = $this->input->post('group_id');
		$group_name = $this->input->post('group_name');
		$group_description = $this->input->post('group_description');
		$group_email_suffix = $this->input->post('group_email_suffix');
	
		$this->load->model('Group_model');
		$result = $this->Group_model->update_group($group_id, $group_name, $group_description, $group_email_suffix);
	
		if ($result)
		{
			echo '{"result":true}';
		}
		else
		{
			echo '{"result":false}';
		}
	}
	
	public function delete_group()
	{
		if (!$this->aauth->is_admin())
		{
			redirect('404');
		}
		$group_id = $this->input->post('group_id');
		
		
		$this->load->model('Group_model');
		$result = $this->Group_model->delete_group($group_id);
		
		if ($result)
		{
			echo '{"result":true}';
		}
		else
		{
			echo '{"result":false}';
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
	/**
	 * Send cancel email
	 * Sends cancel email to a specific email address
	 * @access admin
	 * @input email
	 *
	 */
	private function send_cancel_email($email, $data) {
		$this->email->from( $this->aauth->config_vars['email'], $this->aauth->config_vars['name']);
		$this->email->to($email);
		$this->email->subject("Supervision session has cancelled.");
		$data['name'] = $this->aauth->config_vars['name'];
		$email_content = $this->load->view("emails/cancel_email", $data, true);
		$this->email->message($email_content);
		$this->email->send();
	}
	private function send_modified_email($email, $data) {
		$this->email->from( $this->aauth->config_vars['email'], $this->aauth->config_vars['name']);
		$this->email->to($email);
		$this->email->subject("Supervision session has been modified.");
		$data['name'] = $this->aauth->config_vars['name'];
		$email_content = $this->load->view("emails/modified_email", $data, true);
		$this->email->message($email_content);
		$this->email->send();
	}
	/**
	 * Check interval input. Possible values are: Days, Weeks, Months.
	 */
	public function interval_check($str) {
		switch ($str) {
			case "Days":
				return true;
			case "Months":
				return true;
			case "Weeks":
				return true;
			default:
				$this->form_validation->set_message('interval', 'The %s field can not be the word ' . $str);
				return false;
		}
	}
}