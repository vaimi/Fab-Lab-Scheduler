<?php $this->load->view('partials/header'); ?>
 
<?php $this->load->view('partials/menu'); ?>
<div class="container">
	<h4 class="modal-title">REGISTRATION SUCCESSFUL!</h4>
	<hr/>
	<p>Dear <?php echo $surname;?>,</p>
	<br/>
	<p>You have now successfully registered at University of Oulu's Fab Lab Scheduler. Please refer to the following details:</p>
	<br/>
	<p>Username: <?php echo $username;?></p>
	<p>Fullname: <?php echo $surname;?></p>
	<p>Email: <?php echo $email;?></p>
	<?php if ($phone_number != '') {?>
		<p>Phone number: <?php echo $phone_number;?></p>
	<?php } ?>
	<?php if ($company != '') {?>
		<p>Company: <?php echo $company;?></p>
	<?php } ?>
	<?php if ($address_street != '') {?>
		<p>Address: <?php echo $address_street;?></p>
	<?php } ?>
	<?php if ($address_postal_code != '') {?>
		<p>Postal code: <?php echo $address_postal_code;?></p>
	<?php } ?>
	<?php if ($student_number != '') {?>
		<p>Student number: <?php echo $student_number;?></p>
	<?php } ?>
	<br/>
	<p>You can visit the Support Desk at any time by going to http://xxx.yyy.com/</p>
	<br/>
	<p>Please do let us know if you have any questions.</p>
	<br/>
	<p>Thank You,</p>
</div>

<?php $this->load->view('partials/footer'); ?>