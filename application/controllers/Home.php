<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Home extends MY_Controller
{
	public function __construct() {
		parent::__construct();
		$this->lang->load('fablab');
	}
	// this is the home page
	public function index() {
		$this->load->view('partials/header');
		$this->load->view('partials/menu');
		$jdata['title'] = $this->lang->line('fablab_homepage_title'); //$lang['']; //"Hello fabricator!";
		$jdata['message'] = $this->lang->line('fablab_homepage_content');;
		$this->load->view('partials/jumbotron_center', $jdata);
		$this->load->view('partials/footer');
	}
}