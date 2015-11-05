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
	
	public function get_user_data($user_id) 
    {
		$this->db->select('main.id, main.email, main.name, 
			main.banned, extra.phone_number, extra.surname, 
			extra.student_number, extra.address_street, extra.address_postal_code, 
			extra.company, extra.quota');
		$this->db->from('aauth_users as main');
		$this->db->join('extended_users_information as extra', 'main.id = extra.id');
		$this->db->where('main.id', $user_id);
		return $this->db->get();
	}
	
	public function update_user_data($user_data) 
    {
		$data = array(
			'surname' => $user_data['surname'],
			'address_street' => $user_data['address_street'],
			'address_postal_code' => $user_data['address_postal_code'],
			'phone_number' => $user_data['phone_number'],
			'student_number' => $user_data['student_number']
		);
		$this->db->trans_start();
		$this->db->where('id', $user_data['user_id']);
		$this->db->update('extended_users_information', $data);
		$this->db->trans_complete();
		if ($this->db->affected_rows() == '1') {
			return true;
		} else {
			if ($this->db->trans_status() === FALSE) {
				return false;
			}
			return true;
		}
	}
	
	public function update_level_data($user_id, $machine_id, $level) 
    {
		$data = array(
			'MachineID' => $machine_id,
			'Aauth_usersID'  => $user_id,
			'Level'  => $level
		);

		$this->db->replace('UserLevel', $data);
	}
	
	public function get_machines($group_id = false) {
		$this->db->select('*');
		$this->db->from('Machine');
		if ($group_id != false)
		{
			$this->db->where('MachineGroupID', $group_id);
		}
		return $this->db->get();
	}
	
	public function get_machine_groups() 
    {
		$this->db->select('*');
		$this->db->from('MachineGroup');
		return $this->db->get();
	}
	
	public function get_levels($user_id = false, $machine_id = false) 
    {
		$this->db->select('*');
		$this->db->from('UserLevel');
		if ($user_id != false)
		{
			$this->db->where('aauth_usersID', $user_id);
		}
		if ($machine_id != false)
		{
			$this->db->where('MachineID', $machine_id);
		}
		return $this->db->get();
	}
	// TODO: This query needs checking.
	public function get_admins() 
    {
		$this->db->select('u.id, u.name, u.email');
		$this->db->distinct();
		$this->db->from('aauth_users as u');
		$this->db->join('aauth_user_to_group', 'u.id = aauth_user_to_group.user_id');	
		$this->db->join('aauth_groups as g', 'aauth_user_to_group.group_id = g.id');
		$this->db->like('g.name', 'Admin');
		return $this->db->get();
	}
	public function set_user_quota($user_id, $amount) 
    {
		$this->db->update('extended_users_information', array('Quota' => $amount), array('id' => $user_id));
		return True;
	}
    
    public function timetable_get_supervision_slots($start_time, $end_time) 
    {
        $sql = "SELECT * FROM Supervision WHERE StartTime > STR_TO_DATE(?,'%Y-%m-%d') AND 
                      EndTime < STR_TO_DATE(?,'%Y-%m-%d')";
        return $this->db->query($sql, array($start_time, $end_time));
    }
}