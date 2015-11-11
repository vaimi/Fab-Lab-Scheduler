<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Machine_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    public function change_activation_status($machine_id)
    {
    	$this->db->select('*');
    	$this->db->from('Machine as mc');
    	$this->db->where('MachineID', $machine_id);
    	$result = $this->db->get()->result_array();
    	if (count($result) == 0)
    		return false;
    	$mc = $result[0];
    	$sql = '';
    	if ($mc['active'] == 0)
    		$sql = 'update Machine set active = 1 where MachineID=?';
    	else
    		$sql = 'update Machine set active = 0 where MachineID=?';
    	$this->db->query($sql, array($machine_id));
    	return true;
    }
    public function delete_machine($machine_id)
    {
    	$this->db->select('*');
    	$this->db->from('Machine as mc');
    	$this->db->where('MachineID', $machine_id);
    	$result = $this->db->get()->result_array();
    	if (count($result) == 0)
    		return false;
    	
    	$sql = 'delete from Machine where MachineID=?';
    	$this->db->query($sql, array($machine_id));
    	return true;
    }
    
    public function update_data($id, $name, $machine_group_id, $manufacturer, $model, $description, $supervision_status)
    {
    	$this->db->select('*');
    	$this->db->from('Machine as mc');
    	$this->db->where('MachineID', $id);
    	$result = $this->db->get()->result_array();
    	if (count($result) == 0)
    		return false;
    	 
    	$sql = 'update Machine 
    			set MachineName=?, MachineGroupID=?, Manufacturer=?,Model=?, 
    			Description=?, NeedSupervision=?
    			where MachineID=?';
    	$this->db->query($sql, array($name, $machine_group_id, $manufacturer, $model, $description, $supervision_status, $id));
    	return true;
    }
}