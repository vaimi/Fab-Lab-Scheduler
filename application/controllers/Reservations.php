<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Reservations extends CI_Controller
{
	public function __construct() {
		parent::__construct();
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
		$this->load->view('reservations/reserve');
		$this->load->view('partials/footer');
	}
}