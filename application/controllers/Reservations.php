<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Reservations extends CI_Controller
{
	public function __construct() {
		parent::__construct();
		$this->load->model("Reservations_model");
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
		$data['machines'] = $machines->result();
		$this->load->view('reservations/reserve',$data);
		$this->load->view('partials/footer');
	}

	// TODO case of non-supervised machines
	// TODO min session lenght
	private function calculate_free_slots($start, $end) {
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
					$first->StartTime = strtotime('g:i a', $first->StartTime);
					$first->EndTime = strtotime('g:i a', $first->EndTime);
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
					}
					// Middle slots
					while(count($reserved) > 0)
					{
						$reservation = array_pop($reserved);
						$reservation->StartTime = strtotime('g:i a', $reservation->StartTime);
						$reservation->EndTime = strtotime('g:i a', $reservation->EndTime);
						$slot->machine = $supervisor_level->MachineID;
						if($previous_end < $reservation->StartTime) 
						{
							$slot = new stdClass();
							$slot->start = $previous_end;
							$slot->end = $reservation->StartTime;
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
	
	public function reserve_get_reserved_slots() {
		//TODO: Load these from db
    	$start = $this->input->get('start');
        $end = $this->input->get('end');
		//var_dump($this->calculate_free_slots($start, $end));
		$response = array 
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
		);
		$this->output->set_output(json_encode($response));
	}

	/*public function reserve_get_reserved_slots() {
		//TODO: Load these from db
		$response = array 
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
		);
		$this->output->set_output(json_encode($response));
	}*/
	
	public function reserve_get_machines() {
		//TODO: Load these from db
		$response = array 
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
		);
		$this->output->set_output(json_encode($response));
	}
}