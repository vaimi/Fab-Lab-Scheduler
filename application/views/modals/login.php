<div id="loginModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Log in to System</h4>
			</div>
			<div class="modal-body">
				<form name="login" method="post"
					action="<?php echo base_url();?>user/login">
					<p>Email:</p>
					<p>
						<input type="email" class="form-control" name="email" id="email"
							style="width: 200px;" value="" required="" autofocus=""
							placeholder="Email address" />
					</p>
					<p>Password:</p>
					<p>
						<input type="password" class="form-control" name="password"
							id="password" style="width: 200px;" value="" required=""
							placeholder="Password" />
					</p>
					<p>
						<input type="checkbox" value="remember" name="remember"
							id="remember" /> 
							<label for="remember">Remember</label>
					</p>
					<p>
						<input type="hidden" id='current' name='current' value='<?php echo current_url();?>' />
						<button type="submit" class='btn btn-success'>Log in</button>
					</p>
					<p>Forgot your password? <a href="<?php echo base_url();?>user/forget_password">Reset it</a>.</p>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>

		</div>
	</div>
</div>