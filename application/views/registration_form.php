<?php $this->load->view('partials/header'); ?>
 
<?php $this->load->view('partials/menu'); ?>

<div class="container">
	<h4 class="modal-title">REGISTRATION</h4>
	
	<?php foreach ($data as $item):?>
		<div style='color:red;'>- <?php echo $item;?></div>
	<?php endforeach;?>
	<form name="registration" method="post" action="<?php echo base_url();?>user/registration"
		onsubmit="return true;">
		<hr/>
		<table>
			<tr>
				<td width="150px"><label for="username">User name *</label></td>
				<td><input type="text" class="form-control" name="username" id="username"
					style="width: 200px;" value="" required="" autofocus="" placeholder="User name" /></td>
			</tr>
			<tr>
				<td><label for="password">Password *</label></td>
				<td><input type="password" class="form-control" name="password" id="password"
					style="width: 200px;" value="" required="" autofocus="" placeholder="Password" /></td>
			</tr>
			<tr>
				<td><label for="surname">Surname *</label></td>
				<td><input type="text" class="form-control" name="surname" id="surname"
					style="width: 200px;" value="" required="" autofocus="" placeholder="Surname" /></td>
			</tr>

			<tr>
				<td><label for="email">Email address *</label></td>
				<td><input type="email" class="form-control" name="email" id="email" style="width: 200px;"
					value="" required="" autofocus="" placeholder="Email address" /></td>
			</tr>
			<tr>
				<td><label for="company">Company</label></td>
				<td><input type="text" class="form-control" name="company" id="company"
					style="width: 200px;" value="" autofocus="" placeholder="Company" /></td>
			</tr>
			<tr>
				<td><label for="student_number">Student number</label></td>
				<td><input type="text" class="form-control" name="student_number" id="student_number"
					style="width: 200px;" value="" autofocus="" placeholder="Student number" /></td>
			</tr>
			<tr>
				<td><label for="social_number">Social number</label></td>
				<td><input type="text" class="form-control" name="social_number" id="social_number"
					style="width: 200px;" value="" autofocus="" placeholder="Social number" /></td>
			</tr>
			<tr>
				<td><input type="submit" class='btn btn-default' text="Register" /></td>
			</tr>
		</table>
	</form>

</div>

<?php $this->load->view('partials/footer'); ?>