<?php $this->load->view('partials/header'); ?>
 
<?php $this->load->view('partials/menu'); ?>

<script src="<?php echo asset_url();?>js/validator.min.js"></script>

<div class="container">
	<h4 class="modal-title">REGISTRATION</h4>
	<hr/>
	<?php foreach ($errors as $item):?>
		<div style='color:red;'>- <?php echo $item;?></div>
	<?php endforeach;?>
	<form data-toggle="validator" name="registration" method="post" action="<?php echo base_url();?>user/registration" onsubmit="return true;">

		<div class="form-group">
			<label for="username">User name</label>
			<input type="text" class="form-control" name="username" id="username" value="<?php echo $username;?>" required placeholder="User name" />
		</div>	

		<div class="form-group">
			<label for="password">Password</label>
			<div class="form-group col-sm-6">
				<input type="password" class="form-control" name="first_password" id="first_password" value="" required placeholder="Password" />
				<span class="help-block">Minimum of 6 characters</span>
			</div>
			<div class="form-group col-sm-6">
				<input type="password" class="form-control" name="second_password" id="second_password" value="" required placeholder="Confirm password" />
				<div class="help-block with-errors"></div>
			</div>
		</div>
		<div class="form-group">
			<label for="first_name">Name</label>
			<div class="form-group col-sm-6">
				<input type="text" class="form-control" name="first_name" id="first_name" value="<?php echo $first_name;?>" required placeholder="First name" />
			</div>
			<div class="form-group col-sm-6">
				<input type="text" class="form-control" name="surname" id="surname" value="<?php echo $surname;?>" required placeholder="Last name" />
			</div>
		</div>
		<div class="form-group">
			<label for="email">Email address</label>
			<input type="email" class="form-control" name="email" id="email" value="<?php echo $email;?>" required placeholder="Email address" />
		</div>
		<div class="form-group">
			<label for="phone_number">Phone number</label>
			<input type="text" class="form-control" name="phone_number" id="phone_number" value="<?php echo $phone_number;?>" required placeholder="Phone number" />
		</div>

		<div class="form-group">
			<label for="company">Company</label>
			<input type="text" class="form-control" name="company" id="company" value="<?php echo $company;?>" placeholder="Company" />
		</div>

		<div class="form-group">
			<label for="address_street">Address</label>
			<div class="form-group col-sm-6">
				<input type="text" class="form-control" name="address_street" id="address_street" value="<?php echo $address_street;?>" placeholder="Address" />
			</div>
			<div class="form-group col-sm-6">
				<input type="text" class="form-control" name="address_postal_code" id="address_postal_code" value="<?php echo $address_postal_code;?>" placeholder="Postal code" />
			</div>
		</div>
		<div class="form-group">
			<label for="student_number">Student number</label>
			<input type="text" class="form-control" name="student_number" id="student_number" value="<?php echo $student_number;?>" placeholder="Student number" />
		</div>
		<button type="submit" class='btn btn-primary' text="Register" >Register</button>
	</form>

</div>

<?php $this->load->view('partials/footer'); ?>