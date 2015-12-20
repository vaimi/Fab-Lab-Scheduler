<div class="container">
	<?php if ($action == 'test') { ?>
	<div class="row"><p style="color:red">Test email has been sent to your address, please check your inbox</p></div>
	<?php } else if ($action == 'confirmed') { ?>
	<?php if ($send_all) { ?>
	<div class="row"><p style="color:red">Email has been sent to all users of the website</p></div>
	<?php } else {?>
	<div class="row"><p style="color:red">Email has been sent to selected recipients</p></div>
	<?php }?>
	<?php } ?>
	<form name="login" method="post" action="<?php echo base_url();?>admin/send_emails">
		<div class="row"><p>Email Subject (*)</p></div>
		<div class="row"><p><input type="email_subject" name="email_subject" class="form-control" value="<?php echo $email_subject;?>"/></p></div>
		<div class="row"><p>Recipients:  (To all users: <input type="checkbox" name="send_all" value="send_all" <?php if ($send_all) {echo 'checked';} ?>>)</p> </div>
		<div class="row"><p><textarea id="recipients" class="form-control"  name="recipients" required=""><?php echo $recipients;?></textarea></p></div>
		<div class="row"><p>Email Content (*)</p></div>
		<div class="row"><p><textarea id="email_content" name="email_content" required=""><?php echo $email_content;?></textarea></p></div>
		<input type="hidden" id="action" name="action" value="test" />
			<div class="row"><p>
			<div class="btn-toolbar">
				<button type="submit" class='btn btn-info'>Send test</button>
				<button type="submit" onclick="$('#action').val('confirmed'); return confirm('Do you want to send email?');" class='btn btn-success'>Send email</button>
				<button type="button" class='btn' <?php echo "onclick=\"location.href =", "'" , site_url("admin/send_emails") , "'" , "\""; ?> >Cancel</button>
			</div></div>
			
		</p>
	</form>
</div>

<script>
	$(document).ready(function() {
		$('#email_content').summernote({
			  height: 400,                 // set editor height

			  minHeight: null,             // set minimum height of editor
			  maxHeight: null,             // set maximum height of editor

			  focus: false,                 // set focus to editable area after initializing summernote
			  onImageUpload: function(files, editor, welEditable) {
	                sendFile(files[0], editor, welEditable);
	            }
			});
		function sendFile(file, editor, welEditable) {
            data = new FormData();
            data.append("file", file);
            $.ajax({
                data: data,
                type: "POST",
                url: "<?php echo base_url();?>admin/post_image",
                cache: false,
                contentType: false,
                processData: false,
                success: function(url) {
                	$('#email_content').summernote('editor.insertImage', url.trim());
                	//editor.insertImage(welEditable, url);
                }
            });
        }
	});
</script>