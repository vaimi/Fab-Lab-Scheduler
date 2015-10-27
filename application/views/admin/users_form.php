<script type="text/javascript">
	onload: banState();
	
	function banState() {
		var banned = <?php echo $data->banned;?>;
		
		if (banned) {
			$("#ban_button").attr("href","javascript:unbanUser();");
			$("#ban_button span").attr("class","glyphicon glyphicon-ok-circle");
			var $contents = $('#ban_button').contents();
			$contents[$contents.length - 1].nodeValue = ' Unban';
		} else {
			$("#ban_button").attr("href","javascript:banUser();");
			$("#ban_button span").attr("class","glyphicon glyphicon-ban-circle");
			var $contents = $('#ban_button').contents();
			$contents[$contents.length - 1].nodeValue = ' Ban';
		}
	}
	
	function banUser() {
		var post_data = {
			'user_id': <?php echo $data->id;?>
		};
		
		var name = <?php echo '"' . $data->name . " " . $data->surname . '"';?>;

		$.ajax({
			type: "POST",
			url: "<?php echo base_url('admin/ban_user'); ?>",
			data: post_data,
			success: function(data) {
				// return success
				if (data.length > 0) {
					$("#ban_button").addClass("animated pulse").one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', 
					function() {
						$(this).removeClass("animated pulse");
					});;
					$("#ban_button").attr("href","javascript:unbanUser();");
					$("#ban_button span").attr("class","glyphicon glyphicon-ok-circle");
					var $contents = $('#ban_button').contents();
					$contents[$contents.length - 1].nodeValue = ' Unban';
					alerter("info", "User " + name + " <strong>banned</strong>!"); 
				}
			}
		}); 
	}
	
	function unbanUser() {
		var post_data = {
			'user_id': <?php echo $data->id;?>
		};
		
		var name = <?php echo '"' . $data->name . " " . $data->surname . '"';?>;

		$.ajax({
			type: "POST",
			url: "<?php echo base_url('admin/unban_user'); ?>",
			data: post_data,
			success: function(data) {
				// return success
				if (data.length > 0) {
					$("#ban_button").addClass("animated pulse").one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', 
					function() {
						$(this).removeClass("animated pulse");
					});;
					$("#ban_button").attr("href","javascript:banUser();");
					$("#ban_button span").attr("class","glyphicon glyphicon-ban-circle");
					var $contents = $('#ban_button').contents();
					$contents[$contents.length - 1].nodeValue = ' Ban';
					alerter("info", "User " + name + " <strong>unbanned</strong>!"); 
				}
			}
		}); 
	}
	
	function deleteUser() {
		var post_data = {
			'user_id': <?php echo $data->id;?>
		};
		
		var name = <?php echo '"' . $data->name . " " . $data->surname . '"';?>;

		$.ajax({
			type: "POST",
			url: "<?php echo base_url('admin/delete_user'); ?>",
			data: post_data,
			success: function(data) {
				// return success
				if (data.length > 0) {
					ajaxSearch(); //TODO some cleaner way might be better
					$("#form").addClass("animated fadeOut").one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', 
					function() {
						$( "#form" ).empty();
					});;
					alerter("info", "User " + name + " <strong>deleted</strong>!"); 
				}
			}
		}); 
	}
	
	function alerter(alert_type, alert_message) {
		
		$.notify({
		// options
			message: alert_message 
		},{
			// settings
			type: alert_type,
			animate: {
				enter: 'animated fadeInDown',
				exit: 'animated fadeOutUp'
			}
		});
	}
	
	$(document).ready(function() {
		$("#remove_button").popover({
			placement: 'top',
			html: 'true',
			title : 'Are you sure?',
			content : '<p>Effect is permanent!</p>'+
			'<a href="javascript:deleteUser();" role="button" class="btn btn-danger" style="margin: 10px 10px;">' +
			'<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Do it!' +
			'</a>'
			});
		});  
</script>

<form class="form-horizontal" id=form>
	<div class="form-group">
		<label class="control-label col-xs-3" for="input_email">Email:</label>
		<div class="col-xs-9">
			<input type="email" class="form-control" id="input_email" placeholder="Email" value=<?php echo $data->email;?>>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-xs-3">Name:</label>
		<div class="col-xs-4">
			<input type="text" class="form-control" id="first_name" placeholder="First Name" value=<?php echo $data->name;?>>
		</div>
		<div class="col-xs-5">
			<input type="text" class="form-control" id="last_name" placeholder="Last Name" value=<?php echo $data->surname;?>>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-xs-3" for="phone_number">Phone:</label>
		<div class="col-xs-9">
			<input type="tel" class="form-control" id="phone_number" placeholder="Phone Number" value=<?php echo $data->phone_number;?>>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-xs-3" for="postal_address">Address:</label>
		<div class="col-xs-9">
			<textarea rows="3" class="form-control" id="postal_address" placeholder="Postal Address"><?php echo $data->address_street;?></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-xs-3" for="zip_code">Zip Code:</label>
		<div class="col-xs-9">
			<input type="text" class="form-control" id="zip_code" placeholder="Zip Code" value=<?php echo $data->address_postal_code;?>>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-xs-3" for="zip_code">Student ID:</label>
		<div class="col-xs-9">
			<input type="text" class="form-control" id="zip_code" placeholder="Student ID" value=<?php echo $data->student_number;?>>
		</div>
	</div>
	<div class="well">
		<div class="btn-toolbar">
			<button type="button" class="btn btn-success">
				<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Save
			</button>
			<div class="btn-group">
				<button type="button" class="btn btn-info">
					<span class="glyphicon glyphicon-education" aria-hidden="true"></span> Levels
				</button>
				<button type="button" class="btn btn-info">
					<span class="glyphicon glyphicon-lock" aria-hidden="true"></span> Groups
				</button>
			</div>
			<div class="btn-group">
				<a href="javascript:banUser();" type="button" id="ban_button" class="btn btn-warning">
					<span class="glyphicon glyphicon-ban-circle" aria-hidden="true"></span> Ban
				</a>
				<a tabindex="0" data-trigger="focus" role="button" id="remove_button" class="btn btn-danger">
					<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete
				</a>
			</div>
		</div>
	</div>
