<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class User_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->load->library("Aauth");
    }
    
    function update_verification($user_id)
    {
    	$ver_code = random_string('alnum', 16);
    	$data['verification_code'] = $ver_code;
    	$this->db->where('id', $user_id);
    	$this->db->update('aauth_users', $data);
    	return $ver_code;
    }
    
    function get_user_by_email($email)
    {
    	$sql = "select * 
    			from aauth_users a
    			inner join extended_users_information b on a.id=b.id
    			where a.email=?";
    	$users = $this->db->query($sql, array($email))->result_array();
    	if (count($users) == 0)
    		return null;
    	return $users[0];
    }
    
    function get_user_data($id)
    {
    	$sql = "select * from aauth_users where id=?";
    	$query = $this->db->query($sql, array($id));
    	$basic_info = $query->row();
    	
    	$sql = "select * from extended_users_information where id=?";
    	$query = $this->db->query($sql, array($id));
    	$extended_info = $query->row();
    	
    	$user_info = array(
    		'id' => $basic_info->id,
    			'email' => $basic_info->email,
    			'name' => $basic_info->name,
    			'surname' => $extended_info->surname,
    			'company' => $extended_info->company,
    			'address_street' => $extended_info->address_street,
    			'address_postal_code' => $extended_info->address_postal_code,
    			'phone_number' => $extended_info->phone_number,
    			'student_number' => $extended_info->student_number,
    			'quota' => $extended_info->quota
    	);
    	return $user_info;
    }
    
    function get_extended_user_data($id) 
    {
    	$sql = "select * from extended_users_information where id=?";
    	$query = $this->db->query($sql, array($id));
    	return $query->row();
    }
	function get_machine_levels_and_info($id) 
	{
		$sql = "select * from fablab_scheduler.UserLevel where aauth_usersID=?";
		$sql_machine = "select * from fablab_scheduler.Machine where MachineID=?;";
		$results = array();
		$query = $this->db->query($sql, array($id));
		foreach ($query->result() as $row)
		{
			$mid = $row->MachineID;
			$level = $row->Level;
			$query_machine = $this->db->query($sql_machine, array($mid));
			$tmp = array(
					'level' => $level,
					'mid' => $mid,
					'machine_name' => $query_machine->row()->MachineName,
					'manufacturer' => $query_machine->row()->Manufacturer,
					'model' => $query_machine->row()->Model,
					'description' => $query_machine->row()->Description
			);
 			array_push($results, $tmp);
		}
		return $results;
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
    
    function update_user($user_information, $user_id)
    {
    	$basic_user_info = [];
    	$extended_user_info = [];
    	$basic_keys = "|email|pass|name|";
    	
    	foreach(array_keys($user_information) as $key)
    	{
    		if (strpos($basic_keys, "|".$key."|") !== false)
    		{
    			if ($key == 'pass')
    				$user_information[$key] = 
    			$basic_user_info[$key] = $user_information[$key];
    			
    		}
    		else 
    		{
    			$extended_user_info[$key] = $user_information[$key];
    		}
    	}
    	
    	if (count($basic_user_info) > 0)
    	{
    		$this->db->where('id', $user_id);
    		$this->db->update('aauth_users', $basic_user_info);
    	}
    	if (count($extended_user_info) > 0)
    	{
    		$this->db->where('id', $user_id);
    		$this->db->update('extended_users_information', $extended_user_info);
    	}
    }
    
    function get_all_users()
    {
    	$sql = "select *
    			from aauth_users a
    			inner join extended_users_information b on a.id=b.id";
    	$users = $this->db->query($sql)->result_array();
    	return $users;
    }
	
    function get_reservations($user_id)
    {
    	$sql = "select r.MachineID, r.StartTime, r.EndTime, r.QRCode, r.PassCode, m.MachineName, m.Manufacturer, m.Model, m.Description
    			from Machine m, Reservation r
    			where r.aauth_usersID=? and r.MachineID = m.MachineID";
    	$results = $this->db->query($sql,$user_id)->result_array();
    	return $results;
    }

    function get_session_data($user_id) 
    {
        $this->db->select("surname");
        $this->db->from("extended_users_information");
        $this->db->where("id", $user_id);
        return $this->db->get()->row();
    }
}