<?php $this->load->view('partials/header'); ?>
 
<?php $this->load->view('partials/menu'); ?>

<div class="container">
	<h4 class="modal-title">Create New Machine Group</h4>
	<hr/>
	<?php foreach ($errors as $item):?>
		<div style='color:red;'>- <?php echo $item;?></div>
	<?php endforeach;?>
	<form name="login" method="post" action="<?php echo base_url();?>admin/create_machine_group">
		<p>Name (*):</p>
		<p><input type="text" class="form-control" name="name" id="name"
					style="" value="<?php echo $name;?>" required="" autofocus=""
					placeholder="Machine Group Name" /></p>
		<p>Description:</p>
		<p><textarea id="description" name="description" required=""><?php echo $description;?></textarea></p>
		<p>Need Supervision: <input type="checkbox" value="need_supervision" name="need_supervision" id="need_supervision" <?php if ($need_supervision !='') { ?> checked <?php } ?> /></p>
		<p>
			<div class="btn-toolbar">
				<button type="submit" class='btn btn-success'>Create Machine Group</button>
				<button type="button" class='btn' <?php echo "onclick=\"location.href =", "'" , site_url("admin/moderate_machines") , "'" , "\""; ?> >Cancel</button>
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
<?php $this->load->view('partials/footer'); ?>