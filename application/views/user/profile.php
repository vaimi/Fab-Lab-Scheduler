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

	function get_conversations()
	{
		$('#user_data_form').fadeOut('fast');
		$.ajax({
			type: "POST",
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
		<div class="col-md-2">
			<div class="col-md-12">
				<ul class="nav nav-pills nav-stacked" id="profile_menu">
					<li class="active"><a href="#" onclick="get_user_profile();">Profile</a></li>
					<li><a href="#" onclick="get_conversations();">Messages</a></li>
					<li><a href="#">Reservations</a></li>
				</ul>
			</div>
		</div>
		<div class="col-md-10" id="user_data_form" >
			
		</div>
	</div>
</div>


