<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Info extends CI_Controller
{
	public function __constructor() {
		parent::__constructor();
	}
	// this is the home page
	public function index() {
		$this->load->view('info/index');
	}
	
	public function floorplan() {
		$this->load->view('info/floorplan');
	}
	
	public function machines() {
		$this->load->view('info/machines');
	}
}