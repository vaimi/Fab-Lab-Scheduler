<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Reservations extends CI_Controller
{
	public function __construct() {
		parent::__construct();
		$this->load->model("Reservations_model");
		$this->load->library('form_validation');
	}
	
	public function index() {
		$this->load->view('partials/header');
		$this->load->view('partials/menu');
		$jdata['title'] = "Need for reservation?";
		$jdata['message'] = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Sed posuere interdum sem. Quisque ligula eros ullamcorper quis, lacinia quis facilisis sed sapien. Mauris varius diam vitae arcu. Sed arcu lectus auctor vitae, consectetuer et venenatis eget velit. Sed augue orci, lacinia eu tincidunt et eleifend nec lacus. Donec ultricies nisl ut felis, suspendisse potenti. Lorem ipsum ligula ut hendrerit mollis, ipsum erat vehicula risus, eu suscipit sem libero nec erat. Aliquam erat volutpat. Sed congue augue vitae neque. Nulla consectetuer porttitor pede. Fusce purus morbi tortor magna condimentum vel, placerat id blandit sit amet tortor.";
		$this->load->view('partials/jumbotron_center', $jdata);
		$this->load->view('partials/footer');
	}
	
	public function active() {
		$this->load->view('partials/header');
		$this->load->view('partials/menu');
		$jdata['title'] = "Need for reservation?";
		$jdata['message'] = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Sed posuere interdum sem. Quisque ligula eros ullamcorper quis, lacinia quis facilisis sed sapien. Mauris varius diam vitae arcu.";
		$this->load->view('partials/jumbotron', $jdata);
		$rdata = array (
			# TODO: These need to be of course loaded from the db
			array("id"=>"1", "machine"=>"no_image.png","reserved"=>"123-34234-12321"),
			array("id"=>"2", "machine"=>"no_image.png","reserved"=>"123-34234-12321"),
			array("id"=>"3", "machine"=>"no_image.png","reserved"=>"123-34234-12321"),
			array("id"=>"4", "machine"=>"no_image.png","reserved"=>"123-34234-12321")
		);
		$this->load->view('reservations/active', array("rdata"=>$rdata));
		$this->load->view('partials/footer');
	}
	
	public function reserve() {
		$this->load->view('partials/header');
		$this->load->view('partials/menu');
		$jdata['title'] = "Need for reservation?";
		$jdata['message'] = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Sed posuere interdum sem. Quisque ligula eros ullamcorper quis, lacinia quis facilisis sed sapien. Mauris varius diam vitae arcu.";
		$this->load->view('partials/jumbotron', $jdata);
		//Load available machines
		$this->load->model("Admin_model"); //TODO things relying from this should use functions from Reservation_model
		$machines = $this->Admin_model->get_machines();
		//Load available quota.
		$data['quota'] = $this->session->userdata('quota');
		$data['machines'] = $machines->result();
		$this->load->view('reservations/reserve',$data);
		$this->load->view('partials/footer');
	}

	// TODO case of non-supervised machines
	// TODO min session lenght
	private function calculate_free_slots_old($start, $end) {
		// Empty array for slots
		$slot_array = array();
		// Get supervision session between the time constraint
		$supervision_sessions = $this->Reservations_model->reservations_get_supervision_slots($start, $end);
		// Loop through all supervision sessions
		foreach($supervision_sessions->result() as $supervision_session)
		{
			$supervision_session->date_StartTime = strtotime($supervision_session->StartTime);
			$supervision_session->date_EndTime = strtotime($supervision_session->EndTime);
			// Get supervisor machine levels
			$supervisor_levels = $this->Reservations_model->reservations_get_supervisor_levels($supervision_session->aauth_usersID);
			// Loop through machines supervisor has atleast level 4
			foreach($supervisor_levels->result() as $supervisor_level)
			{
				// Get reservations with right time & machine id
				$reserved = $this->Reservations_model->reservations_get_reserved_slots($supervision_session->StartTime, $supervision_session->EndTime, $supervisor_level->MachineID);
				// If there is already reserved sessions
				$previous_end = 0; // for remembering previous ending
				if(count($reserved) > 0)
				{
					// First slot
					$first = array_pop($reserved);
					$first->StartTime = strtotime($first->StartTime);
					$first->EndTime = strtotime($first->EndTime);
					if($supervision_session->date_StartTime > $first->StartTime)
					{
						if (count($reserved) > 0)
						{
							$previous_end = $first->EndTime;
						}
						else
						{
							$slot = new stdClass();
							$slot->start = $first->EndTime;
							$slot->end = $supervision_session->date_EndTime;
							$slot->machine = $supervisor_level->MachineID;
							if ($supervisor_level->Level == 5)
							{
								$slot->required = 0;
							}
							else
							{
								$slot->required = 3;
							}
							$slot_array[] = $slot;
						}
					}
					else
					{
						$slot = new stdClass();
						$slot->start = $supervision_session->date_StartTime;
						$slot->end = $first->StartTime;
						$slot->machine = $supervisor_level->MachineID;
						if ($supervisor_level->Level == 5)
						{
							$slot->required = 0;
						}
						else
						{
							$slot->required = 3;
						}
						$slot_array[] = $slot;
						$previous_end = $first->EndTime;
					}
					// Middle slots
					while(count($reserved) > 0)
					{
						$reservation = array_pop($reserved);
						$reservation->StartTime = strtotime($reservation->StartTime);
						$reservation->EndTime = strtotime($reservation->EndTime);
						$slot->machine = $supervisor_level->MachineID;
						if($previous_end < $reservation->StartTime) 
						{
							$slot = new stdClass();
							$slot->start = $previous_end;
							$slot->end = $reservation->StartTime;
							$slot->machine = $supervisor_level->MachineID;
							if ($supervisor_level->Level == 5)
							{
								$slot->required = 0;
							}
							else
							{
								$slot->required = 3;
							}
							$slot_array[] = $slot;
							$previous_end = $reservation->EndTime;
						}
					}
					// End slot
					if($previous_end < $supervision_session->date_EndTime) 
					{
						$slot = new stdClass();
						$slot->start = $previous_end;
						$slot->end = $supervision_session->date_EndTime;
						$slot->machine = $supervisor_level->MachineID;
						if ($supervisor_level->Level == 5)
						{
							$slot->required = 0;
						}
						else
						{
							$slot->required = 3;
						}
						$slot_array[] = $slot;
					}
				}
				else
				{
					$slot = new stdClass();
					$slot->start = $supervision_session->date_StartTime;
					$slot->end = $supervision_session->date_EndTime;
					$slot->machine = $supervisor_level->MachineID;
					if ($supervisor_level->Level == 5)
					{
						$slot->required = 0;
					}
					else
					{
						$slot->required = 3;
					}
					$slot_array[] = $slot;
				}
			}
		}
		return $slot_array;
		// return free slots as an array of objects. Note that these have to be still filtered based on user level.
	}

	// TODO case of non-supervised machines
	// TODO min session lenght
	private function calculate_free_slots($start, $end) {
		$free_slots = array();
		$machines = $this->Reservations_model->reservations_get_group_machines();
		foreach($machines->result() as $machine)
		{
			if ($machine->NeedSupervision)
			{
				// Machines that require always supervison
				$supervision_sessions = $this->Reservations_model->reservations_get_supervision_slots($start, $end);
				// Loop through all supervision sessions
				foreach($supervision_sessions->result() as $supervision_session)
				{
					$supervision_session->date_StartTime = strtotime($supervision_session->StartTime);
					$supervision_session->date_EndTime = strtotime($supervision_session->EndTime);
					// Get supervisor machine level
					$supervisor_level = $this->Reservations_model->reservations_get_supervisor_levels($supervision_session->aauth_usersID, $machine->MachineID);
					$supervisor_level = $supervisor_level->row();
					// Pass if we don't have sufficien level
					if ($supervisor_level == null) {
						continue;
					}
					// Get reservations
					$reservations = $this->Reservations_model->reservations_get_reserved_slots($supervision_session->StartTime, $supervision_session->EndTime, $machine->MachineID);
					// Make array for breakpoints
					$breakpoints = array(); 
					if (count($reservations) != 0) 
					{
						foreach($reservations as $reservation) 
						{
							$breakpoints[] = strtotime($reservation->StartTime);
							$breakpoints[] = strtotime($reservation->EndTime);
						}
					}
					// Cleanup breakpoints
					if (count($breakpoints) > 0) 
					{
						// check whether the first breakpoint is somewhere later in the supervision session
						if ($breakpoints[0] > $supervision_session->date_StartTime)
						{
							 array_unshift($breakpoints, $supervision_session->date_StartTime); 
						}
						else
						{
							array_shift($breakpoints);
						}
						// check whether the last fbreakpoint is before end of the supervision session
						if (end($breakpoints) <= $supervision_session->date_EndTime)
						{
							 $breakpoints[] = $supervision_session->date_EndTime; 
						}
						else
						{
							array_pop($breakpoints);
						}
						// remove breakpoints without break between
						$breakpoints_dub = array_diff_assoc($breakpoints, array_unique($breakpoints));
						$breakpoints = array_diff($breakpoints, $breakpoints_dub);
						$breakpoints = array_values($breakpoints);
						$break_amount = count($breakpoints);
						for($i=0; $i<=$break_amount-1; $i=$i+2)
						{
							$slot = new stdClass();
							$slot->start = $breakpoints[$i];
							$slot->end = $breakpoints[$i+1];
							$slot->machine = $machine->MachineID;
							$slot->svLevel = $supervisor_level->Level;
							$free_slots[] = $slot;
						}
					}
					else
					{
							$slot = new stdClass();
							$slot->start = $supervision_session->date_StartTime;
							$slot->end = $supervision_session->date_EndTime;
							$slot->machine = $machine->MachineID;
							$slot->svLevel = $supervisor_level->Level;
							$free_slots[] = $slot;
					}
				}

			} 
			else 
			{
				// Machines that can be used without supervison
				// lvl 3 and above can reserve these. 

			}
		}
		return $free_slots;	
	}
	/**
	 * Deletes free slots if user has not suitable level for the machine.
	 * It combines free slots if supervisors have slots in same time.
	 */
	private function filter_free_slots($free_slots)
	{
		$tmp = $free_slots;
		$user_id = $this->session->userdata('id');

		//loop over free slots to delete unnecessary ones.
		foreach ($tmp as $key=>$free_slot)
		{
			$mid = $free_slot->machine;
			$user_machine_level = $this->Reservations_model->get_user_level($user_id, $mid)->row();
			//If user level is not found in db. 1-4 cant reserve 2-4 cant machine if 4 supervisor 3 can reserve
			//if 5 supervisor all can reserve
			if ($user_machine_level == null) {
				//unset($tmp[$key]);
				$user_machine_level->Level = 1;
				//continue;
			}
			$user_machine_level = $user_machine_level->Level;
			//If supervisor lvl is 4, delete slot if user lvl is 2 or below 
			if($free_slot->svLevel == SUPERVISOR_CAN_SUPERVISE && $user_machine_level < USER_SKILLED) 
			{
				unset($tmp[$key]);
				continue;
			}
		}
		
		$results = array();
		//reindex slots just in case.
		$tmp = array_values($tmp);
		//Sort array by start time. Next while loop needs it.
		usort($tmp, function($a, $b)
		{
			return $a->start > $b->start;
		});
		//Combine free slots if necessary
		while(list($key, $free_slot) = each($tmp))
		{
			$end_slot = $free_slot;
			unset($tmp[$key]);
			while(list($key2, $possible_start_slot) = each($tmp))
			{
				//if slots are not from same machine
				if ($end_slot->machine != $possible_start_slot->machine) {
					continue;
				}
				
				$start_p = (int) $possible_start_slot->start;
				$end_p = (int) $possible_start_slot->end;
				$start = (int) $end_slot->start;
				$end = (int) $end_slot->end;
				//if slots are connected
				if ( $end >= $start_p && $start <= $start_p) {
					$end_slot = $possible_start_slot;
					unset($tmp[$key2]);
				}
			}
			$free_slot->end = $end_slot->end;
			//create a combined slot
			array_push($results, $free_slot);
			reset($tmp);
		}
		return $results;
	}
	public function reserve_get_free_slots() 
	{
		$start = $this->input->get('start');
        $end = $this->input->get('end');
        $free_slots = $this->calculate_free_slots($start, $end); //TODO these need to be still filtered (e.g. if there is no break with supervision session/multiple supervisors) + user level
	    $free_slots = $this->filter_free_slots($free_slots);
        $response = array();
	    if (count($free_slots) > 0)
	    {
	        foreach ($free_slots as $free_slot) 
	        {
	        	$response[] = array(
	        		"resourceId" => "mac_" . $free_slot->machine,
	        		"start" => date("Y-m-d H:i:s",$free_slot->start),
	        		"end" => date("Y-m-d H:i:s",$free_slot->end),
	        		"title" => "Free " . date("G \h i \m",$free_slot->end - $free_slot->start),
	        		"reserved" => 0
	        	);
	        }
        }
        $this->output->set_output(json_encode($response));

	}
	
	public function reserve_get_reserved_slots() 
	{
    	$start = $this->input->get('start');
        $end = $this->input->get('end');
		//var_dump($this->calculate_free_slots($start, $end));
		$reservations = $this->Reservations_model->reservations_get_all_reserved_slots($start, $end);
		$response = array();
		foreach ($reservations as $reservation) 
		{
			$response[] = array(
				"resourceId" => "mac_" . $reservation->MachineID,
        		"start" => $reservation->StartTime,
        		"end" => $reservation->EndTime,
        		"title" => "Reserved",
        		"reserved" => 1
			);
		}		
		/*$response = array 
		(
			array 
			(
				"id" => "cat_1_1",
				"resourceId" => "mac_1",
				"start" => "2015-10-26T09:00:00",
				"end" => "2015-10-26T10:00:00",
				//"url" => "confirm/1/1",
				"title" => "FREE"
			),
			array 
			(
				"id" => "cat_1_2",
				"resourceId" => "mac_1",
				"start" => "2015-10-26T10:00:00",
				"end" => "2015-10-26T12:00:00",
				"color" => "#f00",
				"title" => "RESERVED"
			),
			array 
			(
				"id" => "cat_1_3",
				"resourceId" => "mac_1",
				"start" => "2015-10-26T12:00:00",
				"end" => "2015-10-26T13:15:00",
				//"url" => "confirm/1/3",
				"title" => "FREE"
			)
		);*/
		$this->output->set_output(json_encode($response));
	}

	public function reserve_get_machines() {
		$response = $this->Reservations_model->reservations_get_machines();
		/*$response = array 
		(
			array 
			(
				"id" => "cat_1",
				"title" => "3d printers",
				"children" => array
				(	
					array
					(
						"id" => "mac_1",
						"title" => "example printer"
					),
					array
					(
						"id" => "mac_2",
						"title" => "example printer2"
					)
				)
			)
		);*/
		$this->output->set_output(json_encode($response));
	}
	public function reserve_time() {
		
		$this->form_validation->set_rules('syear', 'Start year', 'required|regex_match[(\d{4})]');
		$this->form_validation->set_rules('smonth', 'Start month', 'required|regex_match[(\d{2})]');
		$this->form_validation->set_rules('sday', 'Start day', 'required|regex_match[(\d{2})]');
		$this->form_validation->set_rules('shour', 'Start hour', 'required|regex_match[(\d{2})]');
		$this->form_validation->set_rules('smin', 'Start minute', 'required|regex_match[(\d{2})]');
		$this->form_validation->set_rules('eyear', 'End year', 'required|regex_match[(\d{4})]');
		$this->form_validation->set_rules('emonth', 'End month', 'required|regex_match[(\d{2})]');
		$this->form_validation->set_rules('eday', 'End day', 'required|regex_match[(\d{2})]');
		$this->form_validation->set_rules('ehour', 'End hour', 'required|regex_match[(\d{2})]');
		$this->form_validation->set_rules('emin', 'End minute', 'required|regex_match[(\d{2})]');
		
		if ($this->form_validation->run() == FALSE)
		{
			//echo errors.
			echo validation_errors();
			die();
		}
		else
		{
			$m_id = $this->input->post('mac_id');
			$m_id = str_replace("mac_", "", $m_id);

			$start_year = $this->input->post('syear');
			$start_month = $this->input->post('smonth');
			$start_day = $this->input->post('sday');
			$start_hour = $this->input->post('shour');
			$start_min = $this->input->post('smin');

			$end_year = $this->input->post('eyear');
			$end_month = $this->input->post('emonth');
			$end_day = $this->input->post('eday');
			$end_hour = $this->input->post('ehour');
			$end_min = $this->input->post('emin');

			//echo $m_id . " " .  $start_time . " " . $end_time . " " . $start_date ." ". $end_date;

			$start_time = new DateTime($start_year . "-" . $start_month . "-" . $start_day . " " . $start_hour . ":" . $start_min);
			$end_time = new DateTime($end_year . "-" . $end_month . "-" . $end_day . " " . $end_hour . ":" . $end_min);
			$start_modulo = $start_time->format('i') % 30;
			$end_modulo = $end_time->format('i') % 30;
			if ($start_time >= $end_time || $start_modulo != 0 || $end_modulo != 0) {
				echo "Error (time does not match or start time bigger than end time)";
				die();
			}
			$data = array(
					'MachineID' => $m_id,
					'aauth_usersID' => $this->session->userdata('id'),
					'StartTime' => $start_time->format('Y-m-d H:i:s'),
					'EndTime' => $end_time->format('Y-m-d H:i:s'),
					'QRCode' => "dunno about this",
					'PassCode' => "dunno about this"
			);
			// TODO Should check overlapping between other reservations
			// TODO Should check quota
			$this->Reservations_model->set_new_reservation($data);
			redirect("Reservations/reserve", "refresh");
		}

	}
}