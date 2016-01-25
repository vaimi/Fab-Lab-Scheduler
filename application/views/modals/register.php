<script src="<?php echo asset_url();?>js/validator.min.js"></script>

<div id="registerModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"><?=$this->lang->line('fablab_register_title');?></h4>
			</div>
			<form name="registration" id="registerform2" method="post" action="<?php echo base_url();?>user/registration" onsubmit="return true;">
			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
			<div class="modal-body">
				<div class="form-group required has-feedback">
					<label class="control-label" for="username"><?=$this->lang->line('fablab_register_username_label');?></label>
					<input type="text" data-minlength="5" data-maxlength="100" class="form-control" name="username" id="username" required placeholder="<?=$this->lang->line('fablab_register_username_placeholder');?>" />
					<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
					<span class="help-block"><?=$this->lang->line('fablab_register_username_help');?></span>
				</div>	
				<div class="form-group required has-feedback">
					<label class="control-label" for="email"><?=$this->lang->line('fablab_register_email_label');?></label>
					<input type="email" class="form-control" name="email" id="email" required placeholder="<?=$this->lang->line('fablab_register_email_placeholder');?>" />
					<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
					<span class="help-block"><?=$this->lang->line('fablab_register_email_help');?></span>
				</div>
				<div class="form-group required has-feedback">
					<label class="control-label"><?=$this->lang->line('fablab_register_password_label');?></label>
					<div class="row">
						<div class="col-sm-6">
							<input type="password" data-minlength="5" data-maxlength="100" class="form-control" name="first_password" id="first_password" value="" required placeholder="<?=$this->lang->line('fablab_register_password_placeholder');?>" />
							<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
							<span class="help-block"><?=$this->lang->line('fablab_register_password_help');?></span>
						</div>
						<div class="col-sm-6">
							<input type="password" class="form-control" name="second_password" id="second_password" data-match="#first_password" data-match-error="<?=$this->lang->line('fablab_register_password_confirm_error_placeholder');?>" value="" required placeholder="<?=$this->lang->line('fablab_register_password_confirm_placeholder');?>" />
							<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
							<div class="help-block with-errors"></div>
						</div>
					</div>
				</div>
				<div class="form-group required has-feedback">
					<label class="control-label"><?=$this->lang->line('fablab_register_name_label');?></label>
					<div class="row">
						<div class="col-sm-6">
							<input type="text" class="form-control" name="first_name" id="first_name" placeholder="<?=$this->lang->line('fablab_register_firstname_placeholder');?>" required/>
							<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
						</div>
						<div class="col-sm-6">
							<input type="text" class="form-control" name="surname" id="surname" placeholder="<?=$this->lang->line('fablab_register_surname_placeholder');?>" required/>
							<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
						</div>
					</div>
				</div>
				<div class="form-group required has-feedback">
					<label class="control-label" for="phone_number"><?=$this->lang->line('fablab_register_phone_label');?></label>
					<input type="tel" class="form-control" name="phone_number" id="phone_number" required placeholder="<?=$this->lang->line('fablab_register_phone_placeholder');?>" />
					<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
				</div>
				<div class="form-group has-feedback">
					<label class="control-label" for="address_street"><?=$this->lang->line('fablab_register_address_label');?></label>
					<div class="row">
						<div class="col-sm-6">
							<input type="text" class="form-control" name="address_street" id="address_street" placeholder="<?=$this->lang->line('fablab_register_address_placeholder');?>" />
							<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
						</div>
						<div class="col-sm-6">
							<input type="text" class="form-control" name="address_postal_code" id="address_postal_code" placeholder="<?=$this->lang->line('fablab_register_zip_placeholder');?>" />
							<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
						</div>
					</div>
				</div>
				<div class="form-group has-feedback">
					<label class="control-label" for="company"><?=$this->lang->line('fablab_register_company_label');?></label>
					<input type="text" class="form-control" name="company" id="company" placeholder="<?=$this->lang->line('fablab_register_company_placeholder');?>" />
					<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
				</div>
				<div class="form-group has-feedback">
					<label class="control-label" for="student_number"><?=$this->lang->line('fablab_register_studentid_label');?></label>
					<input type="text" class="form-control" name="student_number" id="student_number" placeholder="<?=$this->lang->line('fablab_register_studentid_placeholder');?>" />
					<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class='btn btn-primary' text="Register" ><?=$this->lang->line('fablab_register_button_register');?></button>
				<button type="button" class="btn btn-default" data-dismiss="modal"><?=$this->lang->line('fablab_register_button_close');?></button>
			</div>
			</form>
			<script>
			$('#registerform2').validator().on('submit', function (e) {
			    if (e.isDefaultPrevented()) {
			    	alert('form is not valid');
			    } else {
			        
			    }
			});
			</script>
		</div>
	</div>
</div>
