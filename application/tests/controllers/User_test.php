<?php

$PUBLIC_GROUP_ID = 2;
$UNIVERSITY_GROUP_ID = 3;
$ADMIN_GROUP_ID = 1;

class User_test extends TestCase
{
	//protected $CI;
	
    public function setUp()
    {
    	
    }
    
    
    public function test_register_success_outside_email()
    {
    	$output = $this->request('POST', ['User', 'registration'], 
    			['username' => 'testing_user',
    			'first_password' => 'testing_password',
    			'second_password'=> 'testing_password',
    			'first_name' => 'testing_firstname', 
    			'surname' => 'testing_firstname', 
    			'email' => 'some_random_email@email.com']);
    	
    	//check user created
    	$count_user_sql = 'select * 
			    			from aauth_users a
			    			inner join extended_users_information b on a.id=b.id
			    			where a.email=?';
    	$users = $this->CI->db->query($count_user_sql, array('some_random_email@email.com'))->result_array();
    	$this->assertEquals(count($users), 1);
    	
    	//check user belong to public group
    	$check_group_sql = 'select *
			    			from aauth_user_to_group a
			    			where a.user_id=? and a.group_id=?';
    	$group_record = $this->CI->db->query($check_group_sql, array($users[0]['id'], $PUBLIC_GROUP_ID))->result_array();
    	$this->assertEquals(count($group_record), 1);
    }
    
    public function test_register_success_university_email()
    {
    	$output = $this->request('POST', ['User', 'registration'],
    			['username' => 'testing_user',
    					'first_password' => 'testing_password',
    					'second_password'=> 'testing_password',
    					'first_name' => 'testing_firstname',
    					'surname' => 'testing_firstname',
    					'email' => 'some_random_email@student.oulu.fi']);
    	 
    	//check user created
    	$count_user_sql = 'select *
			    			from aauth_users a
			    			inner join extended_users_information b on a.id=b.id
			    			where a.email=?';
    	$users = $this->CI->db->query($count_user_sql, array('some_random_email@email.com'))->result_array();
    	$this->assertEquals(count($users), 1);
    	 
    	//check user belong to public group
    	$check_group_sql = 'select *
			    			from aauth_user_to_group a
			    			where a.user_id=? and a.group_id=?';
    	$group_record = $this->CI->db->query($check_group_sql, array($users[0]['id'], $UNIVERSITY_GROUP_ID))->result_array();
    	$this->assertEquals(count($group_record), 1);
    }
	
	public function test_login()
    {
        $output = $this->request('POST', ['User', 'login'], ['user_id' => '1']);
    }
}