<script>
	onload: get_user_profile();
	$(document).on('click', '#profile_menu li', function() {
	   $("#profile_menu li").removeClass("active");
	   $(this).addClass("active");
	});
	
	function get_user_profile()
	{
		$('#user_data_form').fadeOut('fast');
		$.ajax({
			type: "POST",
			data: {'csrf_test_name': csrf_token},
			url: "<?php echo base_url('user/get_user_profile'); ?>",
			success: function(data) {
				// return success
				if (data.length > 0) {
					$('#user_data_form').html(data);
					$('#user_data_form').fadeIn('fast');
				}
			}
		});
	}
	function get_machine_levels()
	{
		$('#user_data_form').fadeOut('fast');
		$.ajax({
			type: "POST",
			data: {'csrf_test_name': csrf_token},
			url: "<?php echo base_url('user/get_machine_levels'); ?>",
			success: function(data) {
				// return success
				if (data.length > 0) {
					$('#user_data_form').html(data);
					$('#user_data_form').fadeIn('fast');
				}
			}
		});
	}
	function get_reservations()
	{
		$('#user_data_form').fadeOut('fast');
		$.ajax({
			type: "POST",
			data: {'csrf_test_name': csrf_token},
			url: "<?php echo base_url('user/get_reservations'); ?>",
			success: function(data) {
				// return success
				if (data.length > 0) {
					$('#user_data_form').html(data);
					$('#user_data_form').fadeIn('fast');
				}
			}
		});
	}
	function get_conversations()
	{
		$('#user_data_form').fadeOut('fast');
		$.ajax({
			type: "POST",
			data: {'csrf_test_name': csrf_token},
			url: "<?php echo base_url('user/get_conversations'); ?>",
			success: function(data) {
				// return success
				if (data.length > 0) {
					$('#user_data_form').html(data);
					$('#user_data_form').fadeIn('fast');
				}
			}
		});
	}
</script>

<div class="container">
	<div class="row">
		<div class="col-md-4">
			<div class="col-md-7">
				<ul class="nav nav-pills nav-stacked" id="profile_menu">
					<li class="active"><a href="#" onclick="get_user_profile();"><?=$this->lang->line('fablab_profile_user_title');?></a></li>
					<li><a href="#" onclick="get_machine_levels();"><?=$this->lang->line('fablab_profile_levels_title');?></a></li>
					<li><a href="#" onclick="get_reservations();"><?=$this->lang->line('fablab_profile_reservations_title');?></a></li>
				</ul>
			</div>
		</div>
		<div class="col-md-8" id="user_data_form">
			
		</div>
	</div>
</div>


