<?php $this->load->view('partials/header'); ?>
 
<?php $this->load->view('partials/menu'); ?>

<div class="container">
	<h4 class="modal-title">Create New Machine</h4>
	<hr/>
	<form name="create_machine" method="post" action="<?php echo base_url();?>admin/create_machine"
		<div class="input-group">
	  		<input type="text" class="form-control focusedInput" id="username" name="username" placeholder="Username" aria-describedby="basic-addon1">
	  		<input type="text" class="form-control focusedInput" id="manufacturer" name="manufacturer" placeholder="Manufacturer" aria-describedby="basic-addon1">
	  		<input type="text" class="form-control focusedInput" id="model" name="model" placeholder="Model" aria-describedby="basic-addon1">
	  		<label for="desc">Description:</label>
			<textarea class="form-control" rows="5" id="desc" name="desc" ></textarea>
			<div class="checkbox">
	 			<label><input type="checkbox" id="needSupervision" name="needSupervisor" value="yes">Need supervision</label>
			</div>
			<div class="btn-toolbar">
				<button type="submit" class='btn btn-success' onclick="this.disabled=true;this.value='Sending, please wait...';this.form.submit();">Save</button>
				<button type="button" class='btn' <?php echo "onclick=\"location.href =", "'" , site_url("admin/moderate_machines") , "'" , "\""; ?> >Cancel</button>
			</div>
		</div>
	</form>
<!-- 	<div class="form-group"> -->
<!-- 		<label for="desc">Description:</label> -->
<!-- 		<textarea class="form-control" rows="5" id="desc"></textarea> -->
<!-- 	</div> -->
	
</div>


<?php $this->load->view('partials/footer'); ?>