<!-- Create Machine Modal -->
	<div id="createMachineModal" class="modal fade" role="dialog">
	  <div class="modal-dialog">
	
	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Create New Machine</h4>
	      </div>
	      <div class="modal-body">
		  	<form name="create_machine" method="post" action="<?php echo base_url();?>admin/create_machine">
				<div class="input-group">
					<input id="cid" type="hidden" name="machineGroup">
			  		<input type="text" class="form-control focusedInput" id="machinename" name="machinename" placeholder="Machine name" aria-describedby="basic-addon1">
			  		<input type="text" class="form-control focusedInput" id="manufacturer" name="manufacturer" placeholder="Manufacturer" aria-describedby="basic-addon1">
			  		<input type="text" class="form-control focusedInput" id="model" name="model" placeholder="Model" aria-describedby="basic-addon1">
			  		<label for="desc">Description:</label>
					<textarea class="form-control summernote_desc" rows="5" id="desc" name="desc" ></textarea>
					<div class="checkbox">
			 			<label><input type="checkbox" id="needSupervision" name="needSupervisor" value="yes">Need supervision</label>
					</div>
					<div class="btn-toolbar">
						<button type="submit" class='btn btn-success' onclick="this.disabled=true;this.value='Sending, please wait...';this.form.submit();">Save</button>
						<button type="button" class='btn' data-dismiss="modal" >Cancel</button>
					</div>
				</div>
			</form>
	      </div>
	    </div>
	
	  </div>
	</div>