<?php 
	$deadline = isset($settings['reservation_deadline']) ? $settings['reservation_deadline'] : "undefined"; 
	$reservation_timespan = isset($settings['reservation_timespan']) ? $settings['reservation_timespan'] : "undefined";
	$interval = isset($settings['interval']) ? $settings['interval'] : "undefined";
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
        <div class="form-group form-inline">
	        <label for="reservation_timespan">Reservation submission timespan:</label>
	        <input type='number' min="1" value="<?php echo $reservation_timespan; ?>" class="form-control" name="reservation_timespan" />
	        <label class="radio-inline"><input type="radio" name="interval" value="Days" <?php  print $interval == "Days" ? "checked" : "" ?> >Days</label>
			<label class="radio-inline"><input type="radio" name="interval" value="Weeks" <?php  print $interval == "Weeks" ? "checked" : "" ?> >Weeks</label>
			<label class="radio-inline"><input type="radio" name="interval" value="Months" <?php  print $interval == "Months" ? "checked" : "" ?> >Months</label>
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