<?php 
	$deadline = isset($settings['reservation_deadline']) ? $settings['reservation_deadline'] : "undefined"; 
?>
<div class="container">
	<form role="form" action="<?php echo base_url();?>admin/save_general_settings" method="post">
		<label for="reservation_deadline">Reservation submission deadline:</label>
		<div class="form-group">
        	<div class='input-group date' id='reservation_deadline'>
            	<input type='text' class="form-control" name="reservation_deadline" />
                <span class="input-group-addon">
                	<span class="glyphicon glyphicon-time"></span>
               </span>
        	</div>
        </div>
		<button type="submit" class='btn btn-success'>Save</button>
	</form>
</div>

<script type="text/javascript">
	$(function () {
		$('#reservation_deadline').datetimepicker({
			format: 'HH:mm',
			stepping: 30
		});
		$('#reservation_deadline').data("DateTimePicker").date("<?php echo $deadline; ?>");
	});
	
</script>