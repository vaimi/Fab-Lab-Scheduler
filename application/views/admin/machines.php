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
			<tr id="machine_group_<?php echo $mg['MachineGroupID']; ?>" data-toggle="collapse" data-target="#accordion_<?php echo $mg['MachineGroupID']?>" class="clickable">
			
				<td><?php echo $mg['MachineGroupID']?></td>
				
				<td><?php echo $mg['Name'];?></td>
				<td class="m_buttons">
					<button type="button" class="noProp btn btn-info" name="<?php echo $mg['MachineGroupID']?>" data-toggle="modal" data-target="#createMachineModal" >
						<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> New machine
					</button>
					<button type="button" class="btn btn-info">
						<span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit
					</button>
					<span id="activate_button_<?php echo $mg['MachineGroupID']; ?>">
					<?php if ($mg['active'] == 0) {?>
					<button type="button" class="btn btn-success" onclick="activate_deactivate_machine_group(<?php echo $mg['MachineGroupID']; ?>, false);">
						<span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Activate
					</button>
					<?php } else { ?>
					<button type="button" class="btn btn-warning" onclick="activate_deactivate_machine_group(<?php echo $mg['MachineGroupID']; ?>, true);">
						<span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Deactivate
					</button>
					<?php }?>
					</span>
					<button type="button" class="btn btn-danger" onclick="delete_machine_group(<?php echo $mg['MachineGroupID']; ?>, '<?php echo $mg['Name']; ?>');">
						<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete
					</button>
					
				</td>
			</tr>
			<tr id="machine_in_group_<?php echo $mg['MachineGroupID'];?>">
				<td colspan="3">
					<div id="accordion_<?php echo $mg['MachineGroupID'];?>" class="collapse">
						<table class="table table-hover machine_table">
							<thead>
								<th>MID</th><th>Manufacturer</th><th>Model</th><th>Tools</th>
							</thead>
							<tbody>
							<?php foreach ($mg['machines'] as $m):?>
								<tr id="machine_<?php echo $m->MachineID ?>">
									<td><?php echo $m->MachineID ?></td>
									<td><?php echo $m->Manufacturer ?></td>
									<td><?php echo $m->Model ?></td>
									<td>
										<button type="button" class="btn btn-info">
											<span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit
										</button>
										<span id="activate_machine_button_<?php echo $m->MachineID; ?>">
											<?php if ($m->active == 0) {?>
											<button type="button" class="btn btn-success" onclick="activate_deactivate_machine(<?php echo $m->MachineID; ?>, false);">
												<span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Activate
											</button>
											<?php } else { ?>
											<button type="button" class="btn btn-warning" onclick="activate_deactivate_machine(<?php echo $m->MachineID; ?>, true);">
												<span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Deactivate
											</button>
											<?php }?>
										</span>
										<button type="button" class="btn btn-danger" onclick="delete_machine(<?php echo $m->MachineID; ?>, '<?php echo $m->Manufacturer ?>', '<?php echo $m->Model ?>');">
											<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete
										</button>
									</td>
								</tr>
							
							<?php endforeach;?>
							</tbody>
						</table>
					</div>
				</td>
			</tr>
			<?php endforeach;?>
			<script>
				$(".m_buttons").click(function(event){
					console.log(event);
					console.log(event.target.name);
					event.stopPropagation();
					//Hack for toggling modal. (otherwise it wont work)
					if ( $.inArray("noProp", event.target.classList) != -1)
					{
						$('#createMachineModal').modal('show');
						$('#cid').val(event.target.name); 
					}
				});
				function activate_deactivate_machine_group(machine_group_id, old_status)
				{
					$.ajax({
						type: "GET",
						dataType : 'json',
						url: "<?php echo base_url('admin/change_activation_status_machine_group'); ?>/" + machine_group_id,
						success: function(data) {
							// return success
							if (data.result == true) 
							{
								if (old_status)
									$('#activate_button_' + machine_group_id.toString()).html('<button type="button" class="btn btn-success" onclick="activate_deactivate_machine_group(' + + machine_group_id.toString() + ', false);"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Activate</button>');
								else
									$('#activate_button_' + machine_group_id.toString()).html('<button type="button" class="btn btn-warning" onclick="activate_deactivate_machine_group(' + + machine_group_id.toString() + ', true);"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Deactivate</button>');
							}
							else
							{
								window.alert('Something wrong, cannot change status of the machine group!!!');
							}
						}
					});
				}
				function delete_machine_group(machine_group_id, machine_group_name)
				{
					var r = confirm("Are you sure you want to delete the machine group " + machine_group_name + "?");
					if (r == true) 
					{
						$.ajax({
							type: "GET",
							dataType : 'json',
							url: "<?php echo base_url('admin/delete_machine_group'); ?>/" + machine_group_id,
							success: function(data) {
								// return success
								if (data.result == true) 
								{
									$('#machine_group_' + machine_group_id.toString()).fadeOut();
									$('#machine_in_group_' + machine_group_id.toString()).fadeOut();
								}
								else
								{
									window.alert('Something wrong, cannot delete the machine group!!!');
								}
							}
						});
					}
				}

				function activate_deactivate_machine(machine_id, old_status)
				{
					$.ajax({
						type: "GET",
						dataType : 'json',
						url: "<?php echo base_url('admin/change_activation_status_machine'); ?>/" + machine_id,
						success: function(data) {
							// return success
							if (data.result == true) 
							{
								if (old_status)
									$('#activate_machine_button_' + machine_id.toString()).html('<button type="button" class="btn btn-success" onclick="activate_deactivate_machine(' + + machine_id.toString() + ', false);"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Activate</button>');
								else
									$('#activate_machine_button_' + machine_id.toString()).html('<button type="button" class="btn btn-warning" onclick="activate_deactivate_machine(' + + machine_id.toString() + ', true);"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Deactivate</button>');
							}
							else
							{
								window.alert('Something wrong, cannot change status of the machine group!!!');
							}
						}
					});
				}
				function delete_machine(machine_id, manufacturer, model)
				{
					var r = confirm("Are you sure you want to delete the machine with manufacturer " + manufacturer + " and model " + model + "?");
					if (r == true) 
					{
						$.ajax({
							type: "GET",
							dataType : 'json',
							url: "<?php echo base_url('admin/delete_machine'); ?>/" + machine_id,
							success: function(data) {
								// return success
								if (data.result == true) 
								{
									$('#machine_' + machine_id.toString()).fadeOut();
								}
								else
								{
									window.alert('Something wrong, cannot delete the machine!!!');
								}
							}
						});
					}
				}
				
			</script>
		</tbody>
	</table>
</div>
