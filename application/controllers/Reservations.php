<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Reservations extends CI_Controller
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
		$this->load->model("Admin_model"); //TODO things relying from this should use functions from Reservation_model
		$machines = $this->Admin_model->get_machines();
		//Load available quota.
		$data['quota'] = $this->session->userdata('quota');
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
		$jdata['title'] = "Reserve";
		$jdata['message'] = "Rember to reserve time before " . $deadline .
		" today. Also you can reserve time " . $settings['reservation_timespan'] . " " . $settings['interval'] . " forward.";

		if ( $data['is_admin'] || $this->is_reservation_deadline_exceeded() ) //Admin can reserve time anytime or User is not admin and deadline is not exceeded.
		{
			$this->load->view('partials/jumbotron', $jdata);
			$this->load->view('reservations/reserve',$data);
		}
		else //Deadline is exceeded, return error.
		{
			$d = array(
					"message" => "Deadline is exceeded. You have to reserve time before " . $deadline . " server time.",
					"title" => "Deadline is exceeded."
			);
			$this->load->view('partials/jumbotron_center', $d);
		}
		$this->load->view('partials/footer');
	}
	private function is_reservation_deadline_exceeded()
	{
		//Timezone must be correct in the server.
		$now = date ("H:i");
		$deadline = date( $this->Reservations_model->get_reservation_deadline() );
		return  $now < $deadline;
	}
	 /**
     * Calculate free slots
     * 
     * Calculates free slots of specified time interval. The slots should not overlap per machine, but 
     * these still need to be filtrated to combine those slots user have required group/level.
     * 
     * @param string $start format Y-m-d H:i:s. lover limit of calculation.
     * @param string $end format Y-m-d H:i:s. upper limit of calculation. 
     * @param int $predefined_machine Optional. You can specify one machine ID you want to find slots. 
     * @param int $now Current time as unixtime. Used to filter out free sessions in history and limit therefore 
     * reservation of old slots.
     * 
     * @access private
     * @return free slots as array of objects. objects have following items:
     * 		machine == int machine id,
     * 		start == int start of the slot as unixtime,
     * 		end == int end of the slot as unixtime,
     * 		svLevel == int supervisor skill level (4,5 or 0 if unsupervised),
     * 		group == int supervision session target group.
     *  
     */
	private function calculate_free_slots($start, $end, $predefined_machine=false, $now) {
		// TODO FIXME Add reservation deadline offset to free slot
		// Array to hold the slot objects
		$free_slots = array();

		// Loop through machines
		$machines = $this->Reservations_model->reservations_get_active_machines_as_db_object();
		foreach($machines->result() as $machine)
		{
			// check whether machine is specified
			if($predefined_machine != false) {
				if ($predefined_machine != $machine->MachineID) continue;
			}

			// Array to hold session ends. Used to calculate extra non-supervised slots OUTSIDE supervision session
			$session_ends = array();

			// Loop through all supervision sessions
			$supervision_sessions = $this->Reservations_model->reservations_get_supervision_slots($start, $end);
			foreach($supervision_sessions->result() as $supervision_session)
			{
				// get unixtimes from the strings
				$supervision_session->date_StartTime = strtotime($supervision_session->StartTime);
				$supervision_session->date_EndTime = strtotime($supervision_session->EndTime);

				// make object for session ends so we can check this on unsupervised session calculation
				$ending = new stdClass();
				$ending->start = $supervision_session->date_StartTime;
				$ending->end = $supervision_session->date_EndTime;
				$session_ends[] = $ending;

				// Get supervisor machine level
				$supervisor_level = $this->Reservations_model->reservations_get_supervisor_levels($supervision_session->aauth_usersID, $machine->MachineID);
				$supervisor_level = $supervisor_level->row();

				// Pass if we don't have sufficient level and machine requires supervision
				if ($supervisor_level == null) {
					if ($machine->NeedSupervision)
					{
						continue;
					}
					$supervisor_level = new stdClass();
					$supervisor_level->Level = 0;
				}
				// Get reservations overlapping the supervision session
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
					// check whether the last breakpoint is before end of the supervision session
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
					// If we for some reason get count that is not %2, don't loop through last
					if ($break_amount % 2 != 0) $break_amount -= 1;
					
					for($i=0; $i<$break_amount; $i=$i+2)
					{
						$slot = new stdClass();
						$slot->end = $breakpoints[$i+1];
						$slot->machine = $machine->MachineID;
						$slot->svLevel = $supervisor_level->Level;
						$slot->group = $supervision_session->aauth_groupsID;
						$slot->start = $breakpoints[$i];
						$free_slots[] = $slot;
					}
				}
				else
				{
					// Schenario where there is no reservations
					$slot = new stdClass();
					$slot->end = $supervision_session->date_EndTime;
					$slot->machine = $machine->MachineID;
					$slot->svLevel = $supervisor_level->Level;
					$slot->group = $supervision_session->aauth_groupsID;
					$slot->start = $supervision_session->date_StartTime;
					$free_slots[] = $slot;
				}
			}
			// If unsupervised session
			if ($machine->NeedSupervision == 0)
			{
				// Check wether we have session ends.
				if (count($session_ends) > 0) 
				{
					// loop through overlapping sessions to remove unnneeded
					$mergearray = array();
				    foreach($session_ends as $ending_start) {
				        foreach ($session_ends as $ending_end) {
				            $merge_start = min($ending_start->start, $ending_end->start);
				            $merge_end = max($ending_start->end, $ending_end->end);

				            $merged = new stdClass();
				            $merged->start = $merge_start;
				            $merged->end = $merge_end;
				            $mergearray[] = $merged;
				        }
				    }

				    // Array to hold sessionends (unixtimes)
				    $session_ends = array();
				    foreach($mergearray as $key => $ending)
				    {	
				    	if ($key == 0)
				    	{
				    		$session_ends[] = $ending->end;
				    	}
				    	// otherwise, start normally
				    	else
				    	{
				    		$session_ends[] = $ending->start;
				    		$session_ends[] = $ending->end;
				    	}
				    }
				    // if the start is still bigger than first item

					// If the start of calculation is before current time, we use $now, otherwise we use $start
						if ($start < $now)
						{
							array_unshift($session_ends, $now);
						}
						else
						{
							array_unshift($session_ends, strtotime($start));
						}

					// If session end is before end of search
					if (end($session_ends) <= strtotime($end))
					{
						 $session_ends[] = strtotime($end);
					}
				}
				else
				// No breakpoints 
				{
					// use previous reservation as startpoint 
					$previous_reservation = $this->Reservations_model->get_previous_reservation_end($machine->MachineID, $start);
					$previous_session = $this->Reservations_model->get_previous_supervision_end($machine->MachineID, $start);
					if ($previous_reservation <= $previous_session and $previous_session <= strtotime($start)) $previous_reservation = $previous_session;
					if ($previous_reservation < $now) $previous_reservation = $now;
					$session_ends[] = $previous_reservation;
					$session_ends[] = $this->Reservations_model->get_next_reservation_start($machine->MachineID, $end);
				}

				$session_amount = count($session_ends);
				// If we for some reason end up with unpaired amount of sessionends, remove last
				if ($session_amount % 2 != 0) $session_amount -= 1;
				for($i=0; $i<$session_amount; $i=$i+2)
				{
					// Get reservations
					$reservations = $this->Reservations_model->reservations_get_reserved_slots(date('Y-m-d H:i:s', $session_ends[$i]), date('Y-m-d H:i:s', $session_ends[$i+1]), $machine->MachineID);
					// Make array for breakpoints
					$breakpoints = array(); 
					// Transfer to unixtime
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
						if ($breakpoints[0] > $session_ends[$i])
						{
							 array_unshift($breakpoints, $session_ends[$i]); 
						}
						else
						{
							array_shift($breakpoints);
						}
						// check whether the last fbreakpoint is before end of the supervision session
						if (end($breakpoints) <= $session_ends[$i+1])
						{
							 $breakpoints[] = $session_ends[$i+1]; 
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
						// Loop through free slots
						for($j=0; $j<$break_amount; $j=$j+2)
						{
							// Make slot object
							$slot = new stdClass();
							$slot->start = $this->Reservations_model->get_previous_reservation_end($machine->MachineID, date('Y-m-d H:i:s', $breakpoints[$j]));
							
							$next_session = $this->Reservations_model->get_next_supervision_start($machine->MachineID, date('Y-m-d H:i:s', $breakpoints[$j]));
							$next_reservation = $this->Reservations_model->get_next_reservation_start($machine->MachineID, date('Y-m-d H:i:s', $breakpoints[$j]));
							$next_start = $next_reservation;
							if ($next_start > $end) $next_start = $next_reservation; 
							$slot->end = $next_start;

							$slot->machine = $machine->MachineID;
							$slot->svLevel = "0";
							$slot->group = 3;
							$free_slots[] = $slot;
						}
					}
					else
					// Case of no breakpoints -> "full slot"
					{
						// find previous reservation
						$slot = new stdClass();
						if (date("H:i:s", $session_ends[$i]) == "00:00:00") {
							$slot->start = $this->Reservations_model->get_previous_reservation_end($machine->MachineID, date('Y-m-d H:i:s', $session_ends[$i]));
						}
						else
						{
							$slot->start = $session_ends[$i];
						}
						// find next reservation/session
						$next_session = $this->Reservations_model->get_next_supervision_start($machine->MachineID, date('Y-m-d H:i:s', $session_ends[$i+1]));
						$next_reservation = $this->Reservations_model->get_next_reservation_start($machine->MachineID, date('Y-m-d H:i:s', $session_ends[$i+1]));
						$next_start = $next_session < $next_reservation ? $next_session : $next_reservation;
						if ($next_start > $end) $next_start = $next_reservation; 
						$slot->end = $next_start;

						$slot->machine = $machine->MachineID;
						$slot->svLevel = "0";
						$slot->group = 3;
						$flag = false;

						// Check that we don't already have this slot in array
						foreach($free_slots as $free)
						{
							if ($free->machine == $slot->machine and $free->start == $slot->start and $free->end == $slot->end)
							{
								$flag = true;
								continue;
							}

						}
						if(!$flag) $free_slots[] = $slot;
					}
				}
			}
		}

		// check that we don't have slots starting in history
		$free_slots = array_values($free_slots);
		foreach($free_slots as $key => $slot)
		{
			if ($slot->end < $now)
			{
				unset($free_slots[$key]);
				continue;
			}
			if ($slot->start < $now)
			{
				$slot->start = $now;
			}
		}
		//Check that we don't have same start and end date. 
		//This error occured when endTime is near the now time. Also Ui doesnt work well.
		$free_slots = array_values($free_slots);
		foreach($free_slots as $key => $slot)
		{
			if ($slot->end <= $slot->start)
			{
				unset($free_slots[$key]);
			}
		}
		return $free_slots;	
	}

	/**
	 * Deletes free slots if user has not suitable level for the machine.
	 * It combines free slots if supervisors have slots in same time.
	 */
	private function filter_free_slots($free_slots, $length=false)
	{
		$tmp = $free_slots;
		$user_id = $this->session->userdata('id');

		$mid = -1;
		$user_machine_level = 1;
		//loop over free slots to delete unnecessary ones.
		foreach ($tmp as $key=>$free_slot)
		{
			if ($mid != $free_slot->machine ) //if mid has changed
			{
				$mid = $free_slot->machine;
				$user_machine_level = $this->Reservations_model->get_user_level($user_id, $mid);
			}
			
			//If user level is not found in db. 1-4 cant reserve 2-4 cant machine if 4 supervisor 3 can reserve
			//if 5 supervisor all can reserve
			
			//If supervisor lvl is 4, delete slot if user lvl is 2 or below 
			if($free_slot->svLevel == SUPERVISOR_CAN_SUPERVISE && $user_machine_level < USER_SKILLED) 
			{
				if(!$free_slot->svLevel == "0" && USER_SKILLED) continue;
				unset($tmp[$key]);
				continue;
			}

			if (!$this->aauth->is_member((int)$free_slot->group))
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
		$slot_count = count($tmp);
		$response = array();
		// There is no need to combine anything if there is only one slot
		if ($slot_count > 1)
		{
			// Go through each slot
			foreach($tmp as $key => $slot)
			{
				// offset
				$i = 1;
				// check that we don't go outside array
				if ($key+$i < $slot_count)
				{
					// check that slot isn't already combined
					if(isset($tmp[$i + $key]))
					{
						// While first slot end is bigger than oncoming slot start i.e. they overlap
						while ($slot->end >= $tmp[$i + $key]->start)
						{
							// we don't have to continue if it's wrong machine
							if($slot->machine != $tmp[$i + $key]->machine) 
							{
								// move offset forward and continue to next
								$i = $i+1;
								if ($key+$i >= $slot_count) break;
								if (!isset($tmp[$i + $key]))
								{
									while(!isset($tmp[$i + $key]) and $key+$i < $slot_count)
									{
										$i = $i+1;
									}
									if ($key+$i >= $slot_count) break;
								}
								continue;
							}
							// If we are overlapping 	
							if ($slot->end >= $tmp[$i + $key]->start)
							{
								// If we will gain bigger slot, modify "first" slot end, otherwise, just remove
								if($slot->end <= $tmp[$i + $key]->end)
								{
									$slot->end = $tmp[$i + $key]->end;
								}
								unset($tmp[$i + $key]);
							}
							// We are not more overlapping, no need to go forward
							else
							{
								break;
							}
							// move offset forward and continue if we stay in the limits of array
							$i = $i+1;
							if ($key+$i >= $slot_count) break;
							if (!isset($tmp[$i + $key]))
							{
								while(!isset($tmp[$i + $key]) and $key+$i < $slot_count)
								{
									$i = $i+1;
								}
								if ($key+$i >= $slot_count) break;
							}
						}
					}
				}
				// if the slot isn't combined, add the "first" slot to response array. Also, check length
				if (isset($tmp[$key])) {
					if ($length != false)
					{
						$diff = $slot->end - $slot->start;
						$slot_length = $diff / ( 60 * 60 );
						if ($slot_length >= $length)
						{
							$response[] = $slot;
						}
					}
					else
					{
						$response[] = $slot;
					}
				} 
			}
		}
		// Case of only one slot
		elseif ($slot_count == 1)
		{
			if ($length != false)
			{
				$diff = $tmp[0]->end - $tmp[0]->start;
				$slot_length = $diff / ( 60 * 60 );
				if ($slot_length >= $length)
				{
					$response[] = $tmp;
				}
			}
			else
			{
				$response = $tmp;
			}		
		}
		return $response;
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
		$limit = new DateTime();
		$limit = $this->round_time($limit, 30);
        $limit_u = $limit->getTimestamp();
        
		$free_slots = $this->calculate_free_slots($start, $end, $machine, $limit_u);

		// Check if length is set and filter results accordingly
		if ($length == null)
		{
			$free_slots = $this->filter_free_slots($free_slots);
		}
		else
		{
			$free_slots = $this->filter_free_slots($free_slots, $length);
		}

		// Form response
		$response = array();
		if (count($free_slots) > 0)
	    {
	        foreach ($free_slots as $free_slot) 
	        {
	        	$start_time = DateTime::createFromFormat('U', $free_slot->start);
	        	$end_time = DateTime::createFromFormat('U', $free_slot->end);
	        	$free = $end_time->diff($start_time);
	        	$response[] = array(
	        		"mid" => $free_slot->machine,
	        		"start" => date('d.m.Y H:i', $free_slot->start),
	        		"end" => date('d.m.Y H:i', $free_slot->end),
	        		"title" => "Free " . $this->format_interval($free)
	        	);
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
		if ( $result['failed'] && !$is_admin)
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
		
		// We don't allow search from history
		$now = new DateTime();
		//Round to nearest 30 minutes.
		$now = $this->round_time($now, 30);
        //Get unix timestamp.
        $now_u = $now->getTimestamp();
        if ($now_u > strtotime($end)) return [];

        // Get unfiltered slots TODO BUG shows slots after endtime 
        $free_slots = $this->calculate_free_slots($start, $end, null ,$now_u); //Takes a lot of time when searching for a month IF there is non-supervised machine
        // Filter slots
	    $free_slots = $this->filter_free_slots($free_slots); //Takes a lot of time when searching for a month IF there is non-supervised machine

	    // Make response
        $response = array();
	    if (count($free_slots) > 0)
	    {
	        foreach ($free_slots as $free_slot) 
	        {
	        	$start_time = DateTime::createFromFormat('U', $free_slot->start);
	        	$end_time = DateTime::createFromFormat('U', $free_slot->end);
	        	$free = $end_time->diff($start_time);
	        	$response[] = array(
	        		"resourceId" => "mac_" . $free_slot->machine,
	        		"start" => date('Y-m-d H:i:s', $free_slot->start),
	        		"end" => date('Y-m-d H:i:s', $free_slot->end),
	        		"title" => date('d.m.Y H:i', $free_slot->start) . " - " . date('d.m.Y H:i', $free_slot->end) . ": Free ". $this->format_interval($free),
	        		"reserved" => 0,
	        	);
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
				$response[] = array(
					"resourceId" => "mac_" . $supervisor_level->MachineID,
	        		"start" => $ssession->StartTime,
	        		"end" => $ssession->EndTime,
	        		"title" => $supervisor->name
				);
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
		$reservations = $this->Reservations_model->reservations_get_all_reserved_slots($start, $end);
		$response = array();
		foreach ($reservations as $reservation) 
		{
			if( $this->aauth->is_admin() )
			{
				$response[] = array(
						"resourceId" => "mac_" . $reservation->MachineID,
						"start" => $reservation->StartTime,
						"end" => $reservation->EndTime,
						"title" => "Reserved information",
						"surname" => $reservation->surname,
						"email" => $reservation->email,
						"is_admin" => true,
						"reserved" => 1
				);
			}
			else
			{
				$response[] = array(
						"resourceId" => "mac_" . $reservation->MachineID,
						"start" => $reservation->StartTime,
						"end" => $reservation->EndTime,
						"title" => "Reserved",
						"reserved" => 1
				);
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
     * @return json array in format {"id":"cat_" + category id,"title":group name,"children":[{"id":"mac_" + machine id,"title":manufacturer + model}]}]
     *  
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
				$now = new DateTime();
		        $now = $this->round_time($now, 30);
		        $now_u = $now->getTimestamp();
				$free_slot = $this->calculate_free_slots($start, $end, $m_id, $now_u);
				$free_slot = $this->filter_free_slots($free_slot);
				$is_overlapping = true;
				if (isset($free_slot[0]))
				{
					if ($free_slot[0]->start <= $start_time->getTimestamp() and $free_slot[0]->end >= $end_time->getTimestamp())
					{
						$is_overlapping = false;
					}
				}
				$diff = $start_time->diff($end_time);
				$hours = $diff->h;
				$hours = $hours + ($diff->format('%d')*24);
				$hours = $hours + ($diff->format('%i')/60);
				$cost = number_format($hours,2);

				if ($is_overlapping) 
				{
					$response['success'] = 0;
					$response['errors'] =  array("Overlapping with other reservation or too low level");
				}
				else if ($cost > $this->Reservations_model->get_user_quota($this->session->userdata('id'))) //TODO this check should be done also on client side, this is just to double check it.
				{
					$response['success'] = 0;
					$response['errors'] =  array("Required quota " . $cost);
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
					$this->Reservations_model->reduce_quota($this->session->userdata('id'), $cost);
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