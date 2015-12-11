<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Group_model extends CI_Model {

	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
	}
	
	function get_group($group_id)
	{
		$this->db->select('*');
		$this->db->where("id", $group_id);
		$this->db->from('aauth_groups');
		
		$result = $this->db->get()->result_array();
		
		if (count($result) == 0)
			return null;
		else 
			return $result[0];
	}
	
	function get_group_list($search_text='')
	{
		$this->db->select('*');
		if (trim($search_text) != '')
		{
			$this->db->where("name like '%$search_text%' or definition like '%$search_text%' or email_prefixes like '%$search_text%'");
		}
		$this->db->from('aauth_groups');
		
		return $this->db->get()->result_array();
	}
	
	function insert_new_group($name, $description, $email_suffix)
	{
		$data = [];
		$data['name'] = $name;
		$data['definition'] = $description;
		$data['email_prefixes'] = $email_suffix;
		$this->db->insert('aauth_groups', $data);
		return ($this->db->affected_rows() != 1) ? false : true;
	}
	
	function update_group($id, $name, $description, $email_suffix)
	{
		$sql = 'update aauth_groups 
    			set name=?, definition=?, email_prefixes=?
    			where id=?';
    	$this->db->query($sql, array($name, $description, $email_suffix, $id));
    	return ($this->db->affected_rows() != 1) ? false : true;
	}
	
	function delete_group($id)
	{
		$sql = 'delete from aauth_perm_to_group where group_id = ?';
		$this->db->query($sql, array($id));
		$sql = 'delete from aauth_user_to_group where group_id = ?';
		$this->db->query($sql, array($id));
		$sql = 'delete from aauth_groups where id = ?';
		$this->db->query($sql, array($id));
		return ($this->db->affected_rows() != 1) ? false : true;
	}
	
}