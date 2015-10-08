<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Reservations extends CI_Controller
{
	public function __constructor() {
		parent::__constructor();
	}
	// this is the home page
	public function index() {
		$this->load->view('reservations/index');
	}
	
	public function active() {
		$this->load->view('reservations/active');
	}
	
	public function reserve() {
		$this->load->view('reservations/active');
	}
}