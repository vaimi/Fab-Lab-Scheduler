<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Message_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function get_conversations($user_id, $other_user_name = '')
    {
    	$sql = "select (case pms.sender_id when ? then pms.receiver_id else pms.sender_id end) other_user_id, max(pms.date_sent) max_date_sent
    			from aauth_pms pms
    			group by (case pms.sender_id when ? then pms.receiver_id else pms.sender_id end)
		    	order by max_date_sent desc";
    	
		/*$sql = "select (case pms.sender_id when ? then pms.receiver_id else pms.sender_id end) other_user_id, user.email, user.name, user_extended.surname, max(pms.date_sent) max_date_sent
		    	from aauth_pms pms
		    	inner join aauth_users user on (case pms.sender_id when ? then pms.receiver_id else pms.sender_id end) = user.id
		    	inner join extended_users_information user_extended on user.id = user_extended.id
				where user.name like '%".$other_user_name."%' or user_extended.surname like '%".$other_user_name."%'
		    	group by (case pms.sender_id when ? then pms.receiver_id else pms.sender_id end), user.email, user.name, user_extended.surname
		    	order by max_date_sent desc";*/
    	$result =  $this->db->query($sql, array($user_id, $user_id))->result_array();
    	$user_id_string = "";
    	foreach($result as $user)
    	{
    		$user_id_string = $user_id_string. $user['other_user_id'] . ',';
    	}
    	$user_id_string = $user_id_string.'-1';
    	
    	$sql = "select user.id other_user_id, user.email, user.name, user_extended.surname
    			from aauth_users user
    			inner join extended_users_information user_extended on user.id = user_extended.id
				where (user.name like '%".$other_user_name."%' or user_extended.surname like '%".$other_user_name."%')
						and user.id in (".$user_id_string.")";
    	
    	$result = $this->db->query($sql)->result_array();
    	return $result;
    }
    
    function get_total_unread($user_id)
    {
    	$sql = "select count(distinct sender_id) count_unread  from aauth_pms where receiver_id=? and date_read != null";
    	return $this->db->query($sql, array($user_id))->result()->count_unread;
    }
    
    function get_conversation($user_id, $other_user_id, $message_id_threshold=0)
    {
    	#update the conversation as read
    	$sql = "update aauth_pms
    			set date_read=now()
    			where ((sender_id=? and receiver_id=?) or (sender_id=? and receiver_id=?))
    			and date_read=null";
    	$this->db->query($sql, array($user_id, $other_user_id, $other_user_id, $user_id));
    	
    	#get conversation
    	$message_threshold = '';
    	if ($message_id_threshold>0)
    		$message_threshold = " and pms.id < ".$message_id_threshold;
    	
    	$sql = "select pms.* 
    			from aauth_pms pms
    			where (sender_id=? and receiver_id=?) or (sender_id=? and receiver_id=?)".$message_threshold."
    			order by date_sent asc
    			LIMIT 30";
    	
    	$result =  $this->db->query($sql, array($user_id, $other_user_id, $other_user_id, $user_id))->result_array();
    	return $result;
    }
    
}