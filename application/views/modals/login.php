<div id="loginModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"><?=$this->lang->line('fablab_login_title');?></h4>
			</div>
			<div class="modal-body">
				<form name="login" method="post"
					action="<?php echo base_url();?>user/login">
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
					<p><?=$this->lang->line('fablab_login_email_label');?></p>
					<p>
						<input type="email" class="form-control" name="email" id="email"
							style="width: 200px;" value="" required="" autofocus=""
							placeholder="<?=$this->lang->line('fablab_login_email_placeholder');?>" />
					</p>
					<p><?=$this->lang->line('fablab_login_password_label');?></p>
					<p>
						<input type="password" class="form-control" name="password"
							id="password" style="width: 200px;" value="" required=""
							placeholder="<?=$this->lang->line('fablab_login_password_placeholder');?>" />
					</p>
					<p>
						<input type="checkbox" value="remember" name="remember"
							id="remember" /> 
							<label for="remember"><?=$this->lang->line('fablab_login_remember');?></label>
					</p>
					<p>
						<input type="hidden" id='current' name='current' value='<?php echo current_url();?>' />
						<button type="submit" class='btn btn-success'><?=$this->lang->line('fablab_login_button_login');?></button>
					</p>
					<p><?=$this->lang->line('fablab_login_reset');?> <a href="<?php echo base_url();?>user/forget_password"><?=$this->lang->line('fablab_login_button_reset');?></a>.</p>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?=$this->lang->line('fablab_login_button_close');?></button>
			</div>

		</div>
	</div>
</div>