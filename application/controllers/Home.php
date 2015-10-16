<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Home extends CI_Controller
{
	public function __constructor() {
		parent::__constructor();
	}
	// this is the home page
	public function index() {
		$this->load->view('partials/header');
		$this->load->view('partials/menu');
		$jdata['title'] = "Hello fabricator!";
		$jdata['message'] = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Sed posuere interdum sem. Quisque ligula eros ullamcorper quis, lacinia quis facilisis sed sapien. Mauris varius diam vitae arcu. Sed arcu lectus auctor vitae, consectetuer et venenatis eget velit. Sed augue orci, lacinia eu tincidunt et eleifend nec lacus. Donec ultricies nisl ut felis, suspendisse potenti. Lorem ipsum ligula ut hendrerit mollis, ipsum erat vehicula risus, eu suscipit sem libero nec erat. Aliquam erat volutpat. Sed congue augue vitae neque. Nulla consectetuer porttitor pede. Fusce purus morbi tortor magna condimentum vel, placerat id blandit sit amet tortor.";
		$this->load->view('partials/jumbotron_center', $jdata);
		$this->load->view('partials/footer');
	}
}