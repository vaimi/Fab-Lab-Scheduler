<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class User_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function get_extended_user_data($id) 
    {
    	$sql = "select * from extended_users_information where id=?";
    	$query = $this->db->query($sql, array($id));
    	return $query->row();
    }

    function insert_extended_user_data($extended_data)
    {
        $this->db->insert('extended_users_information', $extended_data);
        return ($this->db->affected_rows() != 1) ? false : true;
    }

    function get_email_prefixes()
    {
        $sql = "SELECT id, email_prefixes FROM aauth_groups";
        return $query = $this->db->query($sql);
    }
    
    function update_user($user_information)
    {
    	$basic_user_info = [];
    	$extended_user_info = [];
    	$basic_keys = "|email|pass|name|";
    	
    	foreach(array_keys($user_information) as $key)
    	{
    		if (strpos($basic_keys, "|".$key."|") !== false)
    		{
    			$basic_user_info[$key] = $user_information[$key];
    		}
    		else 
    		{
    			$extended_user_info[$key] = $user_information[$key];
    		}
    	}
    	
    	if (count($basic_user_info) > 0)
    	{
    		$this->db->update('aauth_users', $basic_user_info);
    	}
    	if (count($extended_user_info) > 0)
    	{
    		$this->db->update('extended_users_information', $extended_user_info);
    	}
    }

}