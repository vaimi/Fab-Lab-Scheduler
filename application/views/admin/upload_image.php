<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script type="text/javascript" src="<?php echo base_url();?>assets/js/clipboard.min.js"></script>
		<title>Fab Lab Scheduler</title>
	</head>
	<body>
		<?php foreach ($errors as $error) { ?>
		<p style="color:red"><?php echo $error; ?></p>
		<?php }?>
		<form enctype="multipart/form-data" method="post" action="<?php echo base_url();?>admin/upload_image">
			<p>Select image to upload:</p>
		    <p><input type="file" name="fileToUpload" id="fileToUpload"></p>
		    <p><input type="submit" value="Upload Image" name="submit"></p>
		</form>
		<?php if ($upload_file != '') { ?>
		<p><img src="<?php echo base_url();?>assets/images/admin_uploads/<?php echo $upload_file;?>" style="width:320px;" /></p>
		
		<p><input type="text" id="image_url" style="height:30px;" value="<?php echo base_url();?>assets/images/admin_uploads/<?php echo $upload_file;?>" />
		<button class="btn" data-clipboard-target="#image_url">
		    Copy to clipboard
		</button>
		</p>
		<?php } ?>
	</body>
</html>