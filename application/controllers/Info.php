<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Info extends MY_Controller
{
	public function __construct() {
		parent::__construct();
		
		$this->load->model('Info_model');
	}
	
	public function index() {
		$this->load->view('partials/header');
		$this->load->view('partials/menu');
		$jdata['title'] = "Want some info?";
		$jdata['message'] = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Sed posuere interdum sem. Quisque ligula eros ullamcorper quis, lacinia quis facilisis sed sapien. Mauris varius diam vitae arcu. Sed arcu lectus auctor vitae, consectetuer et venenatis eget velit. Sed augue orci, lacinia eu tincidunt et eleifend nec lacus. Donec ultricies nisl ut felis, suspendisse potenti. Lorem ipsum ligula ut hendrerit mollis, ipsum erat vehicula risus, eu suscipit sem libero nec erat. Aliquam erat volutpat. Sed congue augue vitae neque. Nulla consectetuer porttitor pede. Fusce purus morbi tortor magna condimentum vel, placerat id blandit sit amet tortor.";
		$this->load->view('partials/jumbotron_center', $jdata);
		$this->load->view('partials/footer');
	}
	
	public function floorplan() {
		$this->load->view('partials/header');
		$this->load->view('partials/menu');
		$jdata['title'] = "Want some info?";
		$jdata['message'] = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Sed posuere interdum sem. Quisque ligula eros ullamcorper quis, lacinia quis facilisis sed sapien. Mauris varius diam vitae arcu.";
		$this->load->view('partials/jumbotron', $jdata);
		$this->load->view('info/floorplan');
		$this->load->view('partials/footer');
	}
	
	public function machines($id = null) {
		$this->load->view('partials/header');
		$this->load->view('partials/menu');
		$jdata['title'] = "Want some info?";
		$jdata['message'] = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Sed posuere interdum sem. Quisque ligula eros ullamcorper quis, lacinia quis facilisis sed sapien. Mauris varius diam vitae arcu.";
		$this->load->view('partials/jumbotron', $jdata);
		if(isset($id))
		{
			$machine_data = $this->get_machine_info($id);
			$this->load->view('info/machineinfo', $machine_data);
		}
		else 
		{
		/*Load machine data from db.
		 * example array
		 *	$mdata = array (
		 *		array("MachineID"=>"1", "MachineName"=>"Example printer", "Description"=>"Very good and fast!"),
	  	 * 	);
		 */
		$mdata = $this->Info_model->get_machine_data();
		$this->load->view('info/machines', array("mdata"=>$mdata));
		}
		$this->load->view('partials/footer');
		
	}
	
	/**
	 * Fetches machine data to be shown on page
	 *
	 * @access	private
	 * @param	int
	 * @return	
	 */
	private function get_machine_info($id) {
		echo "tbd";
	}
}