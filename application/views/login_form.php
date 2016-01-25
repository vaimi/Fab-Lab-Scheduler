<?php $this->load->view('partials/header'); ?>
 
<?php $this->load->view('partials/menu'); ?>
<div class="container">
	<h4 class="modal-title">Log in to System</h4>
	<hr/>
	<?php foreach ($data as $item):?>
		<div style='color:red;'>- <?php echo $item;?></div>
	<?php endforeach;?>
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

<?php $this->load->view('partials/footer'); ?>