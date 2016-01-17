<div class="container">
	<h4 class="modal-title">Edit Machine Group</h4>
	<hr/>
	<?php if ($action == 'edit') {?><div style='color:red;'>Edit successful</div><?php }?>
	<form name="login" method="post" action="<?php echo base_url();?>admin/edit_machine_group/<?php echo $machine_group['MachineGroupID'] ?>">
		<p>Name (*):</p>
		<p><input type="text" class="form-control" name="name" id="name"
					style="" value="<?php echo $machine_group['Name'];?>" required="" autofocus=""
					placeholder="Machine Group Name" /></p>
		<p>Description:</p>
		<p><textarea id="description" name="description" required=""><?php echo $machine_group['Description'];?></textarea></p>
		
		<p>
			<div class="btn-toolbar">
				<button type="submit" class='btn btn-success'>Save</button>
				<button type="button" class='btn' <?php echo "onclick=\"location.href =", "'" , site_url("admin/moderate_machines") , "'" , "\""; ?> >Back</button>
			</div>
		</p>
	</form>
</div>

<script>
	$(document).ready(function() {
		$('#description').summernote({
			  height: 200,                 // set editor height

			  minHeight: null,             // set minimum height of editor
			  maxHeight: null,             // set maximum height of editor

			  focus: false,                 // set focus to editable area after initializing summernote
			});
	});
</script>