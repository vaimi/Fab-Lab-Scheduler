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

}