<div class="container">
	<?php echo '<a type="button" class="btn btn-primary" href=' . base_url('admin/create_machine_group') . '>'?>
	  <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> New category
	</a>
	<table class="table table-hover machine_table">
		<thead>
			<th>CID</th><th>Catecory name</th><th>Tools</th>
		</thead>
		
		<tbody>
			<tr data-toggle="collapse" data-target="#accordion" class="clickable">
				<td>1</td>
				<td>Lorem ipsum</td>
				<td>
					<button type="button" class="btn btn-info">
						<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> New machine
					</button>
					<button type="button" class="btn btn-info">
						<span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit
					</button>
					<button type="button" class="btn btn-danger">
						<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete
					</button>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<div id="accordion" class="collapse">
						<table class="table table-hover machine_table">
							<thead>
								<th>MID</th><th>Manufacturer</th><th>Model</th><th>Tools</th>
							</thead>
							<tbody>
								<tr>
									<td>1</td>
									<td>Lorem ipsum</td>
									<td>Lorem ipsum</td>
									<td>
										<button type="button" class="btn btn-info">
											<span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit
										</button>
										<button type="button" class="btn btn-danger">
											<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete
										</button>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
</div>
