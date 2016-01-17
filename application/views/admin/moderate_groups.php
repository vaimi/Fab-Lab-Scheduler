<script>

	get_group_detail(<?php echo $groups[0]['id']; ?>);

	var current_groups = [];
	var state = 'VIEW_GROUP';

	var current_group_id = '';

	function search_groups()
	{
		$.ajax({
			type: "POST",
			dataType : 'json',
			data: {
				'group_detail' : $('#filter_text').val(),
				'csrf_test_name': csrf_token
				},
			url: "<?php echo base_url('admin/get_group_list'); ?>",
			success: function(data)
			{
				$('#search_results').html('');
				$( data ).each(function( index, group ) {
					var group_content = '<a class="list-group-item" href="javascript:get_group_detail(' + group.id + ');">' + group.name + '</a>';
					$('#search_results').html($('#search_results').html() + group_content);
				});
				current_groups = data;
			}
		});
	}

	function get_group_detail(group_id)
	{
		$.ajax({
			type: "GET",
			dataType : 'json',
			data: {'csrf_test_name': csrf_token},
			url: "<?php echo base_url('admin/get_group_detail'); ?>/" + group_id.toString(),
			success: function(data)
			{
				$('#group_id').val(data.id);
				$('#group_name').val(data.name);
				$('#group_description').val(data.definition);
				$('#group_email_suffix').val(data.email_prefixes);

				current_group_id = data.id;
			}
		});
		state = 'VIEW_GROUP';
		$('#reset_group').fadeOut(0);
		$('#edit_group').fadeIn(0);
		$('#delete_group').fadeIn(0);
	}

	function update_group()
	{

		var r = confirm("Are you sure you want to update the group " + $('#group_name').val() + "?");
		if (r == false)
			return; 
		
		$.ajax({
			type: "POST",
			dataType : 'json',
			data: {
				'group_id' : $('#group_id').val(),
				'group_name' : $('#group_name').val(),
				'group_description' : $('#group_description').val(),
				'group_email_suffix' : $('#group_email_suffix').val(),
				'csrf_test_name': csrf_token
			},
			url: "<?php echo base_url('admin/update_group'); ?>",
			success: function(data)
			{
				search_groups();
				get_group_detail($('#group_id').val());
				window.alert('Group edited');
			},
			error: function(data)
			{
				window.alert('Error, cannot edit group');
			}
		});
	}

	function create_group()
	{


		
		$.ajax({
			type: "POST",
			dataType : 'json',
			data: {
				'group_id' : $('#group_id').val(),
				'group_name' : $('#group_name').val(),
				'group_description' : $('#group_description').val(),
				'group_email_suffix' : $('#group_email_suffix').val(),
				'csrf_test_name': csrf_token
			},
			url: "<?php echo base_url('admin/create_group'); ?>",
			success: function(data)
			{
				search_groups();
				get_group_detail(data.group_id);
				window.alert('Group created');
				
			},
			error: function(data)
			{
				window.alert('Error, cannot create group');
			}
		});
	}

	function delete_group()
	{

		var r = confirm("Are you sure you want to delete the group " + $('#group_name').val() + "?");
		if (r == false)
			return; 
		
		$.ajax({
			type: "POST",
			dataType : 'json',
			data: {
				'group_id' : $('#group_id').val(),
				'csrf_test_name': csrf_token
			},
			url: "<?php echo base_url('admin/delete_group'); ?>",
			success: function(data)
			{
				search_groups();
				if (current_groups.length > 0)
				{
					get_group_detail(current_groups[0].id);
				}
				else
				{
					$('#group_id').val('');
					$('#group_name').val('');
					$('#group_description').val('');
					$('#group_email_suffix').val('');
				}
			}
		});
	}

	function change_state_create_new_group()
	{
		state = 'CREATE_NEW_GROUP';
		$('#group_id').val('');
		$('#group_name').val('');
		$('#group_description').val('');
		$('#group_email_suffix').val('');

		$('#edit_group').fadeOut(0);
		$('#delete_group').fadeOut(0);
		$('#reset_group').fadeIn(0);
	}

	function bttn_Create_Group_Clicked()
	{
		if (state == 'VIEW_GROUP')
			change_state_create_new_group();
		else
			create_group();
	}

	function reset_group()
	{
		get_group_detail(current_group_id);
		$('#reset_group').fadeOut(0);
		$('#edit_group').fadeIn(0);
		$('#delete_group').fadeIn(0);
	}
		
</script>
<div class="container">
	<div class="row">
		<!-- Left side/top of the user management page -->
		<div class="col-md-4">
			<!-- Search box -->
			<h3>Group List</h3>
			<div class="row">
				<div class="col-md-12">
					<input type="text" class="form-control input-lg" id="filter_text" placeholder="Search by name, description or email suffix" onkeyup="search_groups();">
				</div>
			</div>
			<!-- Search result area -->
			<div class="row">
				<div class="col-lg-12">
					<div class="well">
						<div class="list-group" id="search_results">
						<?php foreach ($groups as $group):?>
							<a class="list-group-item" href="javascript:get_group_detail(<?php echo $group['id']; ?>);"><?php echo $group['name']; ?></a>
						<?php endforeach;?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Right side/bottom of the user management page -->
		<div class="col-md-8">
			<h3>Group Detail</h3>
			<div class="tab-pane active" id="basic">
				<form class="form-horizontal" id="basic_form">
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
					<div class="form-group">
						<label class="control-label col-md-2" for="name">Name:</label>
						<div class="col-md-8">
							<input type="hidden" id="group_id" value="" />
							<input type="text" class="form-control" id="group_name" placeholder="Name" value="">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2">Description:</label>
						<div class="col-md-8">
							<input type="text" class="form-control" id="group_description" placeholder="Description" value="">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2" for="email_suffix">Email suffix:</label>
						<div class="col-md-8">
							<input type="text" class="form-control" id="group_email_suffix" placeholder="Example: *.oulu.fi|oulu.fi" value="">
						</div>
					</div>
					<a type="button" class="noProp btn btn-info" id="new_group" onclick="bttn_Create_Group_Clicked();" >
						<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> New
					</a>
					<a id="edit_group" type="button submit" class="btn btn-success" onclick="update_group();">
						<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Edit
					</a>
					<a type="button" class="btn btn-danger" id="delete_group" onclick="delete_group();">
						<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete
					</a>
					
					<a type="button" class="btn btn-danger" id="reset_group" onclick="reset_group();" style="display:none">
						<span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> Reset
					</a>
				</form>
				
			</div>
		</div>
	</div>
</div>