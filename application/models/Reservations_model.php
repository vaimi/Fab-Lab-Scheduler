<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Reservations_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    public function reservations_get_supervision_slots($start_time, $end_time) 
    {
        $sql = "SELECT * FROM Supervision WHERE StartTime <= STR_TO_DATE(?,'%Y-%m-%d %H:%i:%s') AND 
                      EndTime >= STR_TO_DATE(?,'%Y-%m-%d %H:%i:%s')";
        return $this->db->query($sql, array($end_time, $start_time));
    }

    public function reservations_get_machine_supervision_slots($start_time, $end_time, $user_id, $machine_id)
    {
        $this->db->select('group_id');
        $this->db->from('aauth_user_to_group');
        $this->db->where('user_id', $user_id);
        $result = $this->db->get();
        if ($result->num_rows() > 0)
        {
            $groups = array();
            foreach ($result->result() as $group) {
                $groups[] = $group->group_id;
            }
        }
        else
        {
            return [];
        }


        $this->db->select('Level');
        $this->db->from('UserLevel');
        $this->db->where('Aauth_usersID', $user_id);
        $this->db->where('MachineID', $machine_id);
        $result = $this->db->get();
        if ($result->num_rows() == 1)
        {
            $level = $result->row()->Level;
            $this->db->flush_cache();
            if ($level < 3)
            {
                $this->db->select('Aauth_usersID');
                $this->db->from('UserLevel');
                $this->db->where('MachineID', $machine_id);
                $this->db->where('Level >', 3);
            }
            else
            {
                $this->db->select('Aauth_usersID');
                $this->db->from('UserLevel');
                $this->db->where('MachineID', $machine_id);
                $this->db->where('Level >', 4);
            }
            $result = $this->db->get();
            if ($result->num_rows() > 0)
            {
                $supervisor_ids = array_map(function($o) { return $o->Aauth_usersID; }, $result->result());
                $this->db->flush_cache();
                $this->db->select("StartTime, EndTime");
                $this->db->from("Supervision");
                $this->db->where("FROM_UNIXTIME(" . $this->db->escape($end_time) . ") > StartTime");
                $this->db->where("FROM_UNIXTIME(" . $this->db->escape($start_time) . ") < EndTime");
                //$this->db->where("StartTime >", date('Y-m-d H:i:s', $end_time));
                //$this->db->where("EndTime >", date('Y-m-d H:i:s', $start_time));
                $this->db->where_in("Aauth_usersID", $supervisor_ids);
                $this->db->where_in("Aauth_groupsID", $groups);
                $this->db->order_by("StartTime", "asc");
                $result = $this->db->get();
                if ($result->num_rows() > 0)
                {
                    return $result->result();
                }
                else
                {
                    return [];
                }
            }
            else
            {
                return [];
            }

        }
        else
        {
            return [];
        }

    }

    public function reservations_get_reserved_slots($start_time, $end_time, $machine) 
    {
        $sql = "SELECT * FROM Reservation WHERE StartTime < STR_TO_DATE(?,'%Y-%m-%d %H:%i:%s') AND 
                      EndTime > STR_TO_DATE(?,'%Y-%m-%d %H:%i:%s') AND MachineID=? ORDER BY StartTime ASC";
        $response = $this->db->query($sql, array(date('Y-m-d H:i:s', $end_time), date('Y-m-d H:i:s', $start_time), $machine));
        return $response->result();
    }


    public function reservations_get_all_reserved_slots($start_time, $end_time) 
    {
        $sql = "SELECT MachineID, StartTime, EndTime, surname, email 
        		FROM Reservation, extended_users_information, aauth_users
        		WHERE EndTime >= STR_TO_DATE(?,'%Y-%m-%d %H:%i:%s') AND 
                StartTime <= STR_TO_DATE(?,'%Y-%m-%d %H:%i:%s') AND
        		Reservation.aauth_usersID = extended_users_information.id AND
        		Reservation.aauth_usersID = aauth_users.id";
        $response = $this->db->query($sql, array($start_time, $end_time));
        return $response->result();
    }

    public function reservations_get_reserved_slots_with_admin_info($start_time, $end_time)
    {
        $this->db->select("r.MachineID, r.ReservationID, r.StartTime, r.EndTime, e.surname, a.id, a.email, u.Level");
        $this->db->from("Reservation as r");
        $this->db->join("extended_users_information as e", "e.id = r.aauth_usersID");
        $this->db->join("aauth_users as a", "a.id = e.id");
        $this->db->join("UserLevel as u", "u.aauth_usersID = r.aauth_usersID AND r.MachineID = u.MachineID");
        $this->db->where("STR_TO_DATE(" . $this->db->escape($end_time) . ", '%Y-%m-%d %H:%i:%s') > StartTime ");
        $this->db->where("STR_TO_DATE(" . $this->db->escape($start_time) . ", '%Y-%m-%d %H:%i:%s') < EndTime ");
        $response = $this->db->get();
        return $response->result();
    }

    public function reservations_get_supervisor_levels($supervisor_id, $machine_id=false)
    {
        $this->db->select('*');
        $this->db->from('UserLevel');
        $this->db->where('Aauth_usersID', $supervisor_id);
        $this->db->where('Level > 3');
        if ($machine_id) {$this->db->where('MachineID', $machine_id);};
        return $this->db->get();
    }

    public function reservations_get_group_machines($group_id=false, $all=false)
    {
        
        $this->db->select('MachineID, Manufacturer, Model, NeedSupervision, active');
        $this->db->from('Machine');
        if (!$all)
        {
            $this->db->where('active', 1);
        }
        if ($group_id) {
            $this->db->where('MachineGroupID', $group_id);
        }
        return $this->db->get();
    }

    public function reservations_get_machines($all=false)
    {
        $this->db->select('MachineGroupID, Name, active');
        $this->db->from('MachineGroup');
        if (!$all)
        {
            $this->db->where('active', 1);
        }
        $groups = $this->db->get();
        $response = array();
        if ($groups->num_rows() > 0) {
            foreach($groups->result() as $group)
            {
                $group_array = array();
                $group_array["id"] = "cat_" . $group->MachineGroupID;
                if ($all and $group->active == 0)
                {
                    $group_array["title"] = "DEACT ". $group->Name;
                }
                else
                {
                    $group_array["title"] = $group->Name;
                }
                $group_array["children"] = array();
                $machines = $this->reservations_get_group_machines($group->MachineGroupID, $all);
                if ($machines->num_rows() > 0)
                {
                    foreach($machines->result() as $machine)
                    {
                        $child = array();
                        $child["id"] = "mac_" . $machine->MachineID;
                        if ($all and $machine->active == 0)
                        {
                            $child["title"] = "DEACT " . $machine->Manufacturer . " " . $machine->Model;
                        }
                        else
                        {
                            $child["title"] = $machine->Manufacturer . " " . $machine->Model;
                        }
                        $group_array['children'][] = $child;

                    }
                    $response[] = $group_array;
                }
            }
        }
        return $response;
    }

    public function reservations_get_active_machines_as_db_object() {
        $this->db->select('m.MachineID, m.Manufacturer, m.Model, m.NeedSupervision');
        $this->db->from('Machine as m');
        $this->db->join('MachineGroup as g', "m.MachineGroupID = g.MachineGroupID");
        $this->db->where('g.active', 1);
        $this->db->where('m.active', 1);
        return $this->db->get();
    }


    public function set_new_reservation($data) {
    	$this->db->insert('Reservation', $data);
        return $this->db->insert_id();
    }
    public function get_general_settings()
    {
    	$tmp = array();
    	$results = $this->db->get("Setting")->result_array();
    	foreach ($results as $result)
    	{
    		$tmp[$result["SettingKey"]] = $result["SettingValue"];
    	}
    	return $tmp;
    }
    public function get_user_level($user_id = false, $machine_id = false)
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
    	$r = $this->db->get()->row();
    	return isset($r) ? $r->Level : 1;
    }

    public function get_user_quota($user_id) {
        $settings = $this->get_general_settings();
        $limit = (int)$settings['default_tokens'];
        $reservation_count = $this->get_user_active_reservation_count($user_id);
        if ($reservation_count < $limit)
        {
            $this->db->select("quota");
            $this->db->from("extended_users_information");
            $this->db->where("id", $user_id);
            $result = $this->db->get();
            $quota = 0;
            if ($result->num_rows() == 1)
            {
                $quota = $result->row()->quota;
            }
            if ($limit > $quota)
            {
                $quota_new = $limit - $reservation_count;
                if ($quota_new > $quota) // If the user will benefit from the change.
                {
                    $this->db->update('extended_users_information', array('Quota' => $quota), array('id' => $user_id));
                }
            }
            return $quota;
        }
        $this->db->flush_cache();
        $this->db->select("quota");
        $this->db->from("extended_users_information");
        $this->db->where("id", $user_id);
        $result = $this->db->get();
        if ($result->num_rows() == 1)
        {
            return $result->row()->quota;
        }
        return 0;
    }

    public function is_reserved($start, $end, $machine)
    {
        $this->db->select("ReservationID");
        $this->db->from("Reservation");
        $this->db->where("MachineID", $machine);
        $this->db->where("StartTime <", $end);
        $this->db->where("EndTime >", $start);
        $result = $this->db->get();
        if ($result->num_rows() > 0)
        {
            return true;
        }
        return false;
    }

    public function get_active_reservations($user) {
        $this->db->select("r.ReservationID, r.StartTime, r.EndTime, m.Manufacturer, m.Model");
        $this->db->from("Reservation as r");
        $this->db->join("Machine as m", "m.MachineID = r.MachineID");
        $this->db->where("Aauth_usersID", $user);
        $this->db->where("EndTime >", date("Y-m-d H:i:s"));
        $result = $this->db->get();
        $response = array();
        if ($result->num_rows() > 0)
        {
            foreach($result->result() as $reservation)
            {
                $line = array();
                $line['id'] = $reservation->ReservationID;
                $line['machine'] = $reservation->Manufacturer . " " . $reservation->Model;
                $line['reserved'] = $reservation->StartTime . " " . $reservation->EndTime;
                $response[] = $line;
            }
        }
        return $response;
    }

    private function get_user_active_reservation_count($user_id)
    {
        $this->db->from("Reservation");
        $this->db->where("NOW() < EndTime");
        $this->db->where("aauth_usersID", $user_id);
        return $this->db->count_all_results();
    }

    public function reduce_quota($user_id) {
        $quota = $this->get_user_quota($user_id);
        $quota = $quota-1;
        if ($quota >= 0)
        {
            $this->db->update('extended_users_information', array('Quota' => $quota), array('id' => $user_id));
            return True;
        }
        return false;
    }

    public function get_previous_reservation_end($machine_id, $time) 
    {
        $this->db->select("EndTime");
        $this->db->from("Reservation");
        $this->db->where("MachineID", $machine_id);
        $this->db->where("EndTime <=", $time);
        $this->db->order_by("EndTime", "desc");
        $this->db->limit(1);
        $result = $this->db->get();
        if ($result->num_rows() > 0)
        {
            return strtotime($result->row()->EndTime);
        }
        $now = new DateTime();
        return $now->getTimestamp();
    }

    public function get_next_reservation_start($machine_id, $time) 
    {
        $this->db->select("StartTime");
        $this->db->from("Reservation");
        $this->db->where("MachineID", $machine_id);
        $this->db->where("StartTime >=", $time);
        $this->db->order_by("StartTime", "asc");
        $this->db->limit(1);
        $result = $this->db->get();
        if ($result->num_rows() > 0)
        {
            return strtotime($result->row()->StartTime);
        }
        return -1;
    }

    public function get_previous_supervision_end($machine_id, $time) 
    {
        $this->db->select("session.EndTime");
        $this->db->from("Supervision as session");
        $this->db->join("UserLevel as level", "level.aauth_usersID = session.aauth_usersID");
        $this->db->where("level.MachineID", $machine_id);
        $this->db->where("level.Level >", 3);
        $this->db->where("session.EndTime <=", $time);
        $this->db->order_by("session.EndTime", "desc");
        $this->db->limit(1);
        $result = $this->db->get();
        if ($result->num_rows() > 0)
        {
            return strtotime($result->row()->EndTime);
        }
        $now = new DateTime();
        $now->add(new DateInterval('P2M'));
        return $now->getTimestamp();
    }

    public function get_next_supervision_start($machine_id, $time) 
    {
        $this->db->select("session.StartTime");
        $this->db->from("Supervision as session");
        $this->db->join("UserLevel as level", "level.aauth_usersID = session.aauth_usersID");
        $this->db->where("level.MachineID", $machine_id);
        $this->db->where("level.Level >", 3);
        $this->db->where("session.StartTime >=", date("Y-m-d H:i:s", $time));
        $this->db->order_by("session.StartTime", "asc");
        $this->db->limit(1);
        $result = $this->db->get();
        if ($result->num_rows() > 0)
        {
            return strtotime($result->row()->StartTime);
        }
        $now = new DateTime();
        return $now->getTimestamp();
    }

    public function get_reservation_email_info($reservation)
    {
        $this->db->select("r.ReservationID, r.StartTime, r.EndTime, m.Manufacturer, m.Model, u.email");
        $this->db->from("Reservation as r");
        $this->db->join("Machine as m", "r.MachineID = m.MachineID");
        $this->db->join("aauth_users as u", "r.aauth_usersID = u.id");
        $this->db->where("ReservationID", $reservation);
        $result = $this->db->get();
        if ($result->num_rows() > 0)
        {
            return $result->row();
        }
        return null;
    }
    public function get_reservation_deadline()
    {
    	$this->db->select("SettingValue");
    	$this->db->from("Setting");
    	$this->db->where("SettingKey", "reservation_deadline");
    	$result = $this->db->get();
    	if ($result->num_rows() > 0)
    	{
    		return $result->row()->SettingValue;
    	}
    	return null;
    }
}
