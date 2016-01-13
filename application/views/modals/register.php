<script src="<?php echo asset_url();?>js/validator.min.js"></script>

<div id="registerModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">REGISTRATION</h4>
			</div>
			<form name="registration" id="registerform" method="post" action="<?php echo base_url();?>user/registration" onsubmit="return true;">
			<div class="modal-body">
				<div class="form-group required has-feedback">
					<label class="control-label" for="username">User name</label>
					<input type="text" data-minlength="5" data-maxlength="100" class="form-control" name="username" id="username" required placeholder="User name" />
					<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
					<span class="help-block">Length between 5 and 100 characters.</span>
				</div>	
				<div class="form-group required has-feedback">
					<label class="control-label" for="email">Email address</label>
					<input type="email" class="form-control" name="email" id="email" required placeholder="Email address" />
					<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
				</div>
				<div class="form-group required has-feedback">
					<label class="control-label">Password</label>
					<div class="row">
						<div class="col-sm-6">
							<input type="password" data-minlength="5" data-maxlength="100" class="form-control" name="first_password" id="first_password" value="" required placeholder="Password" />
							<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
							<span class="help-block">Length between 5 and 20 characters.</span>
						</div>
						<div class="col-sm-6">
							<input type="password" class="form-control" name="second_password" id="second_password" data-match="#first_password" data-match-error="Whoops, these aren't the same" value="" required placeholder="Confirm password" />
							<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
							<div class="help-block with-errors"></div>
						</div>
					</div>
				</div>
				<div class="form-group required has-feedback">
					<label class="control-label">Name</label>
					<div class="row">
						<div class="col-sm-6">
							<input type="text" class="form-control" name="first_name" id="first_name" placeholder="First name" required/>
							<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
						</div>
						<div class="col-sm-6">
							<input type="text" class="form-control" name="surname" id="surname" placeholder="Last name" required/>
							<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
						</div>
					</div>
				</div>
				<div class="form-group required has-feedback">
					<label class="control-label" for="phone_number">Phone number</label>
					<input type="tel" class="form-control" name="phone_number" id="phone_number" required placeholder="Phone number" />
					<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
				</div>
				<div class="form-group has-feedback">
					<label class="control-label" for="address_street">Address</label>
					<div class="row">
						<div class="col-sm-6">
							<input type="text" class="form-control" name="address_street" id="address_street" placeholder="Address" />
							<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
						</div>
						<div class="col-sm-6">
							<input type="text" class="form-control" name="address_postal_code" id="address_postal_code" placeholder="Postal code" />
							<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
						</div>
					</div>
				</div>
				<div class="form-group has-feedback">
					<label class="control-label" for="company">Company</label>
					<input type="text" class="form-control" name="company" id="company" placeholder="Company" />
					<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
				</div>
				<div class="form-group has-feedback">
					<label class="control-label" for="student_number">Student number</label>
					<input type="text" class="form-control" name="student_number" id="student_number" placeholder="Student number" />
					<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class='btn btn-primary' text="Register" >Register</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
			</form>
		</div>
	</div>
</div>
<script>
$('#registerform').validator().on('submit', function (e) {
    if (e.isDefaultPrevented()) {
    	alert('form is not valid');
    } else {
        
    }
});
</script>