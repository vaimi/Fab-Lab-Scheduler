<?php $this->load->view('partials/header');
$this->load->view('partials/menu');
$jdata['title'] = "404 Not found";
$jdata['message'] = "Something went wrong, we were unable to find page you reguested. Please send email to our administrator at xx@yy.zz if you don't expect to see this page";
$this->load->view('partials/jumbotron_center', $jdata);
$this->load->view('partials/footer'); ?>

