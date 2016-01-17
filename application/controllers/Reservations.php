<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Reservations extends MY_Controller
{
	public function __construct() {
		parent::__construct();
		$this->load->model("Reservations_model");
		$this->load->library('form_validation');
	}
	
	public function index() {
		$this->basic_schedule();
	}

	private function no_public_access()
	{
		if (!$this->aauth->is_loggedin())
		{
			show_error('No public access. Log in first.', 401);
		}
	}
	
	public function active() {
		$this->no_public_access();
		$this->load->view('partials/header');
		$this->load->view('partials/menu');
		$jdata['title'] = "Active reservations";
		$jdata['message'] = "List of all your active reservations. Please note that you can't cancel already running session.";
		$this->load->view('partials/jumbotron', $jdata);
		$rdata = $this->Reservations_model->get_active_reservations($this->session->userdata('id'));
		$this->load->view('reservations/active', array("rdata"=>$rdata));
		$this->load->view('partials/footer');
	}

	public function basic_schedule() {
		$this->load->view('partials/header');
		$this->load->view('partials/menu');
		$jdata['title'] = "Basic schedule";
		$jdata['message'] = "Here you can see assigned supervisors and active reservations. If you want to do reservation go to " . anchor("reservations/reserve", "reserve");
		$this->load->view('partials/jumbotron', $jdata);
		$this->load->view('reservations/reserve_public');
		$this->load->view('partials/footer');
	}
	
	public function reserve() {
		$this->no_public_access();
		$this->load->view('partials/header');
		$this->load->view('partials/menu');

		//Load available machines
		$machines = $this->Reservations_model->reservations_get_machines_basic_info();
		//Load available quota.
		$data['quota'] = $this->Reservations_model->get_user_quota($this->session->userdata('id'));
		$data['machines'] = $machines->result();
		$data['is_admin'] = $this->aauth->is_admin();
		$deadline = $this->Reservations_model->get_reservation_deadline();
		//Get general settings for displaying them in the view
		$settings = $this->Reservations_model->get_general_settings();
		if ( !isset($settings['reservation_timespan']) || !isset($settings['interval']) )
		{
			//TODO Should Show Better error.
			show_error("Parameters are not found in db.");
		}
		$jdata['title'] = "Reserve a time";
		$jdata['message'] = "Remember that to be able to make a reservation for tomorrow, you have to reserve before " . $deadline .
		" today. Also you can make a reservation only " . $settings['reservation_timespan'] . " " . $settings['interval'] . " forward.";

		$this->load->view('partials/jumbotron', $jdata);
		$this->load->view('reservations/reserve',$data);
		/*if ( $data['is_admin'] ) //Admin can reserve time anytime or User is not admin and deadline is not exceeded.
		{
			$this->load->view('partials/jumbotron', $jdata);
			$this->load->view('reservations/reserve',$data);
		}
		elseif ( !$data['is_admin'] && !$this->is_reservation_deadline_exceeded() ) //Deadline is exceeded, return error.
		{
			
			$this->load->view('partials/jumbotron', $jdata);
			$this->load->view('reservations/reserve',$data);
		}
		/*else 
		{
			$d = array(
					"message" => "Deadline is exceeded. You have to reserve time before " . $deadline . " server time.",
					"title" => "Deadline is exceeded."
			);
			$this->load->view('partials/jumbotron_center', $d);
		}*/
		$this->load->view('partials/footer');
	}
	private function is_reservation_deadline_exceeded()
	{
		//Timezone must be correct in the server.
		$now = date ("H:i");
		$deadline = date( $this->Reservations_model->get_reservation_deadline() );
		return  $now > $deadline;
	}

	private function slot_merger($slot_array)
	{
		$slot_array = array_values($slot_array);
		foreach ($slot_array as $key => $value)
		{
			$slot_array[$key]->StartTime = strtotime($slot_array[$key]->StartTime);
			$slot_array[$key]->EndTime = strtotime($slot_array[$key]->EndTime);
		}
		$slot_count = count($slot_array);
		$response = array();
		// There is no need to combine anything if there is only one slot
		if ($slot_count > 1)
		{
			// Go through each slot
			foreach($slot_array as $key => $slot)
			{
				// offset
				$i = 1;
				// check that we don't go outside array
				if ($key+$i < $slot_count)
				{
					// check that slot isn't already combined
					if(isset($slot_array[$i + $key]))
					{
						// While first slot end is bigger than oncoming slot start i.e. they overlap
						while ($slot->EndTime >= $slot_array[$i + $key]->StartTime)
						{
							// If we are overlapping 	
							if ($slot->EndTime >= $slot_array[$i + $key]->StartTime)
							{
								// If we will gain bigger slot, modify "first" slot end, otherwise, just remove
								if($slot->EndTime <= $slot_array[$i + $key]->EndTime)
								{
									$slot->EndTime = $slot_array[$i + $key]->EndTime;
								}
								unset($slot_array[$i + $key]);
							}
							// We are not more overlapping, no need to go forward
							else
							{
								break;
							}
							// move offset forward and continue if we stay in the limits of array
							$i = $i+1;
							if ($key+$i >= $slot_count) break;
							if (!isset($slot_array[$i + $key]))
							{
								while(!isset($slot_array[$i + $key]) and $key+$i < $slot_count)
								{
									$i = $i+1;
								}
								if ($key+$i >= $slot_count) break;
							}
						}
					}
				}
				// if the slot isn't combined, add the "first" slot to response array.
				if (isset($slot_array[$key])) {
					$response[] = $slot;
				} 
			}
		}
		// Case of only one slot
		elseif ($slot_count == 1)
		{
			$response = $slot_array;
		}
		$response_hashmap = [];
		foreach ($response as $response_row)
		{
			$response_hashmap[$response_row->StartTime] = $response_row->EndTime;
		}

		return $response_hashmap;
	}

	private function calculate_free_slots($start, $end, $predefined_machine=false, $offset=false, $length_limit=false)
	{
		// Array to hold the slot objects
		$free_slots = array();

		$start = strtotime($start);
		$end = strtotime($end);

		$settings = $this->Reservations_model->get_general_settings();
		if(isset($settings['reservation_deadline']))
		{
			$startTime = new DateTime("@" . $start);
			$startTime->setTime(0, 0, 0);
			$endTime = new DateTime("@" . $end);
			$endTime->setTime(0, 0, 0);
			$now = new Datetime();
			$start_limit = new DateTime();
			//$start_limit->setTime(0, 0, 0);
			if ($startTime < $start_limit)
			{
				$limit_array = explode(":", $settings['reservation_deadline']);
				$start_limit->setTime($limit_array[0], $limit_array[1], 0);
				if ($now < $start_limit)
				{
					date_add($now,date_interval_create_from_date_string("1 days"));
				}
				else
				{
					date_add($now,date_interval_create_from_date_string("2 days"));
				}
				$now->setTime(0, 0, 0);
				$start = $now->getTimestamp();
			}
		}
		if ($startTime > $endTime)
		{
			return [];
		}


		$machines = $this->Reservations_model->reservations_get_active_machines_as_db_object();
		foreach($machines->result() as $machine)
		{
			// check whether machine is specified
			if($predefined_machine != false) {
				if ($predefined_machine != $machine->MachineID) continue;
			}
			// get supervision sessions
			$supervision_sessions = $this->Reservations_model->reservations_get_machine_supervision_slots($start, $end, $this->session->userdata('id'), $machine->MachineID);
			// merge overlapping sessions
			$supervision_hashmap = $this->slot_merger($supervision_sessions);
			// get reservation 
			$reservations = $this->Reservations_model->reservations_get_reserved_slots($start, $end, $machine->MachineID);
			// merge overlapping reservations
			$reservation_hashmap = $this->slot_merger($reservations);
			// go through sessions
			
			foreach ($supervision_hashmap as $s_start => $s_end) 
			{
				$endpoints = array();
				foreach ($reservation_hashmap as $r_start => $r_end) {
					if ($s_start < $r_end and $s_end > $r_start)
					{
						$endpoints[] = $r_start;
						$endpoints[] = $r_end;
					}
				}
				if (count($endpoints) > 0)
				{
					if ($endpoints[0] > $s_start)
					{
						array_unshift($endpoints, $s_start);
					}
					else
					{
						array_shift($endpoints);
					}
				}
				else
				{
					$endpoints[] = $s_start;
				}
				if (end($endpoints) < $s_end)
				{
					$endpoints[] = $s_end;
				}

				$endpoint_amount = count($endpoints);
				// If we for some reason end up with unpaired amount of ends, remove last
				if ($endpoint_amount % 2 != 0) $endpoint_amount -= 1;
				for($i=0; $i<$endpoint_amount; $i=$i+2)
				{
					$slot = new stdClass();
					if ($length_limit)
					{
						if ($length_limit > ($endpoints[$i+1] - $endpoints[$i]))
						{	

							$slot->disqualified = true;
						}
					}
					$slot->end = $endpoints[$i+1];
					$slot->machine = $machine->MachineID;
					$slot->start = $endpoints[$i];
					$slot->unsupervised = 0;
					$free_slots[] = $slot;
				}

				if (count($free_slots) > 0) {
					if (!$machine->NeedSupervision)
					{
						$setuptime = isset($settings['nightslot_pre_time']) ? 60 * $settings['nightslot_pre_time'] : 60 * 30; //TODO: what if not in db?
						$treshold = isset($settings['nightslot_threshold']) ? 60 * $settings['nightslot_threshold'] : 60 * 120; //TODO: what if not in db?
						$previous = end($free_slots);
						if (($previous->end - $previous->start) >= $setuptime)
						{
							$next_start = $this->Reservations_model->get_next_supervision_start($machine->MachineID, $previous->end);
							$length = $next_start - $previous->end;
							$length_flag = false;
							if ($length_limit)
							{
								if ($length_limit > $length)
								{	
									$length_flag = true;
								}
							}
							if ($length > $treshold and !$length_flag)
							{
								$old_end = $previous->end;
								$previous->end = $previous->end - $setuptime;
								if ($previous->end == $previous->start)
								{
									array_pop($free_slots);
								}
								$slot = new stdClass();
								$slot->end = $old_end;
								$slot->machine = $machine->MachineID;
								$slot->start = $previous->end;
								$slot->unsupervised = 1;
								$slot->next_start = $next_start; 
								$free_slots[] = $slot;
							}
						}
					}
				}

			}
		}
		return $free_slots;
	}

	 /**
     * Search slots
     * 
	 * Search free slots that fit to requirements. Only machine is mandatory, you can set also date and length.
	 * You have to be logged in to use this.
     * 
     * @uses post::input 'mid' Mandatory. Numeric machine identifier.
     * @uses post::input 'date' Day limiter. format (\d{4}/\d{2}/\d{2})
     * @uses post:input 'length' Numeric lenght limiter. Sessions shorter than this are ignored
     * 
     * @access public
     * @return json array in format [{"mid": machineid,"start":"dd.mm.yyyy hh:mm","end":"dd.mm.yyyy hh:mm","title":"Free x h y m"]
     *  
     */
	public function reserve_search_free_slots()
	{
		// You must be locked in to filter these properly
		$this->no_public_access();
		$this->form_validation->set_rules('mid', 'machine', 'required|numeric');
		$this->form_validation->set_rules('length', 'session lenght', 'numeric');
		$this->form_validation->set_rules('day', 'session day', 'exact_length[10]|regex_match[(\d{4}-\d{2}-\d{2})]');
	    if ($this->form_validation->run() == FALSE)
		{
			//echo errors.
			echo validation_errors();
			die();
		}
		// get variables
		$machine = $this->input->post('mid');
		$length = $this->input->post('length');
		$day = $this->input->post('day');

		// If day is not set, use current + db interval limit.
		$now = new DateTime();
		$now->setTime(0,0,0);
		$is_admin = $this->aauth->is_admin();
		
		if ($day == null)
		{
			$start = $now->format('Y-m-d H:i:s');
			if (!$is_admin)
			{
				//Get general settings
				$settings = $this->Reservations_model->get_general_settings();
				if ( !isset($settings['reservation_timespan']) || !isset($settings['interval']) )
				{
					//TODO Should Show Better error.
					show_error("Parameters are not found in db.");
				}
				$now->modify('+' . $settings['reservation_timespan'] . $settings['interval']);
			}
			else 
			{
				$now->add(new DateInterval('P2M')); // For admin
			}
			$end = $now->format('Y-m-d H:i:s');
		}
		// if day is set, set hours
		else
		{
			$start = $day . " 00:00:00";
			$end = $day . " 23:59:59";
			$result = $this->is_time_limit_exceeded($start, $end);
			//If user tries to search over the time limit.
			if($result['failed'] && !$is_admin) 
			{
				$err = array(
				 	"mid" => "0",
	        		"start" => "0000-00-00 00:00:00",
	        		"end" => "0000-00-00 00:00:00",
	        		"title" => "You cannot search over the limit."
	        	);
				$this->output->set_output(json_encode($err));
				return;
			}
		}

		// Give current time to slot finder
		//$limit = new DateTime();
		//$limit = $this->round_time($limit, 30);
        //$limit_u = $limit->getTimestamp();
        
		//$free_slots = $this->calculate_free_slots($start, $end, $machine, null, null, );

		// Check if length is set and filter results accordingly
		if ($length == null or $length == 0)
		{
			$free_slots = $this->calculate_free_slots($start, $end, $machine, null, false);
		}
		else
		{
			$length = $length * 3600;
			$free_slots = $this->calculate_free_slots($start, $end, $machine, null, $length);
		}

		// Form response
		$response = array();
		if (count($free_slots) > 0)
	    {
	        foreach ($free_slots as $free_slot) 
	        {
	        	if (isset($free_slot->disqualified)) {continue;};
	        	$start_time = DateTime::createFromFormat('U', $free_slot->start);
	        	$end_time = DateTime::createFromFormat('U', $free_slot->end);
	        	$free = $end_time->diff($start_time);
	        	$row = array();
        		$row["mid"] = $free_slot->machine;
        		$row["start"] = date('d.m.Y H:i', $free_slot->start);
        		$row["end"] = date('d.m.Y H:i', $free_slot->end);
        		if ($free_slot->unsupervised == 1)
        		{
        			$next_dt = DateTime::createFromFormat('U', $free_slot->next_start);
        			$start_dt = DateTime::createFromFormat('U', $free_slot->start);
        			$row["title"] = "Night slot: Potential length " . $this->format_interval($start_dt->diff($next_dt));
        			$row["next_start"] = date('d.m.Y H:i', $free_slot->next_start);
        		}
        		else
        		{
        			$row["title"] = "Free " . $this->format_interval($free);
        		}
        		$row["unsupervised"] = $free_slot->unsupervised;
        		$response[] = $row;
	        }
        }
        $this->output->set_output(json_encode($response));

	}
	
	private function is_time_limit_exceeded($start, $end) 
	{
		//Add limitation if user is not an admin.....
		$settings = $this->Reservations_model->get_general_settings();
		if ( !isset($settings['reservation_timespan']) || !isset($settings['interval']) )
		{
			//TODO Should Show Better error.
			show_error("Parameters are not found in db.");
		}
		$timespan = $settings['reservation_timespan'];
		$interval = $settings['interval'];
		
		// We don't allow search from history
		$now = new DateTime();
		//Round to nearest 30 minutes.
		$now = $this->round_time($now, 30);
		//if user is fetching over limit.
		$future_limit = clone $now;
		$future_limit->setTime(0, 0, 0);
		$future_limit->modify('+' . $timespan . $interval);
		$startTime = new DateTime($start);
		$startTime->setTime(0, 0, 0);
		$endTime = new DateTime($end);
		$endTime->setTime(0, 0, 0);
		if ( $future_limit <= $startTime || $future_limit <= $endTime )
		{
			return array( "failed" => true, "future_limit" => $future_limit );
		}
		else 
		{
			return array( "failed" => false, "future_limit" => $future_limit );
		}
	}
	public function cancel_reservation()
	{
		$this->form_validation->set_rules('id', 'Reservation id', 'required|is_natural_no_zero');
		if ($this->form_validation->run() == FALSE)
		{
			//echo errors.
			echo validation_errors();
			die();
		}
		$id = $this->input->post('id');
		if ( $this->aauth->is_admin() )
		{
			$new_state = RES_CANCEL_ADMIN; //admin cancellation
		}
		else {
			$new_state = RES_CANCEL_USER; //user cancellation	
			$now = new DateTime();
			$res_start_time = new DateTime($this->Reservations_model->get_reservation_by_id($id)->StartTime);
			if ($now >= $res_start_time)
			{
				echo json_encode(array("success" => false, "error" => "Cannot cancel past or ongoing reservation."));
				return;
			}
		}
		$success = $this->Reservations_model->set_reservation_state($id, $new_state); // FIXME cancel state???
		if ($success)
		{
			echo json_encode(array("success" => true));
		}
		else {
			echo json_encode(array("success" => false , "error" => "Cannot cancel reservation."));
		}
		return;
	}
	/**
     * Get calendar free slots
     * 
	 * Used to fetch calendar free slot events. 
     * 
     * @uses get::input 'start' start day
     * @uses get::input 'end' end day
     * 
     * @access public
     * @return json array in format [{"resourceId":"mac_" + machine id ,"start":"yyyy-mm-dd hh:mm:ss","end":"yyyy-mm-dd hh:mm:ss","title":"Free","reserved":0}]
     *  
     */
	public function reserve_get_free_slots() 
	{
		//$this->output->enable_profiler(TRUE);
		// You must be locked in to filter these properly
		$this->no_public_access();
		$this->form_validation->set_data($this->input->get());
		$this->form_validation->set_rules('start', 'start day', 'required|exact_length[10]|regex_match[(\d{4}-\d{2}-\d{2})]');
		$this->form_validation->set_rules('end', 'end day', 'required|exact_length[10]|regex_match[(\d{4}-\d{2}-\d{2})]');
	    if ($this->form_validation->run() == FALSE)
		{
			//echo errors.
			validation_errors();
			die();
		}
		$start = $this->input->get('start');
        $end = $this->input->get('end');
		
        $result = $this->is_time_limit_exceeded($start, $end);
        $is_admin = $this->aauth->is_admin();
		//check time limitation
		if ($result['failed'] && !$is_admin)
		{	
			$startTime = new DateTime($start);
			$startTime->setTime(0, 0, 0);
			$endTime = new DateTime($end);
			$endTime->setTime(0, 0, 0);
			$future_limit = $result['future_limit'];
			//Which time exceeded.
			if ($future_limit < $startTime)
			{
				return [];
			}

			if ($future_limit <= $endTime)
			{
				//Just reduce end time.
				$end = $future_limit->format("Y-m-d");
			}
		}
		
		/*// We don't allow search from history
		$now = new DateTime();
		//Round to nearest 30 minutes.
		$now = $this->round_time($now, 30);
        //Get unix timestamp.
        $now_u = $now->getTimestamp();*/
        //if ($now_u > strtotime($end)) return [];

        // Get unfiltered slots TODO BUG shows slots after endtime 
        $free_slots = $this->calculate_free_slots($start, $end, null ,1); 
        // Filter slots
	    //$free_slots = $this->filter_free_slots($free_slots);

	    // Make response
        $response = array();
	    if (count($free_slots) > 0)
	    {
	        foreach ($free_slots as $free_slot) 
	        {
	        	if (isset($free_slot->disqualified)) {continue;};
	        	$start_time = DateTime::createFromFormat('U', $free_slot->start);
	        	$end_time = DateTime::createFromFormat('U', $free_slot->end);
	        	$free = $end_time->diff($start_time);
	        	$row = array();
        		$row["resourceId"] = "mac_" . $free_slot->machine;
        		$row["start"] = date('Y-m-d H:i:s', $free_slot->start);
        		$row["end"] = date('Y-m-d H:i:s', $free_slot->end);
        		$row["reserved"] = 0;
        		$row["nightslot"] = $free_slot->unsupervised;
	        	if ($free_slot->unsupervised == 1)
        		{
        			$next_dt = DateTime::createFromFormat('U', $free_slot->next_start);
        			$start_dt = DateTime::createFromFormat('U', $free_slot->start);
        			$row["title"] = "Night slot: Potential length " . $this->format_interval($start_dt->diff($next_dt));
        			$row["next_start"] = date('Y-m-d H:i:s', $free_slot->next_start);
        		}
        		else
        		{
        			$startday = date("d.m.Y", $free_slot->start);
        			$endday = date("d.m.Y", $free_slot->end);
        			if ($startday == $endday){
        				$time = date("H:i", $free_slot->start) . " - " . date("H:i", $free_slot->end);
        			}
        			else
        			{
        				$time = date('d.m.Y H:i', $free_slot->start) . " - " . date('d.m.Y H:i', $free_slot->end);
        			}
        			$row["title"] = $time . " : Free ". $this->format_interval($free);
        		}
        		$response[] = $row;
	        }
        }
        $this->output->set_output(json_encode($response));
	}

	/**
     * Format interval
     * 
	 * Make nice looking title for slots 
     * 
     * @param DateTime $interval input time
     * 
     * @access private
     * @return string interval in human friendly format
     *  
     */
	private function format_interval($interval) 
	{
	    $result = "";
	    if ($interval->y) { $result .= $interval->format("%y y "); }
	    if ($interval->m) { $result .= $interval->format("%m m "); }
	    if ($interval->d) { $result .= $interval->format("%d d "); }
	    if ($interval->h) { $result .= $interval->format("%h h "); }
	    if ($interval->i) { $result .= $interval->format("%i m "); }
	    return $result;
	}

	/**
     * Get calendar supervision slots
     * 
	 * Used to fetch calendar supervision slots. Used in public calendar. 
     * 
     * @uses get::input 'start' start day
     * @uses get::input 'end' end day
     * 
     * @access public
     * @return json array in format [{"resourceId":"mac_" + machine id ,"start":"yyyy-mm-dd hh:mm:ss","end":"yyyy-mm-dd hh:mm:ss","title":supervisor name}]
     *  
     */
	public function reserve_get_supervision_slots() {
		// Validate input
		$this->form_validation->set_data($this->input->get());
		$this->form_validation->set_rules('start', 'start day', 'required|exact_length[10]|regex_match[(\d{4}-\d{2}-\d{2})]');
		$this->form_validation->set_rules('end', 'end day', 'required|exact_length[10]|regex_match[(\d{4}-\d{2}-\d{2})]');
	    if ($this->form_validation->run() == FALSE)
		{
			//echo errors.
			validation_errors();
			die();
		}
		$start = $this->input->get('start');
        $end = $this->input->get('end');
        // get slots
		$ssessions = $this->Reservations_model->reservations_get_supervision_slots($start, $end);

		// form response
		$response = array();
		foreach ($ssessions->result() as $ssession) 
		{
			$supervisor_levels = $this->Reservations_model->reservations_get_supervisor_levels($ssession->aauth_usersID);
			$supervisor = $this->aauth->get_user($ssession->aauth_usersID);
			foreach ($supervisor_levels->result() as $supervisor_level)
			{
				$row = array();
				$row["resourceId"] = "mac_" . $supervisor_level->MachineID;
				$row["start"] = $ssession->StartTime;
				$row["end"] = $ssession->EndTime;
				$row["title"] = $supervisor->name;
				if ($supervisor_level->Level == 4)
				{
					$row["className"] = "calendar-supervisor-4";
				}
				else
				{
					$row["className"] = "calendar-supervisor-5";
				}
				$response[] = $row;
			}
		}
  		$this->output->set_output(json_encode($response));
	}
	
	/**
     * Get calendar reserved slots
     * 
	 * Used to fetch calendar reserved slots. Used in public and reservation calendar. 
     * 
     * @uses get::input 'start' start day
     * @uses get::input 'end' end day
     * 
     * @access public
     * @return json array in format [{"resourceId":"mac_" + machine id ,"start":"yyyy-mm-dd hh:mm:ss","end":"yyyy-mm-dd hh:mm:ss","title":"Reserved", "reserved":"1"}]
     *  
     */
	public function reserve_get_reserved_slots() 
	{
    	$start = $this->input->get('start');
        $end = $this->input->get('end');
		//var_dump($this->calculate_free_slots($start, $end));
		$response = array();
		if($this->aauth->is_admin()) 
		{
			if ($this->session->userdata('reservations_states') == null)
			{
				$reservations = $this->Reservations_model->reservations_get_reserved_slots_with_admin_info($start, $end);
			}
			else
			{	
				$reservations = $this->Reservations_model->reservations_get_reserved_slots_with_admin_info($start, $end, $this->session->userdata('reservations_states'));
			}
			
			foreach ($reservations as $reservation) 
			{
				$row = array();
				$row["resourceId"] = "mac_" . $reservation->MachineID;
				$row["reservation_id"] = $reservation->ReservationID;
				$row["start"] = $reservation->StartTime;
				$row["end"] = $reservation->EndTime;
				$row["title"] = "Reserved: " . $reservation->first_name . " " . $reservation->surname;
				$row["user_id"] = $reservation->id;
				$row["first_name"] = $reservation->first_name;
				$row["surname"] = $reservation->surname;
				$row["email"] = $reservation->email;
				$row["is_admin"] = true;
				$row["user_level"] = $reservation->Level;
				$row["reserved"] = 1;
				switch($reservation->Level)
				{
					case 1:
						$row["className"] = "calendar-user-1";
						break;
					case 2:
						$row["className"] = "calendar-user-2";
						break;
					case 3:
						$row["className"] = "calendar-user-3";
						break;
					case 4:
						$row["className"] = "calendar-user-admin";
						break;
					case 5:
						$row["className"] = "calendar-user-admin";
						break;
					default:
						$row["className"] = "calendar-user-1";
						break;
				}
				if ($reservation->State == 4)
				{
					$row["className"] = "calendar-repair";
					$row["title"] = "Repair";
				}
				if (in_array($reservation->State, array(2,3,5))) {
					$row["className"] = $row["className"] . " calendar-cancelled";
					$row["title"] = "Cancelled: " . $reservation->surname;
					$row["reserved"] = 2;
				}

				$response[] = $row;
			}
		}
		else
		{
			$reservations = $this->Reservations_model->reservations_get_all_reserved_slots($start, $end);
			foreach ($reservations as $reservation) 
			{
				$row = array();
				$row["resourceId"] = "mac_" . $reservation->MachineID;
				$row["start"] = $reservation->StartTime;
				$row["end"] = $reservation->EndTime;
				$row["title"] = "Reserved";
				$row["reserved"] = 1;
				if ($this->session->userdata('id') == $reservation->id)
				{
					$row["title"] = "Owned";
					$row["user_id"] = $reservation->id;
					$row["reservation_id"] = $reservation->ReservationID;
					$row["first_name"] = $reservation->first_name;
					$row["surname"] = $reservation->surname;
					$row["email"] = $reservation->email;
				}
				if ($reservation->State == 4)
				{
					$row["className"] = "calendar-repair";
					$row["title"] = "Repair";
				}
				$response[] = $row;
			}
		}
		$this->output->set_output(json_encode($response));
	}

	/**
     * Get calendar machines
     * 
	 * Used to fetch calendar machines. Used in public and reservation calendar. 
     * 
     * @access public
   	 *
     * @return [{"groupText":group_identier_text,"id":machine id,"title":title text},...]  
     */
	public function reserve_get_machines() {
		$response = $this->Reservations_model->reservations_get_machines();
		$this->output->set_output(json_encode($response));
	}

	/**
     * Get quota
     * 
	 * Get user quota
	 *
	 * @uses input::post 'id' user id
     * 
     * @access public
     * @return float user quota
     *  
     */
	public function reserve_get_quota() {
		$this->no_public_access();
		echo $this->Reservations_model->get_user_quota($this->session->userdata('id'));
	}

	//TODO possibly add the qr and pass
	private function reserve_send_confirm_email($reservation_id) {
		$reservation = $this->Reservations_model->get_reservation_email_info($reservation_id);
		$this->email->from( $this->aauth->config_vars['email'], $this->aauth->config_vars['name']);
		$this->email->to($reservation->email);
		$this->email->subject("You succefully reserved Fab Lab session");
		$email_content = "Dear fabricator,<br>
		<br>
		You succesfully reserved new session to our Fab Lab. Remember that if you miss the end of your reservation, we can't assure that you can finish the work. Here is your reservation details: <br>
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
	/**
     * Reserve time
     * 
	 * Reserve free slot
	 *
	 * @uses input::post 'syear' start year
	 * @uses input::post 'smonth' start month
	 * @uses input::post 'sday' start day
	 * @uses input::post 'shour' start hour
	 * @uses input::post 'smin' start minute
	 * @uses input::post 'eyear' end year
	 * @uses input::post 'emonth' end month
	 * @uses input::post 'eday' end day
	 * @uses input::post 'ehour' end hour
	 * @uses input::post 'emin' end minute
     * 
     * @access public
     * @return json array{"success":1(true) or 0(false), "errors":[error string]} 
     *  
     */
	public function reserve_time() {
		
		$this->form_validation->set_rules('syear', 'start year', 'required|regex_match[(\d{4})]');
		$this->form_validation->set_rules('smonth', 'start month', 'required|regex_match[(\d{2})]');
		$this->form_validation->set_rules('sday', 'start day', 'required|regex_match[(\d{2})]');
		$this->form_validation->set_rules('shour', 'start hour', 'required|regex_match[(\d{2})]');
		$this->form_validation->set_rules('smin', 'start minute', 'required|regex_match[(\d{2})]');
		$this->form_validation->set_rules('eyear', 'end year', 'required|regex_match[(\d{4})]');
		$this->form_validation->set_rules('emonth', 'end month', 'required|regex_match[(\d{2})]');
		$this->form_validation->set_rules('eday', 'end day', 'required|regex_match[(\d{2})]');
		$this->form_validation->set_rules('ehour', 'end hour', 'required|regex_match[(\d{2})]');
		$this->form_validation->set_rules('emin', 'end minute', 'required|regex_match[(\d{2})]');
		
		$response = array();
		if ($this->form_validation->run() == FALSE)
		{
			//echo errors.
			$response['success'] = 0;
			$response['errors'] =  $this->form_validation->error_array();
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

			$start_time = new DateTime($start_year . "-" . $start_month . "-" . $start_day . " " . $start_hour . ":" . $start_min);
			$end_time = new DateTime($end_year . "-" . $end_month . "-" . $end_day . " " . $end_hour . ":" . $end_min);

			// TODO we should check the length
			// TODO check if reservation is done after deadline.
			//$start_modulo = $start_time->format('i') % 30;
			//$end_modulo = $end_time->format('i') % 30;
			if ($start_time >= $end_time) 
			{
				$response['success'] = 0;
				$response['errors'] =  array("Start time bigger than end time");
			}
			else
			{
				$start = $start_time->format('Y-m-d H:i:s');
				$end = $end_time->format('Y-m-d H:i:s');
				$start_limit = clone $start_time;
				$start_limit->setTime(0,0,0);
				$end_limit = clone $end_time;
				$end_limit->setTime(23,59,59);
				/*$now = new DateTime();
		        $now = $this->round_time($now, 30);
		        $now_u = $now->getTimestamp();*/
				$free_slots = $this->calculate_free_slots($start_limit->format('Y-m-d H:i:s'), $end_limit->format('Y-m-d H:i:s'), $m_id);
				//$free_slot = $this->filter_free_slots($free_slot);
				$is_overlapping = true;
				foreach ($free_slots as $free_slot) {
					if ($free_slot->unsupervised == 1)
					{
						if ($free_slot->start == $start_time->getTimestamp() and $free_slot->end == $end_time->getTimestamp())
						{
							$is_overlapping = false;
							$nightslot = true;
							break;
						}
					}
					else
					{
						if ($free_slot->start <= $start_time->getTimestamp() and $free_slot->end >= $end_time->getTimestamp())
						{
							$is_overlapping = false;
							$nightslot = false;
							break;
						}
					}
				}
				/*$diff = $start_time->diff($end_time);
				$hours = $diff->h;
				$hours = $hours + ($diff->format('%d')*24);
				$hours = $hours + ($diff->format('%i')/60);
				$cost = number_format($hours,2);*/

				if ($is_overlapping) 
				{
					$response['success'] = 0;
					$response['errors'] =  array("Overlapping with other reservation or too low level");
				}
				else if (!$this->Reservations_model->reduce_quota($this->session->userdata('id'))) //TODO this check should be done also on client side, this is just to double check it.
				{
					$response['success'] = 0;
					$response['errors'] =  array("No tokens left");
				}
				else
				{
					$data = array(
							'MachineID' => $m_id,
							'aauth_usersID' => $this->session->userdata('id'),
							'StartTime' => $start,
							'EndTime' => $end,
							'QRCode' => "dunno about this",
							'PassCode' => "dunno about this"
					);
					$reservation_id = $this->Reservations_model->set_new_reservation($data);
					
					$response['success'] = 1;
					$this->reserve_send_confirm_email($reservation_id);
				}
			}
		}
		$this->output->set_output(json_encode($response));
	}
	
	private function round_time(\DateTime $datetime, $precision = 30) {
		// 1) Set number of seconds to 0 (by rounding up to the nearest minute if necessary)
		$second = (int) $datetime->format("s");
		if ($second > 30) {
			// Jumps to the next minute
			$datetime->add(new \DateInterval("PT".(60-$second)."S"));
		} elseif ($second > 0) {
			// Back to 0 seconds on current minute
			$datetime->sub(new \DateInterval("PT".$second."S"));
		}
		// 2) Get minute
		$minute = (int) $datetime->format("i");
		// 3) Convert modulo $precision
		$minute = $minute % $precision;
		if ($minute > 0) {
			// 4) Count minutes to next $precision-multiple minuts
			$diff = $precision - $minute;
			// 5) Add the difference to the original date time
			$datetime->add(new \DateInterval("PT".$diff."M"));
		}
		return $datetime;
	}
	
}