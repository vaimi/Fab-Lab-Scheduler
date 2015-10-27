<script src='<?php echo asset_url();?>js/bootstrap-notify.min.js'></script>
<link rel='stylesheet' href='<?php echo asset_url();?>css/animate.css' />
<script type="text/javascript">
		onload: ajaxSearch();
        function ajaxSearch() {
            var input_data = $('#search_people').val();

			var post_data = {
				'search_data': input_data
			};

			$.ajax({
				type: "POST",
				url: "<?php echo base_url('admin/user_search'); ?>",
				data: post_data,
				success: function(data) {
					// return success
					if (data.length > 0) {
						$('#search_results').addClass('auto_list');
						$('#search_results').html(data);
					}
				}
			});

        }
		
		function fetchUserData(user_id) {			
			var post_data = {
				'user_id': user_id
			};
			$('#user_data_form').addClass("animated fadeOut")
			$.ajax({
				type: "POST",
				url: "<?php echo base_url('admin/fetch_user_data'); ?>",
				data: post_data,
				success: function(data) {
					// return success
					if (data.length > 0) {
						$('#user_data_form').removeClass("animated fadeOut");
						$('#user_data_form').html(data);
					}
				}
			});
		}
		
		$(document).on('click', '#search_results a', function() {
		   $("#search_results a").removeClass("active");
		   $(this).addClass("active");
		});
</script>
<div class="container">
	<div class="row">
		<div class="col-md-6">
			<h2>Search for user</h2>
			<div class="row">
				<div class="col-md-12">
					<input type="text" class="form-control input-lg" id="search_people" placeholder="Search by email, name, phone..." onkeyup="ajaxSearch();">
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<h4>Results</h4>
					<div class="list-group" id="search_results">
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<h2>User data</h2>
			<form class="form-horizontal" id="user_data_form">
			</form>
		</div>
	</div>
</div>