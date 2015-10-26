<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Admin_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
	
	public function get_autocomplete($search_data, $offset=0)
    {
		$this->db->select('main.id, main.email, main.name, extra.phone_number, extra.surname, extra.student_number');
		$this->db->from('aauth_users as main');
		$this->db->join('extended_users_information as extra', 'main.id = extra.id');
		$this->db->like('main.email', $search_data);
        $this->db->or_like('main.name', $search_data);
		$this->db->or_like('extra.phone_number', $search_data);
		$this->db->or_like('extra.surname', $search_data);
		$this->db->or_like('extra.student_number', $search_data);
		$this->db->limit(10);
		$this->db->offset($offset);
		return $this->db->get();
    }
}