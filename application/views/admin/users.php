<script type="text/javascript" src="<?=asset_url()?>js/bootstrap-notify.min.js"></script>
<link rel="stylesheet" href="<?=asset_url()?>css/animate.css"/>
<script type="text/javascript" src="<?=asset_url()?>js/bootstrap-rating.min.js"></script>
<script type="text/javascript" src="<?=asset_url()?>js/fablab_users_managment.js"></script>
<script type="text/javascript" src="<?=asset_url()?>js/fablab_users_search.js"></script>

<script type="text/javascript">
	ajaxSearch();
</script>
<div class="container">
	<div class="row">
		<!-- Left side/top of the user management page -->
		<div class="col-md-4">
			<!-- Search box -->
			<h3>Search for user</h3>
			<div class="row">
				<div class="col-md-12">
					<input type="text" class="form-control input-lg" id="search_people" placeholder="Search by email, name, phone..." onkeyup="ajaxSearch();">
				</div>
			</div>
			<!-- Search result area -->
			<div class="row">
				<div class="col-lg-12">
					<div class="well">
						<h4>Results</h4>
						<div class="list-group" id="search_results">
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Right side/bottom of the user management page -->
		<div class="col-md-8">
			<h3>User data</h3>
			<form class="form-horizontal" id="user_data_form">
			</form>
		</div>
	</div>
</div>