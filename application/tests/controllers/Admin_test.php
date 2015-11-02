<?php
class Admin_test extends TestCase
{
    public function setUp()
    {
        $CI =& get_instance();
        $CI->load->library('aauth');
    }

    // access tests
    public function test_access_as_public()
    {
        $this->request('GET', ['Admin', 'moderate_users']);
        $this->assertRedirect(404);
    }

    public function test_access_as_user()
    {
        $this->request->setCallablePreConstructor(
            function ($CI) {
                // Get mock object
                $auth = $this->getDouble(
                    'aauth', ['is_loggedin' => TRUE]
                );
                // Inject mock object
                $CI->aauth = $auth;
            }
        );
        $this->request('GET', ['Admin', 'moderate_users']);
        $this->assertRedirect(404);
    }

    public function test_access_as_admin()
    {
        $this->request->setCallablePreConstructor(
            function () {
                // Get mock object
                $auth = $this->getDouble(
                    'aauth', ['is_admin' => TRUE]
                );
                // Inject mock object
                load_class_instance('aauth', $auth);
            }
        );
        $this->request('GET', ['Admin', 'moderate_users']);
        $this->assertResponseCode(200);
    }


}