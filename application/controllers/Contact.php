<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Contact extends MY_Controller
{
	public function __construct() {
		parent::__construct();
	}
	// this is the home page
	public function index() {
		
		$this->load->view('partials/header');
		$this->load->view('partials/menu');
		$jdata['title'] = "Some questions?";
		$jdata['message'] = "Something on your mind? Please contact one of the administrators.";
		$this->load->view('partials/jumbotron', $jdata);
		$this->load->view('contact');
		
		$this->load->view('partials/footer');
	}
}