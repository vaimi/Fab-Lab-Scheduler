<div class="container">
	<div class="row">
		<div class="col-md-6">
			<h2>Search for user</h2>
			<div class="row">
				<div class="col-lg-12">
					<div class="input-group input-group-lg">
						<input type="text" class="form-control input-lg" id="search_people" placeholder="Search by email, name, phone...">
							<span class="input-group-btn">
								<button class="btn btn-default btn-lg" type="submit">Search</button>
							</span>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<h4>Results</h4>
					<div class="list-group">
					    <a href="#" class="list-group-item active">Mikko Väisänen</a>
						<a href="#" class="list-group-item">Markus Särkiniemi</a>
						<a href="#" class="list-group-item">Thang Luu</a>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<h2>User data</h2>
			<form class="form-horizontal">
			<div class="form-group">
				<label class="control-label col-xs-3" for="input_email">Email:</label>
				<div class="col-xs-9">
					<input type="email" class="form-control" id="input_email" placeholder="Email">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-3">Name:</label>
				<div class="col-xs-4">
					<input type="text" class="form-control" id="first_name" placeholder="First Name">
				</div>
				<div class="col-xs-5">
					<input type="text" class="form-control" id="last_name" placeholder="Last Name">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-3" for="phone_number">Phone:</label>
				<div class="col-xs-9">
					<input type="tel" class="form-control" id="phone_number" placeholder="Phone Number">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-3" for="postal_address">Address:</label>
				<div class="col-xs-9">
					<textarea rows="3" class="form-control" id="postal_address" placeholder="Postal Address"></textarea>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-3" for="zip_code">Zip Code:</label>
				<div class="col-xs-9">
					<input type="text" class="form-control" id="zip_code" placeholder="Zip Code">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-3" for="zip_code">Student ID:</label>
				<div class="col-xs-9">
					<input type="text" class="form-control" id="zip_code" placeholder="Student ID">
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
		</div>
	</div>
</div>