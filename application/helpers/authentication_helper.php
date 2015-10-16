<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('get_user_info')) {
    function get_user_info()
    {
    	$CI =& get_instance();
    	
    	$user_info = $CI->aauth->get_user();
        if (!$user_info)
        	return false;
        
        $sql = "select * from extended_users_information where id=?";
        $query = $CI->db->query($sql, array($user_info->id));
        if ($query->num_rows() > 0)
        {
        	$row = $query->row();
        	
        	$user_info->surname = $row->surname;
        	$user_info->company = $row->company;
        	$user_info->address_street = $row->address_street;
        	$user_info->address_postal_code = $row->address_postal_code;
        	$user_info->phone_number = $row->phone_number;
        	$user_info->student_number = $row->student_number;
        	$user_info->quota = $row->quota;
        }
        else 
        {
        	$user_info->surname = '';
        	$user_info->company = '';
        	$user_info->address_street = '';
        	$user_info->address_postal_code = '';
        	$user_info->phone_number = '';
        	$user_info->student_number = '';
        	$user_info->quota = '';
        }
        
        return $user_info;
    }
}