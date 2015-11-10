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
	public function create_new_machine($data)
	{
		$this->db->insert('Machine', $data);
	}
	public function create_new_machine_group($data)
	{
		$this->db->insert('MachineGroup', $data);
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
        $sql = "SELECT * FROM Supervision WHERE StartTime > STR_TO_DATE(?,'%Y-%m-%d %H:%i:%s') AND 
                      EndTime < STR_TO_DATE(?,'%Y-%m-%d %H:%i:%s')";
        return $this->db->query($sql, array($start_time, $end_time));
    }
    public function timetable_save_modified($slot)
    {
            $data = array(
            "SupervisionID" => (int)$slot->id,
            "StartTime" => date("Y-m-d H:i:s", strtotime($slot->start)),
            "EndTime" => date("Y-m-d H:i:s", strtotime($slot->end)),
            "Aauth_usersID" => (int)$slot->assigned
        );
        $this->db->replace('Supervision', $data);
    }
    
    public function timetable_save_new($slot)
    {
        $data = array(
            "StartTime" => date("Y-m-d H:i:s", strtotime($slot->start)),
            "EndTime" => date("Y-m-d H:i:s", strtotime($slot->end)),
            "Aauth_usersID" => (int)$slot->assigned
        );
        $this->db->insert('Supervision', $data);
    }
    
    public function timetable_save_deleted($slot)
    {
        $this->db->delete('Supervision', array('SupervisionId' => $slot->id)); 
    }
    
    public function timetable_fetch_by_id($id)
    {
        $this->db->select('*');
		$this->db->from('Supervision');
		$this->db->where('SupervisionID', $id);
		return $this->db->get();
    }
    public function schedule_copy($startDate, $endDate, $copyStartDate) {
    	$r = $this->timetable_get_supervision_slots($startDate, $endDate);
    	//Make DateTime objects.
    	$copyStartDateObj =  new DateTime($copyStartDate);
    	$startDateObj =  new DateTime($startDate);
    	//This offset is added to replicated schedules
    	$offset = date_diff($startDateObj, $copyStartDateObj);
    	$info = array();
    	foreach ($r->result() as $row) {
    		$tmp = array();
    		$new_start_time =  new DateTime($row->StartTime);
    		$new_end_time =  new DateTime($row->EndTime);
    		$new_start_time->add($offset);
    		$new_end_time->add($offset);
    		$slot = new StdClass();
    		$slot->start = $new_start_time->format('Y-m-d H:i:s');
    		$slot->end = $new_end_time->format('Y-m-d H:i:s');
    		$slot->assigned = $row->aauth_usersID;
    		$this->timetable_save_new($slot);
    		
    		$tmp['startTime_old'] = $row->StartTime;
    		$tmp['EndTime_old'] = $row->EndTime;
    		$tmp['startTime_new'] = $slot->start;
    		$tmp['EndTime_new'] = $slot->end;
    		$tmp['id'] = $slot->assigned;
    		array_push($info, $tmp);
    	}
    	return $info;
    }
}