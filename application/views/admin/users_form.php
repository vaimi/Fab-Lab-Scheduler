<script type="text/javascript" src="<?=asset_url()?>js/bootstrap-rating.min.js"></script>
<script type="text/javascript">
	var user = {
		"id": <?=$basic->id?>,
		"name": <?php echo '"' . $basic->name . " " . $basic->surname . '"';?>,
		"banned": <?=$basic->banned?>
	};

	$(document).ready(function() {
		$("#remove_button").popover({
			placement: 'bottom',
			html: 'true',
			title : 'Are you sure?',
			content : '<p>Effect is permanent!</p>'+
			'<a href="javascript:deleteUser(user)" role="button" class="btn btn-danger" style="margin: 10px 10px;">' +
			'<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Do it!' +
			'</a>'
		});
		$('#tab-content .tab-pane').css('height', $('#tab-content .tab-pane').css('height') );
	});

	// Toolbar button listeners
	$('#save_button').click(function(){
		saveData(user);
	});

	$('#quota_button').click(function(){
		setQuota(user);
	});

	$('#ban_button').click(function(){
		banUser(user);
	});

	// Levels tab buttons
	$('#levelsnone').click(function(){
		$('.panel-collapse.in')
		.collapse('hide');
	});
	$('#levelsall').click(function(){
		$('.panel-collapse:not(".in")')
		.collapse('show');
	});

	function banState(user) {
		var $contents = "";
		$contents = $('#ban_button').contents();
		if (user.banned == 1) {
			$("#ban_button span").attr("class","glyphicon glyphicon-ok-circle");
			$contents[$contents.length - 1].nodeValue = ' Unban';
		} else {
			$("#ban_button span").attr("class","glyphicon glyphicon-ban-circle");
			$contents[$contents.length - 1].nodeValue = ' Ban';
		}
	}
	banState(user);
</script>
<div id="user_content">
	<!-- Toolbar -->
	<div class="btn-toolbar" id="toolbar">
		<a id="save_button" type="button submit" class="btn btn-success">
			<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Save
		</a>
		<a id="quota_button" type="button" class="btn btn-info">
			<span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> Reset quota <span id="quota_badge" class="badge"><?=round($basic->quota, 1);?></span>
		</a>
		<div class="btn-group">
			<a type="button" id="ban_button" class="btn btn-warning">
				<span class="glyphicon glyphicon-ban-circle" aria-hidden="true"></span> Ban
			</a>
			<a tabindex="0" data-trigger="focus" role="button" id="remove_button" class="btn btn-danger">
				<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete
			</a>
		</div>
	</div>
	<br>
	<!-- Tabs -->
	<ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
		<li class="active"><a href="#basic" data-toggle="tab">Basic</a></li>
		<li><a href="#groups" data-toggle="tab">Groups</a></li>
		<li><a href="#levels" data-toggle="tab">Levels</a></li>
	</ul>
	<!-- Tab content -->
	<div id="tab-content" class="tab-content">
		<!-- Basic info tab -->
		<div class="tab-pane active" id="basic">
			<h4>User basic data</h4>
			<form class="form-horizontal" id="basic_form">
				<div class="form-group">
					<label class="control-label col-md-2" for="email_input">Email:</label>
					<div class="col-md-8">
						<input type="email" class="form-control" id="email_input" placeholder="Email" value=<?=$basic->email;?>>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-2">Name:</label>
					<div class="col-md-4">
						<input type="text" class="form-control" id="name_input" placeholder="First Name" value=<?=$basic->name;?>>
					</div>
					<div class="col-md-4">
						<input type="text" class="form-control" id="surname_input" placeholder="Last Name" value=<?=$basic->surname;?>>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-2">Address:</label>
					<div class="col-md-4">
						<input type="text" class="form-control" id="address_street_input" placeholder="Postal Address" value=<?=$basic->address_street;?>></textarea>
					</div>
					<div class="col-md-4">
						<input type="text" class="form-control" id="address_postal_code_input" placeholder="Zip Code" value=<?php echo $basic->address_postal_code;?>>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-2" for="phone_number_input">Phone:</label>
					<div class="col-md-4">
						<input type="tel" class="form-control" id="phone_number_input" placeholder="Phone Number" value=<?=$basic->phone_number;?>>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-2" for="student_number_input">Student ID:</label>
					<div class="col-md-4">
						<input type="text" class="form-control" id="student_number_input" placeholder="Student ID" value=<?=$basic->student_number;?>>
					</div>
				</div>
			</form>
		</div>
		<!-- Group tab -->
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
		<!-- Levels tab-->
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
													</div>
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
