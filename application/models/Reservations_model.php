<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Reservations_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    public function reservations_get_supervision_slots($start_time, $end_time) 
    {
        $sql = "SELECT * FROM Supervision WHERE StartTime > STR_TO_DATE(?,'%Y-%m-%d %H:%i:%s') AND 
                      EndTime < STR_TO_DATE(?,'%Y-%m-%d %H:%i:%s')";
        return $this->db->query($sql, array($start_time, $end_time));

    }

    public function reservations_get_reserved_slots($start_time, $end_time, $machine) 
    {
        $sql = "SELECT * FROM Reservation WHERE StartTime > STR_TO_DATE(?,'%Y-%m-%d %H:%i:%s') AND 
                      EndTime < STR_TO_DATE(?,'%Y-%m-%d %H:%i:%s') AND MachineID=? ORDER BY StartTime DESC";
        $response = $this->db->query($sql, array($start_time, $end_time, $machine));
        return $response->result();
    }

    public function reservations_get_supervisor_levels($supervisor_id)
    {
        $this->db->select('*');
        $this->db->from('UserLevel');
        $this->db->where('Aauth_usersID', $supervisor_id);
        $this->db->where('Level > 3');
        return $this->db->get();
    }
}