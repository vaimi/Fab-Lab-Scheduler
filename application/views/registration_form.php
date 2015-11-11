<?php $this->load->view('partials/header'); ?>
 
<?php $this->load->view('partials/menu'); ?>

<div class="container">
	<h4 class="modal-title">REGISTRATION</h4>
	<hr/>
	<?php foreach ($errors as $item):?>
		<div style='color:red;'>- <?php echo $item;?></div>
	<?php endforeach;?>
	<form name="registration" method="post" action="<?php echo base_url();?>user/registration"
					onsubmit="return true;">
					<table>
						<tr>
							<td width="150px"><label for="username">User name *</label></td>
							<td><input type="text" class="form-control" name="username" id="username"
								style="width: 200px;" value="<?php echo $username;?>" required="" autofocus="" placeholder="User name" /></td>
						</tr>
						<tr>
							<td><label for="password">Password *</label></td>
							<td><input type="password" class="form-control" name="first_password" id="first_password"
								style="width: 200px;" value="" required="" autofocus="" placeholder="Password" /></td>
						</tr>
						<tr>
							<td><label for="password">Retype Password *</label></td>
							<td><input type="password" class="form-control" name="second_password" id="second_password"
								style="width: 200px;" value="" required="" autofocus="" placeholder="Password" /></td>
						</tr>
						<tr>
							<td><label for="surname">Full name *</label></td>
							<td><input type="text" class="form-control" name="surname" id="surname"
								style="width: 200px;" value="<?php echo $surname;?>" required="" autofocus="" placeholder="Full name" /></td>
						</tr>
			
						<tr>
							<td><label for="email">Email address *</label></td>
							<td><input type="email" class="form-control" name="email" id="email" style="width: 200px;"
								value="<?php echo $email;?>" required="" autofocus="" placeholder="Email address" /></td>
						</tr>
						<tr>
							<td><label for="phone_number">Phone number</label></td>
							<td><input type="text" class="form-control" name="phone_number" id="phone_number" style="width: 200px;"
								value="<?php echo $phone_number;?>" required="" autofocus="" placeholder="Phone number" /></td>
						</tr>
						<tr>
							<td><label for="company">Company</label></td>
							<td><input type="text" class="form-control" name="company" id="company"
								style="width: 200px;" value="<?php echo $company;?>" autofocus="" placeholder="Company" /></td>
						</tr>
						<tr>
							<td><label for="address_street">Address</label></td>
							<td><input type="text" class="form-control" name="address_street" id="address_street"
								style="width: 200px;" value="<?php echo $address_street;?>" autofocus="" placeholder="Address" /></td>
						</tr>
						<tr>
							<td><label for="address_postal_code">Postal code</label></td>
							<td><input type="text" class="form-control" name="address_postal_code" id="address_postal_code"
								style="width: 200px;" value="<?php echo $address_postal_code;?>" autofocus="" placeholder="Postal code" /></td>
						</tr>
						<tr>
							<td><label for="student_number">Student number</label></td>
							<td><input type="text" class="form-control" name="student_number" id="student_number"
								style="width: 200px;" value="<?php echo $student_number;?>" autofocus="" placeholder="Student number" /></td>
						</tr>
						<tr>
							<td><button type="submit" class='btn btn-primary' text="Register" >Register</button></td>
						</tr>
					</table>
				</form>

</div>

<?php $this->load->view('partials/footer'); ?>