<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Info_model extends CI_Model {
	
	function __construct() {
		parent::__construct();
	}
	
	public function get_machine_data($id = null)
	{
		$this->db->select('*');
		$this->db->from('Machine as mc');
		if (isset($id))
		{
			$this->db->where('MachineID', $id);
		}
		$result = $this->db->get()->result_array();
		if (count($result) == 0)
		{
			return null;
		}
		return $result;
	}
	
}