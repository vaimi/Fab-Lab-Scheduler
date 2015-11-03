<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Error_404 extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->output->set_status_header('404');
        $this->load->view('page_not_found_404');
    }
}
?>