<?php 
	$deadline = isset($settings['reservation_deadline']) ? $settings['reservation_deadline'] : "undefined"; 
	$default_tokens = isset($settings['default_tokens']) ? $settings['default_tokens'] : "undefined";
	$reservation_timespan = isset($settings['reservation_timespan']) ? $settings['reservation_timespan'] : "undefined";
	$nightslot_preparation_time = isset($settings['nightslot_pre_time']) ? $settings['nightslot_pre_time'] : "undefined";
	$nightslot_threshold = isset($settings['nightslot_threshold']) ? $settings['nightslot_threshold'] : "undefined";
	$interval = isset($settings['interval']) ? $settings['interval'] : "undefined";
?>
<div class="container">
	<form role="form" action="<?php echo base_url();?>admin/save_general_settings" method="post">
		<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
		<fieldset>
			<legend>Reservation</legend>
			<div class="form-group form-inline">
				<label class="control-label col-md-2" for="reservation_deadline">Submission deadline:</label>
	        	<div class='input-group date' id='reservation_deadline'>
	            	<input type='text' class="form-control" name="reservation_deadline" />
	                <span class="input-group-addon">
	                	<span class="glyphicon glyphicon-time"></span>
	               </span>
	        	</div>
	        </div>
	        <div class="form-group form-inline">
		        <label class="control-label col-md-2" for="reservation_timespan">Submission timespan:</label>
		        <input type='number' min="1" value="<?php echo $reservation_timespan; ?>" class="form-control" name="reservation_timespan" />
		        <label class="radio-inline"><input type="radio" name="interval" value="Days" <?php  print $interval == "Days" ? "checked" : "" ?> >Days</label>
				<label class="radio-inline"><input type="radio" name="interval" value="Weeks" <?php  print $interval == "Weeks" ? "checked" : "" ?> >Weeks</label>
				<label class="radio-inline"><input type="radio" name="interval" value="Months" <?php  print $interval == "Months" ? "checked" : "" ?> >Months</label>
		   	</div>
			<div class="form-group form-inline">
	        	<label class="control-label col-md-2" for="default_tokens">Amount of default tokens:</label>
	        	<input type='number' min="1" value="<?php echo $default_tokens; ?>" class="form-control" name="default_tokens" />
	        </div>
        </fieldset>
        
        <fieldset>
        	<legend>Nightslot</legend>
        	<div class="form-group form-inline">
	        	<label class="control-label col-md-2" for="nightslot_pre_time">Preparation time (minutes):</label>
	        	<input type='number' min="1" value="<?php echo $nightslot_preparation_time; ?>" class="form-control" name="nightslot_pre_time" />
	        </div>
	        <div class="form-group form-inline">
	        	<label class="control-label col-md-2" for="nightslot_threshold">Threshold time (minutes):</label>
	        	<input type='number' min="1" value="<?php echo $nightslot_threshold; ?>" class="form-control" name="nightslot_threshold" />
	        </div>
        </fieldset>

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