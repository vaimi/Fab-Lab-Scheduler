<form class="form-horizontal" id=form>
	<div class="form-group">
		<label class="control-label col-xs-3" for="input_email">Email:</label>
		<div class="col-xs-9">
			<input type="email" class="form-control" id="input_email" placeholder="Email" value=<?php echo $data->email;?>>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-xs-3">Name:</label>
		<div class="col-xs-4">
			<input type="text" class="form-control" id="first_name" placeholder="First Name" value=<?php echo $data->name;?>>
		</div>
		<div class="col-xs-5">
			<input type="text" class="form-control" id="last_name" placeholder="Last Name" value=<?php echo $data->surname;?>>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-xs-3" for="phone_number">Phone:</label>
		<div class="col-xs-9">
			<input type="tel" class="form-control" id="phone_number" placeholder="Phone Number" value=<?php echo $data->phone_number;?>>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-xs-3" for="postal_address">Address:</label>
		<div class="col-xs-9">
			<textarea rows="3" class="form-control" id="postal_address" placeholder="Postal Address"><?php echo $data->address_street;?></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-xs-3" for="zip_code">Zip Code:</label>
		<div class="col-xs-9">
			<input type="text" class="form-control" id="zip_code" placeholder="Zip Code" value=<?php echo $data->address_postal_code;?>>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-xs-3" for="zip_code">Student ID:</label>
		<div class="col-xs-9">
			<input type="text" class="form-control" id="zip_code" placeholder="Student ID" value=<?php echo $data->student_number;?>>
		</div>
	</div>
	<div class="well">
		<div class="btn-toolbar">
			<button type="button" class="btn btn-success">
				<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Save
			</button>
			<div class="btn-group">
				<button type="button" class="btn btn-info">
					<span class="glyphicon glyphicon-education" aria-hidden="true"></span> Levels
				</button>
				<button type="button" class="btn btn-info">
					<span class="glyphicon glyphicon-lock" aria-hidden="true"></span> Groups
				</button>
			</div>
			<div class="btn-group">
				<button type="button" class="btn btn-warning">
					<span class="glyphicon glyphicon-ban-circle" aria-hidden="true"></span> Ban
				</button>
				<button type="button" class="btn btn-danger">
					<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete
				</button>
			</div>
		</div>
	</div>
