<h3>User data</h3>
<div id="user_content">
	<div id="tab-content" class="tab-content">
		<div class="tab-pane active" id="basic">
			<form class="form-horizontal" id="basic_form">
				<div class="form-group">
					<label class="control-label col-xs-2" for="email_input">Email:</label>
					<div class="col-xs-8">
						<input type="email" class="form-control" id="email_input"
							placeholder="Email" readonly
							value="<?php echo $email;?>" >
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-2" for="email_input">Password:</label>
					<div class="col-xs-8">
						<input type="password" class="form-control" id="first_password"
							placeholder="Password">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-2" for="email_input">Repeat Password:</label>
					<div class="col-xs-8">
						<input type="password" class="form-control" id="second_password"
							placeholder="Repeat Password">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-2">Name:</label>
					<div class="col-xs-8">
						<input type="text" class="form-control" id="name_input"
							placeholder="First Name"
							value="<?php echo $name; ?>" >
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-2">Full name:</label>
					<div class="col-xs-8">
						<input type="text" class="form-control" id="surname_input"
							placeholder="Last Name"
							value="<?php echo $surname; ?>" >
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-2" for="phone_number_input">Phone:</label>
					<div class="col-xs-8">
						<input type="tel" class="form-control" id="phone_number_input"
							placeholder="Phone Number"
							value="<?php echo $phone_number;?>" >
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-2">Address:</label>
					<div class="col-xs-4">
						<input type="text" class="form-control" id="address_street_input"
							placeholder="Postal Address"
							value="<?php echo $address_street;?>" >
					</div>
					<div class="col-xs-4">
						<input type="text" class="form-control"
							id="address_postal_code_input" placeholder="Zip Code"
							value="<?php echo $address_postal_code;?>" >
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-2" for="student_number_input">Student
						ID:</label>
					<div class="col-xs-4">
						<input type="text" class="form-control" id="student_number_input"
							placeholder="Student ID"
							value="<?php echo $student_number; ?>" >
					</div>
				</div>
				<a href="javascript:update_user();" type="submit"
					class="btn btn-success"> <span
					class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
					Save
				</a>
			</form>
		</div>
	</div>
</div>
<script>
function update_user(user_id) {			
	var post_data = {
		'email': $('#email_input').val(),
		'name': $('#name_input').val(),
		'surname': $('#surname_input').val(),
		'phone_number': $('#phone_number_input').val(),
		'address_street': $('#address_street_input').val(),
		'address_postal_code': $('#address_postal_code_input').val(),
		'student_number': $('#student_number_input').val(),
		'first_password': $('#first_password').val(),
		'second_password': $('#second_password').val()
	};
	$('#user_data_form').fadeOut('fast');
	$.ajax({
		type: "POST",
		url: "<?php echo base_url('user/update_profile'); ?>",
		data: post_data,
		success: function(data) {
			// return success
			if (data.length > 0) {
				$('#user_data_form').html(data);
				$('#user_data_form').fadeIn('fast');
			}
		}
	});
}
</script>