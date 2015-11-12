<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MachineGroup_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    public function get_machine_group($machine_group_id)
    {
    	$this->db->select('*');
    	$this->db->from('MachineGroup');
    	$this->db->where('MachineGroupID', $machine_group_id);
    	$result = $this->db->get()->result_array();
    	if (count($result) == 0)
    		return null;
    	return $result[0];
    }
    
    public function change_activation_status($machine_group_id)
    {
    	$this->db->select('*');
    	$this->db->from('MachineGroup as mc');
    	$this->db->where('MachineGroupID', $machine_group_id);
    	$result = $this->db->get()->result_array();
    	if (count($result) == 0)
    		return false;
    	$mc = $result[0];
    	$sql = '';
    	if ($mc['active'] == 0)
    		$sql = 'update MachineGroup set active = 1 where MachineGroupID=?';
    	else
    		$sql = 'update MachineGroup set active = 0 where MachineGroupID=?';
    	$this->db->query($sql, array($machine_group_id));
    	return true;
    }
    public function delete_machine_group($machine_group_id)
    {
    	$this->db->select('*');
    	$this->db->from('MachineGroup as mc');
    	$this->db->where('MachineGroupID', $machine_group_id);
    	$result = $this->db->get()->result_array();
    	if (count($result) == 0)
    		return false;
    	
    	$sql = 'delete from MachineGroup where MachineGroupID=?';
    	$this->db->query($sql, array($machine_group_id));
    	$sql = 'delete from Machine where MachineGroupID=?';
    	$this->db->query($sql, array($machine_group_id));
    	return true;
    }
    
    public function update_data($id, $name, $description, $supervision_status)
    {
    	$this->db->select('*');
    	$this->db->from('MachineGroup as mc');
    	$this->db->where('MachineGroupID', $id);
    	$result = $this->db->get()->result_array();
    	if (count($result) == 0)
    		return false;
    	 
    	$sql = 'update MachineGroup 
    			set Name=?, Description=?, NeedSupervision=?
    			where MachineGroupID=?';
    	$this->db->query($sql, array($name, $description, $supervision_status, $id));
    	return true;
    }
}