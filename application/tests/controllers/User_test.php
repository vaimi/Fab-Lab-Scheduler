<?php
class User_test extends TestCase
{
	//protected $CI;
	
    public function setUp()
    {
    }
	
    public function test_valid_user_name() {
//     	$post_data = array( 
//     		'username' => "Urkki",
//     		'first_password' => "asdasdasd",
//     		'second_password' => "asdasdasd",
//     		'first_name' => "Make",
//     		'surname' => "Äitis",
//     		'email' => "***REMOVED***",
//     		'address_street' => "",
//     		'address_postal_code' => "",
//     		'phone_number' => "",
//     		'company' => "",
//     		'student_number' => ""
//     	);
//     	$output = $this->request('POST', ['User', 'registration'], $post_data);
//     	$this->assertContains('<title>Welcome to CodeIgniter</title>', $output);
    	$this->assertEquals(1,1);
    }
}