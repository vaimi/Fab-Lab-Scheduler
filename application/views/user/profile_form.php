<div id="user_content">
	<div id="tab-content" class="tab-content">
		<div class="tab-pane active" id="basic">
			<form class="form-horizontal" id="basic_form">
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
				<div class="form-group">
					<label class="control-label col-xs-2" for="email_input"><?=$this->lang->line('fablab_profile_user_email');?></label>
					<div class="col-xs-8">
						<input type="email" class="form-control" id="email_input"
							placeholder="<?=$this->lang->line('fablab_profile_user_email_placeholder');?>" readonly
							value="<?php echo $email;?>" >
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-2" for="first_password"><?=$this->lang->line('fablab_profile_user_password');?></label>
					<div class="col-xs-8">
						<input type="password" class="form-control" id="first_password" name="first_password"
							placeholder="<?=$this->lang->line('fablab_profile_user_password_placeholder');?>">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-2" for="second_password"><?=$this->lang->line('fablab_profile_user_password_confirm');?></label>
					<div class="col-xs-8">
						<input type="password" class="form-control" id="second_password" name="second_password"
							placeholder="<?=$this->lang->line('fablab_profile_user_password_confirm_placeholder');?>">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-2"><?=$this->lang->line('fablab_profile_user_username');?></label>
					<div class="col-xs-8">
						<input type="text" class="form-control" id="name_input"
							placeholder="<?=$this->lang->line('fablab_profile_user_username_placeholder');?>" readonly
							value="<?php echo $name; ?>" >
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-2"><?=$this->lang->line('fablab_profile_user_firstname');?></label>
					<div class="col-xs-8">
						<input type="text" class="form-control" id="first_name_input"
							placeholder="<?=$this->lang->line('fablab_profile_user_firstname_placeholder');?>"
							value="<?php echo $first_name; ?>" >
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-2"><?=$this->lang->line('fablab_profile_user_surname');?></label>
					<div class="col-xs-8">
						<input type="text" class="form-control" id="surname_input"
							placeholder="<?=$this->lang->line('fablab_profile_user_surname_placeholder');?>"
							value="<?php echo $surname; ?>" >
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-2" for="phone_number_input"><?=$this->lang->line('fablab_profile_user_phone');?></label>
					<div class="col-xs-8">
						<input type="tel" class="form-control" id="phone_number_input"
							placeholder="<?=$this->lang->line('fablab_profile_user_phone_placeholder');?>"
							value="<?php echo $phone_number;?>" >
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-2"><?=$this->lang->line('fablab_profile_user_address');?></label>
					<div class="col-xs-4">
						<input type="text" class="form-control" id="address_street_input"
							placeholder="<?=$this->lang->line('fablab_profile_user_address_placeholder');?>"
							value="<?php echo $address_street;?>" >
					</div>
					<div class="col-xs-4">
						<input type="text" class="form-control"
							id="address_postal_code_input" placeholder="<?=$this->lang->line('fablab_profile_user_postal_placeholder');?>"
							value="<?php echo $address_postal_code;?>" >
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-2" for="student_number_input"><?=$this->lang->line('fablab_profile_user_studentid');?></label>
					<div class="col-xs-4">
						<input type="text" class="form-control" id="student_number_input"
							placeholder="<?=$this->lang->line('fablab_profile_user_studentid_placeholder');?>"
							value="<?php echo $student_number; ?>" >
					</div>
				</div>
				<a href="javascript:update_user();" type="submit"
					class="btn btn-success"> <span
					class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
					<?=$this->lang->line('fablab_profile_user_save');?>
				</a>
			</form>
		</div>
	</div>
</div>
<script>
function update_user(user_id) {			
	var post_data = {
		'email': $('#email_input').val(),
		'name': $('#name_input').val(),
		'first_name': $('#first_name_input').val(),
		'surname': $('#surname_input').val(),
		'phone_number': $('#phone_number_input').val(),
		'address_street': $('#address_street_input').val(),
		'address_postal_code': $('#address_postal_code_input').val(),
		'student_number': $('#student_number_input').val(),
		'first_password': $('#first_password').val(),
		'second_password': $('#second_password').val(),
		'csrf_test_name': csrf_token
	};
	$('#user_data_form').fadeOut('fast');
	$.ajax({
		type: "POST",
		url: "<?php echo base_url('user/update_profile'); ?>",
		data: post_data,
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