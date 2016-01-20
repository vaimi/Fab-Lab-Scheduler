<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Contact extends MY_Controller
{
	public function __construct() {
		parent::__construct();
		$this->lang->load('fablab');
	}
	// this is the home page
	public function index() {
		
		$this->load->view('partials/header');
		$this->load->view('partials/menu');
		$jdata['title'] = $this->lang->line('fablab_contact_title');
		$jdata['message'] = $this->lang->line('fablab_contact_content');
		$this->load->view('partials/jumbotron', $jdata);
		$this->load->view('contact', array('page_body' => $this->lang->line('fablab_contact_body')));
		
		$this->load->view('partials/footer');
	}
}