<script src="<?php echo asset_url();?>js/sortable.min.js"></script>
<script type="text/javascript" src="<?=asset_url()?>js/bootstrap-notify.min.js"></script>
<script type="text/javascript">
	//triggered when modal is about to be shown
	$( document ).ready(function() {
		$('#cancelModal').on('show.bs.modal', function(e) {
			$('#cancelModal').data("id", $(e.relatedTarget).data("reservation-id"));
		});
	});
	function alerter(alert_type, alert_message) {
		// alerter function for on-screen alerts
		$.notify({
		// options
		message: alert_message 
		},{
			// settings
			type: alert_type,
			mouse_over: "pause",
			timer: 5000,
			animate: {
				enter: 'animated fadeInDown',
				exit: 'animated fadeOutUp'
			}
		});
	}
	function cancelReservation() 
	{
		var id = $('#cancelModal').data("id");
		console.log(id);
		var d = { "id" : id };
		$.ajax({
			type: "POST",
			url: "cancel_reservation",
			data: d,
			success: function(data) {
				var json = JSON.parse(data);
				console.log(json);
				if(json.success)
				{
					alerter("success", "Cancellation succeeded.");
				}
				else 
				{
					alerter("danger", "error: " + json.error);
				}
			}
		}); 
	}
</script>
<div class="container">
	<table class="sortable-theme-bootstrap table table-striped" data-sortable>
		<thead>
			<tr>
				<th data-sorted="true" data-sorted-direction="ascending">ID</th>
				<th>Machine</th>
				<th>Reserved for</th>
				<th data-sortable="false">Actions</th>
			</tr>
		</thead>
		<tbody>
				<?php 
				foreach ($rdata as $row) {
					echo '<tr>';
					echo '<td>'.$row['id'].'</td>';
					echo '<td>'.$row['machine'].'</td>';
					echo '<td>'.$row['reserved'].'</td>';
					echo '<td><a type="button" class="btn btn-warning" href="#cancelModal" data-toggle="modal" data-reservation-id='.$row['id'].'><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Cancel</a>';
					echo '</tr>';
				}
				?>
		</tbody>
	</table>
</div>
<div id="cancelModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Confirm</h4>
	      </div>
	      <div class="modal-body">
	        <p>Do you really want to cancel reservation?</p>
	      </div>
	      <div class="modal-footer">
	      	<button type="button" class="btn btn-danger" data-dismiss="modal" onclick="cancelReservation();">Confirm</button>
	      	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
	    </div>
	</div>
</div>