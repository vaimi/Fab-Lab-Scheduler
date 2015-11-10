<div class="container">
	<?php $this->load->view('modals/create_machine');?>
	<?php echo '<a type="button" class="btn btn-primary" href=' . base_url('admin/create_machine_group') . '>'?>
	  <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> New category
	</a>
	
	<table class="table table-hover machine_table">
		<thead>
			<th>CID</th><th>Category name</th><th>Tools</th>
		</thead>
		
		<tbody>
			<?php foreach ($machineGroups as $mg):?>
			<tr data-toggle="collapse" data-target="#accordion" class="clickable">
			
				<td><?php echo $mg['MachineGroupID']?></td>
				
				<td>Lorem ipsum</td>
				<td class="m_buttons">
					<button type="button" class="noProp btn btn-info" name="1" data-toggle="modal" data-target="#createMachineModal" >
						<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> New machine
					</button>
					<button type="button" class="btn btn-info">
						<span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit
					</button>
					<button type="button" class="btn btn-danger">
						<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete
					</button>
					<script>
						$(".m_buttons").click(function(event){
// 							console.log(event);
// 							console.log(event.target.name);
							event.stopPropagation();
							//Hack for toggling modal. (otherwise it wont work)
							if ( $.inArray("noProp", event.target.classList) != -1)
							{
								$('#createMachineModal').modal('show');
								$('#cid').val(event.target.name); 
							}
						});
					</script>
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
			<?php endforeach;?>
		</tbody>
	</table>
</div>
