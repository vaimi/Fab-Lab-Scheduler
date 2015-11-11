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
			<tr >
				<td colspan="3">
					<div id="accordion_<?php echo $mg['MachineGroupID'];?>" class="collapse">
						<table class="table table-hover machine_table">
							<thead>
								<th>MID</th><th>Manufacturer</th><th>Model</th><th>Tools</th>
							</thead>
							<tbody id="machine_in_group_<?php echo $mg['MachineGroupID'];?>">
							<?php foreach ($mg['machines'] as $m):?>
								<tr id="machine_<?php echo $m->MachineID ?>">
									<td><?php echo $m->MachineID ?></td>
									<td id="detail_manufacturer_<?php echo $m->MachineID ?>"><?php echo $m->Manufacturer ?></td>
									<td id="detail_model_<?php echo $m->MachineID ?>"><?php echo $m->Model ?></td>
									<td>
										<button type="button" class="btn btn-info" onclick="$('#edit_machine_<?php echo $m->MachineID; ?>').modal('show');">
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
									<div id="edit_machine_<?php echo $m->MachineID; ?>" class="modal fade" role="dialog">
	  <div class="modal-dialog">
	
	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Edit Machine</h4>
	      </div>
	      <div class="modal-body">
		  	

					<label for="machine_group_id_<?php echo $m->MachineID; ?>">Machine group</label>
					<select id="machine_group_id_<?php echo $m->MachineID; ?>" class="form-control">
					<?php foreach ($machine_groups as $mg) {?>
						<option <?php if ($mg->MachineGroupID == $m->MachineGroupID) {?> selected <?php } ?> value="<?php echo $mg->MachineGroupID; ?>"><?php echo $mg->Name; ?></option>
					<?php }?>
					</select>
					<label for="machine_name_<?php echo $m->MachineID; ?>">Name</label>
			  		<input type="text" value="<?php echo $m->MachineName ?>" class="form-control focusedInput" id="machine_name_<?php echo $m->MachineID; ?>" name="machine_name_<?php echo $m->MachineID; ?>" placeholder="Machine name" aria-describedby="basic-addon1">
			  		<label for="manufacturer_<?php echo $m->MachineID; ?>">Manufacturer</label>
			  		<input type="text" value="<?php echo $m->Manufacturer ?>" class="form-control focusedInput" id="manufacturer_<?php echo $m->MachineID; ?>" name="manufacturer_<?php echo $m->MachineID; ?>" placeholder="Manufacturer" aria-describedby="basic-addon1">
			  		<label for="model_<?php echo $m->MachineID; ?>">Model</label>
			  		<input type="text" value="<?php echo $m->Model ?>" class="form-control focusedInput" id="model_<?php echo $m->MachineID; ?>" name="model_<?php echo $m->MachineID; ?>" placeholder="Model" aria-describedby="basic-addon1">
			  		<label for="desc">Description:</label>
					<textarea class="form-control" rows="5" id="desc_<?php echo $m->MachineID; ?>" name="desc_<?php echo $m->MachineID; ?>" ><?php echo $m->Description ?></textarea>
					<div class="checkbox">
			 			<label><input type="checkbox" id="needSupervision_<?php echo $m->MachineID; ?>" name="needSupervisor_<?php echo $m->MachineID; ?>" <?php if ($m->NeedSupervision == 1) {?> checked <?php } ?> value="yes">Need supervision</label>
					</div>
					<div class="btn-toolbar">
						<button type="submit" class='btn btn-success' onclick="edit_machine(<?php echo $m->MachineID; ?>, <?php echo $m->MachineGroupID; ?>);">Save</button>
						<button type="button" class='btn' data-dismiss="modal" onclick="$('#edit_machine_<?php echo $m->MachineID; ?>').modal('hide');" >Cancel</button>
					</div>
	      </div>
	    </div>
	
	  </div>
	</div>
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
				
				function edit_machine(machine_id, old_machine_group_id)
				{
					$.ajax({
						type: "POST",
						dataType : 'json',
						data: {
							'machine_group_id' : $('#machine_group_id_' + machine_id.toString()).val(),
							'machine_name' : $('#machine_name_' + machine_id.toString()).val(),
							'manufacturer' : $('#manufacturer_' + machine_id.toString()).val(),
							'model' : $('#model_' + machine_id.toString()).val(),
							'description' : $('#desc_' + machine_id.toString()).val(),
							'need_supervision': $('#needSupervision_' + machine_id.toString()).is(':checked')?'yes':''
							},
						url: "<?php echo base_url('admin/edit_machine'); ?>/" + machine_id,
						success: function(data) {
							// return success
							if (data.result == true) 
							{
								//$('#machine_' + machine_id.toString()).fadeOut();
								
								$('#detail_manufacturer_' + machine_id.toString()).html($('#manufacturer_' + machine_id.toString()).val());
								$('#detail_model_' + machine_id.toString()).html($('#model_' + machine_id.toString()).val());
								$('#edit_machine_' + machine_id.toString()).modal('hide');
								
								if (old_machine_group_id!=$('#machine_group_id_' + machine_id.toString()).val())
								{
									machine_content = $('#machine_' + machine_id.toString()).html();
									$('#machine_' + machine_id.toString()).remove();
									$('#machine_in_group_' + $('#machine_group_id_' + machine_id.toString()).val().toString()).append(machine_content);
								}
							}
							else
							{
								window.alert('Something wrong, cannot edit the machine!!!');
							}
						}
					});
				}
				
			</script>
		</tbody>
	</table>
</div>
