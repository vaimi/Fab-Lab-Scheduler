<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test_model extends CI_Model {
	function __construct() {
		parent::__construct();
	}
	
	function test() {	
		$fields = $this->db->list_fields('aauth_users');

		foreach ($fields as $field)
		{
		   echo $field;
		}
	}
}