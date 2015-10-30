<script src='<?php echo asset_url();?>js/bootstrap-rating.min.js'></script>
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
	
	function resetQuota(amount) {
		amount = typeof amount !== 'undefined' ? amount : -1;
		disableForm(true);
		var post_data = {
			'user_id': <?php echo $basic->id;?>,
			'amount': amount
		};
		
		$.ajax({
			type: "POST",
			url: "<?php echo base_url('admin/set_quota'); ?>",
			data: post_data,
			success: function(data) {
				disableForm(false);
				if (data.length > 0) {
					var message = $.parseJSON(data);
					if (message['success'] == 1) {
						$("#quota_badge").text(message['amount']);
						$("#quota_badge").addClass("animated pulse").one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', 
						function() {
							$(this).removeClass("animated pulse");
						});
						alerter("info", "User " + <?php echo '"' . $basic->name . " " . $basic->surname . '"';?> + " <strong>quota</strong> updated!"); 
					} else {
						alerter("warning", "<strong>Error</strong> while updating user " + <?php echo '"' . $basic->name . " " . $basic->surname . '"';?> + " <strong>quota</strong>!"); 
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

	$('#levelsnone').click(function(){
	  $('.panel-collapse.in')
		.collapse('hide');
	});
	$('#levelsall').click(function(){
	  $('.panel-collapse:not(".in")')
		.collapse('show');
	});
</script>
<div id="user_content">
	<div class="btn-toolbar" id="toolbar">
		<a href="javascript:saveData();" type="button submit" class="btn btn-success">
			<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Save
		</a>
		<a href="javascript:resetQuota();" type="button" class="btn btn-info">
			<span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> Reset quota <span id="quota_badge" class="badge"><?php echo round($basic->quota, 1);?></span>
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
					<label class="control-label col-xs-2" for="email_input">Email:</label>
					<div class="col-xs-8">
						<input type="email" class="form-control" id="email_input" placeholder="Email" value=<?php echo $basic->email;?>>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-2">Name:</label>
					<div class="col-xs-4">
						<input type="text" class="form-control" id="name_input" placeholder="First Name" value=<?php echo $basic->name;?>>
					</div>
					<div class="col-xs-4">
						<input type="text" class="form-control" id="surname_input" placeholder="Last Name" value=<?php echo $basic->surname;?>>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-2" for="phone_number_input">Phone:</label>
					<div class="col-xs-8">
						<input type="tel" class="form-control" id="phone_number_input" placeholder="Phone Number" value=<?php echo $basic->phone_number;?>>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-2">Address:</label>
					<div class="col-xs-4">
						<input type="text" class="form-control" id="address_street_input" placeholder="Postal Address" value=<?php echo $basic->address_street;?>></textarea>
					</div>
					<div class="col-xs-4">
						<input type="text" class="form-control" id="address_postal_code_input" placeholder="Zip Code" value=<?php echo $basic->address_postal_code;?>>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-2" for="student_number_input">Student ID:</label>
					<div class="col-xs-4">
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
            <h4>User machine levels</h4>
			<div class="btn-toolbar" id="toolbar">
				<div class="btn-group">
					<a href="javascript:void(0);" class="btn btn-info" id="levelsall">
						<span class="glyphicon glyphicon-collapse-down" aria-hidden="true"></span> Expand all
					</a>
					<a href="javascript:void(0);" class="btn btn-info" id="levelsnone">
						<span class="glyphicon glyphicon-collapse-up" aria-hidden="true"></span> Collapse all
					</a>
				</div>
			</div>
			<br>
			 
            <form id="level_form" method="post">
				<div class="panel-group" id="accordion" role="tablist">
					<?php foreach($levels as $g_key => $g_value): ?>
					<div class="panel panel-info">
						<div class="panel-heading" role="tab" id="heading_c<?=$g_key;?>">
							<h4 class="panel-title pull-left">
								<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse_<?=$g_key?>">
									<?=$g_value['category']?>
								</a>
							</h4>
							<div class="pull-right machine_level_rating">
								<a id="g_<?=$g_key?>_rating_reset">
									<span class="rating-star glyphicon glyphicon-minus"/>
								</a>
								<input type="hidden" id="g_<?=$g_key?>_rating_setter" class="rating" data-filled="glyphicon glyphicon-star rating-star rating-star-filled" data-empty="glyphicon glyphicon-star-empty rating-star rating-star-empty"/>
								<script>
								$('#g_<?=$g_key?>_rating_reset').click(function(){
									$('#g_<?=$g_key?>_rating_setter').rating('rate', 0);
									$('.g_<?=$g_key?>_rating').rating('rate', 0);
								});
								$(g_<?=$g_key?>_rating_setter).on('change', function () {
									$('.g_<?=$g_key?>_rating').rating('rate', $(g_<?=$g_key?>_rating_setter).rating('rate'));
								});
								</script>
							</div>
							<div class="clearfix"></div>
						</div>
						<div id="collapse_<?=$g_key?>" class="panel-collapse collapse" role="tabpanel">
							<table class="table table-hover machine_table table-striped" id="m_table_<?=$g_key?>">
								<thead>
									<tr>
										<th>MID</th>
										<th>Manufacturer & Model</th>
										<th>Level</th>
									</tr>
								</thead>
								<tbody>
								<?php foreach($g_value['machines'] as $m_key => $m_value):?>
									<tr id="m_<?=$m_key?>">
										<td><?=$m_key?></td>
										<td><?=$m_value['manufacturer']?> <?=$m_value['model']?></td>
										<td>
										<div class="machine_level_rating">
											<a id="m_<?=$m_key?>_rating_reset">
												<span class="rating-star glyphicon glyphicon-minus"/>
											</a>
											<input type="hidden" id="<?=$g_key?>_rating" class="rating g_<?=$g_key?>_rating m_rating" name="<?=$g_key?>" data-filled="glyphicon glyphicon-star rating-star rating-star-filled" data-empty="glyphicon glyphicon-star-empty rating-star rating-star-empty"/>
											<script>
												$('#<?=$m_key?>_rating').rating('rate', <?=$m_value['level']?>);
												$('#m_<?=$m_key?>_rating_reset').click(function(){
													$('#<?=$m_key?>_rating').rating('rate', 0);
												});
											</script>
										</td>
									</tr>
								<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
			</form>
        </div>
	</div>
</div>
