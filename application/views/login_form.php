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
		<p>Email:</p>
		<p>
			<input type="email" class="form-control" name="email" id="email"
				style="width: 200px;" value="<?php echo $email;?>" required="" autofocus=""
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
				id="remember" /> Remember
		</p>
		<p>
			<input type="hidden" id='current' name='current' value='<?php echo current_url();?>' />
			<button type="submit" class='btn btn-success'>Log in</button>
		</p>
	</form>

</div>

<?php $this->load->view('partials/footer'); ?>