<script>

	get_group_detail(<?php echo $groups[0]['id']; ?>);

	var current_groups = [];

	function search_groups()
	{
		$.ajax({
			type: "POST",
			dataType : 'json',
			data: {
				'group_detail' : $('#filter_text').val()
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
		$('#group_name').prop("readonly", true);
		$('#group_description').prop("readonly", true);
		$('#group_email_suffix').prop("readonly", true);
		$.ajax({
			type: "GET",
			dataType : 'json',
			url: "<?php echo base_url('admin/get_group_detail'); ?>/" + group_id.toString(),
			success: function(data)
			{
				$('#group_id').val(data.id);
				$('#group_name').val(data.name);
				$('#group_description').val(data.definition);
				$('#group_email_suffix').val(data.email_prefixes);
			}
		});
	}

	function update_group()
	{
		$.ajax({
			type: "POST",
			dataType : 'json',
			data: {
				'group_id' : $('#group_id').val(),
				'group_name' : $('#group_name').val(),
				'group_description' : $('#group_description').val(),
				'group_email_suffix' : $('#group_email_suffix').val()
			},
			url: "<?php echo base_url('admin/update_group'); ?>",
			success: function(data)
			{
				$('#group_id').val(data.id);
				$('#group_name').val(data.name);
				$('#group_description').val(data.definition);
				$('#group_email_suffix').val(data.email_prefixes);
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
				'group_email_suffix' : $('#group_email_suffix').val()
			},
			url: "<?php echo base_url('admin/update_group'); ?>",
			success: function(data)
			{
				search_groups();
				get_group_detail(data.id);
				
				$('#group_id').val(data.id);
				$('#group_name').val(data.name);
				$('#group_description').val(data.definition);
				$('#group_email_suffix').val(data.email_prefixes);
				
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
				'group_id' : $('#group_id').val()
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
					<div class="form-group">
						<label class="control-label col-md-2" for="name">Name:</label>
						<div class="col-md-8">
							<input type="hidden" id="group_id" value="" />
							<input type="text" class="form-control" id="group_name" placeholder="Name" value="" readonly>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2">Description:</label>
						<div class="col-md-8">
							<input type="text" class="form-control" id="group_description" placeholder="Description" value="" readonly>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2" for="email_suffix">Email suffix:</label>
						<div class="col-md-8">
							<input type="text" class="form-control" id="group_email_suffix" placeholder="Example: *.oulu.fi|oulu.fi" value="" readonly>
						</div>
					</div>
					<a type="button" class="noProp btn btn-info" id="new_group" onclick="change_state_create_new_group();" >
						<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> New
					</a>
					<a id="edit_group" type="button submit" class="btn btn-success">
						<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Edit
					</a>
					<a type="button" class="btn btn-danger" id="delete_group" onclick="delete_group();">
						<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete
					</a>
				</form>
				
			</div>
		</div>
	</div>
</div>