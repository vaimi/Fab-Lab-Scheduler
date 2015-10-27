<script type="text/javascript">
	onload: banState();
	
	function disableForm (yes) {
		if (yes) {
			$("#form :input").attr('disabled', true);
			$("#toolbar a").addClass('disabled');
		} else {
			$('#form :input').removeAttr('disabled');
			$('#toolbar a').removeClass('disabled');
		}
	}
	
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
	
	function saveData() {
		disableForm(true);
		var post_data = {
			'user_id': <?php echo $data->id;?>,
			'email': $('#email_input').val(),
			'username': $('#name_input').val(),
			'surname': $('#surname_input').attr('value'),
			'phone_number': $('#phone_number_input').val(),
			'address_street': $('#address_street_input').val(),
			'address_postal_code': $('#address_postal_code_input').val(),
			'student_number': $('#student_number_input').val()
		}
		
		$.ajax({
			type: "POST",
			url: "<?php echo base_url('admin/save_user_data'); ?>",
			data: post_data,
			success: function(data) {
				disableForm(false);
				if (data.length > 0) {
					var message = $.parseJSON(data);
					if (message['success'] == 1) {
						alerter("success", "User " + post_data['username'] + " " + post_data['surname'] + " data <strong>saved</strong>!"); 
					} else {
						$.each(message.errors, function(index, value) {
						   alerter("warning", value);
					    });
					}
				}
			}
		}); 
	}
	
	function banUser() {
		disableForm(true);
		var post_data = {
			'user_id': <?php echo $data->id;?>
		};
		
		var name = <?php echo '"' . $data->name . " " . $data->surname . '"';?>;

		$.ajax({
			type: "POST",
			url: "<?php echo base_url('admin/ban_user'); ?>",
			data: post_data,
			success: function(data) {
				disableForm(false);
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
		disableForm(true);
		var post_data = {
			'user_id': <?php echo $data->id;?>
		};
		
		var name = <?php echo '"' . $data->name . " " . $data->surname . '"';?>;

		$.ajax({
			type: "POST",
			url: "<?php echo base_url('admin/unban_user'); ?>",
			data: post_data,
			success: function(data) {
				disableForm(false);
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
		disableForm(true);
		var post_data = {
			'user_id': <?php echo $data->id;?>
		};
		
		var name = <?php echo '"' . $data->name . " " . $data->surname . '"';?>;

		$.ajax({
			type: "POST",
			url: "<?php echo base_url('admin/delete_user'); ?>",
			data: post_data,
			success: function(data) {
				disableForm(false);
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

<form class="form-horizontal" id="form">
	<div class="form-group">
		<label class="control-label col-xs-3" for="email_input">Email:</label>
		<div class="col-xs-9">
			<input type="email" class="form-control" id="email_input" placeholder="Email" value=<?php echo $data->email;?>>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-xs-3">Name:</label>
		<div class="col-xs-4">
			<input type="text" class="form-control" id="name_input" placeholder="First Name" value=<?php echo $data->name;?>>
		</div>
		<div class="col-xs-5">
			<input type="text" class="form-control" id="surname_input" placeholder="Last Name" value=<?php echo $data->surname;?>>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-xs-3" for="phone_number_input">Phone:</label>
		<div class="col-xs-9">
			<input type="tel" class="form-control" id="phone_number_input" placeholder="Phone Number" value=<?php echo $data->phone_number;?>>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-xs-3" for="address_street_input">Address:</label>
		<div class="col-xs-9">
			<textarea rows="3" class="form-control" id="address_street_input" placeholder="Postal Address"><?php echo $data->address_street;?></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-xs-3" for="address_postal_code_input">Zip Code:</label>
		<div class="col-xs-9">
			<input type="text" class="form-control" id="address_postal_code_input" placeholder="Zip Code" value=<?php echo $data->address_postal_code;?>>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-xs-3" for="student_number_input">Student ID:</label>
		<div class="col-xs-9">
			<input type="text" class="form-control" id="student_number_input" placeholder="Student ID" value=<?php echo $data->student_number;?>>
		</div>
	</div>
</form>
<div class="well" id="toolbar">
	<div class="btn-toolbar">
		<a href="javascript:saveData();" type="button" class="btn btn-success">
			<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Save
		</a>
		<div class="btn-group">
			<a type="button" class="btn btn-info">
				<span class="glyphicon glyphicon-education" aria-hidden="true"></span> Levels
			</a>
			<a type="button" class="btn btn-info">
				<span class="glyphicon glyphicon-lock" aria-hidden="true"></span> Groups
			</a>
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
