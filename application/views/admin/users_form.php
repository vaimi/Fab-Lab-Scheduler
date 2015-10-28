<script type="text/javascript">
	onload: banState();
	function disableForm (yes) {
		if (yes) {
			$("#user_content :input").attr('disabled', true);
			$("#tabs > li").addClass('disabled');;
			$("#user_content a").addClass('disabled');
		} else {
			$('#user_content :input').removeAttr('disabled');
			$('#tabs > li').removeClass('disabled');
			$('#user_content a').removeClass('disabled');
		}
	}
	
	function banState() {
		var banned = <?php echo $basic->banned;?>;
		
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
		var post_data = {
			'user_id': <?php echo $basic->id;?>,
			'email': $('#email_input').val(),
			'username': $('#name_input').val(),
			'surname': $('#surname_input').val(),
			'phone_number': $('#phone_number_input').val(),
			'address_street': $('#address_street_input').val(),
			'address_postal_code': $('#address_postal_code_input').val(),
			'student_number': $('#student_number_input').val(),
			'groups' : $(group_form).serialize(),
			'levels' : $(level_form).serialize()
		}
		disableForm(true);
		
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
						$("#search_results > .active").text(post_data['username'] + " " + post_data['surname'])
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
			'user_id': <?php echo $basic->id;?>
		};
		
		var name = <?php echo '"' . $basic->name . " " . $basic->surname . '"';?>;

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
			'user_id': <?php echo $basic->id;?>
		};
		
		var name = <?php echo '"' . $basic->name . " " . $basic->surname . '"';?>;

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
			'user_id': <?php echo $basic->id;?>
		};
		
		var name = <?php echo '"' . $basic->name . " " . $basic->surname . '"';?>;

		$.ajax({
			type: "POST",
			url: "<?php echo base_url('admin/delete_user'); ?>",
			data: post_data,
			success: function(data) {
				disableForm(false);
				if (data.length > 0) {
					ajaxSearch(); //TODO some cleaner way might be better
					$("#user_content").addClass("animated fadeOut").one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', 
					function() {
						$( "#user_content" ).empty();
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
		$('#tab-content .tab-pane').css('height', $('#tab-content .tab-pane').css('height') );
		});

	
</script>
<div id="user_content">
	<div class="btn-toolbar" id="toolbar">
		<a href="javascript:saveData();" type="button submit" class="btn btn-success">
			<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Save
		</a>
		<a href="javascript:saveData();" type="button" class="btn btn-info">
			<span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> Reset quota
		</a>
		<div class="btn-group">
			<a href="javascript:banUser();" type="button" id="ban_button" class="btn btn-warning">
				<span class="glyphicon glyphicon-ban-circle" aria-hidden="true"></span> Ban
			</a>
			<a tabindex="0" data-trigger="focus" role="button" id="remove_button" class="btn btn-danger">
				<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete
			</a>
		</div>
	</div>
	<br>
	<ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
        <li class="active"><a href="#basic" data-toggle="tab">Basic</a></li>
        <li><a href="#groups" data-toggle="tab">Groups</a></li>
        <li><a href="#levels" data-toggle="tab">Levels</a></li>
    </ul>
	<div id="tab-content" class="tab-content">
        <div class="tab-pane active" id="basic">
			<h4>User basic data</h4>
			<form class="form-horizontal" id="basic_form">
				<div class="form-group">
					<label class="control-label col-xs-3" for="email_input">Email:</label>
					<div class="col-xs-9">
						<input type="email" class="form-control" id="email_input" placeholder="Email" value=<?php echo $basic->email;?>>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-3">Name:</label>
					<div class="col-xs-4">
						<input type="text" class="form-control" id="name_input" placeholder="First Name" value=<?php echo $basic->name;?>>
					</div>
					<div class="col-xs-5">
						<input type="text" class="form-control" id="surname_input" placeholder="Last Name" value=<?php echo $basic->surname;?>>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-3" for="phone_number_input">Phone:</label>
					<div class="col-xs-9">
						<input type="tel" class="form-control" id="phone_number_input" placeholder="Phone Number" value=<?php echo $basic->phone_number;?>>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-3" for="address_street_input">Address:</label>
					<div class="col-xs-9">
						<textarea rows="3" class="form-control" id="address_street_input" placeholder="Postal Address"><?php echo $basic->address_street;?></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-3" for="address_postal_code_input">Zip Code:</label>
					<div class="col-xs-9">
						<input type="text" class="form-control" id="address_postal_code_input" placeholder="Zip Code" value=<?php echo $basic->address_postal_code;?>>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-3" for="student_number_input">Student ID:</label>
					<div class="col-xs-9">
						<input type="text" class="form-control" id="student_number_input" placeholder="Student ID" value=<?php echo $basic->student_number;?>>
					</div>
				</div>
			</form>
		</div>
		<div class="tab-pane" id="groups">
            <h4>User groups</h4>
			<form id="group_form" method="post">
				<?php foreach($groups as $group) {
				echo '<div class="checkbox" >';
				echo '	<label>';
				if ($group->in === 0) {
					echo '		<input type="checkbox" name="' . $group->id . '">' . " " . $group->name;
				} else {
					echo '		<input type="checkbox" checked name="' . $group->id . '">' . " " . $group->name;
				}
				echo '	</label>';
				echo '</div>';
				}?>
			</form>
        </div>
		<div class="tab-pane" id="levels">
            <h4>User machine levels</4>
            <form id="level_form" method="post">
			<p></p>
			</form>
        </div>
	</div>
</div
