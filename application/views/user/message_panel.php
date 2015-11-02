<script>
	function get_conversation(conversation_id)
	{
		$.ajax({
			type: "POST",
			url: "<?php echo base_url('user/get_conversation'); ?>/" + conversation_id,
			success: function(data) 
			{
				if (data.length > 0) 
				{
					$('#messages_panel').html(data);
				}
			}
		});
	}
</script>

<?php if (count($conversations) > 0) {?>
<script>
	onload: setTimeout('get_conversation(<?php echo $conversations[0]['other_user_id']; ?>);', 300);
</script>
<?php } ?>
<div class="row">
	<div id="conversation_list" class="col-md-3">
		<div class="list-group">
			<?php foreach ($conversations as $conversation) {?>
				<button type="button" onclick="get_conversation(<?php echo $conversation['other_user_id']; ?>);" class="list-group-item"><?php echo $conversation['name'].' '.$conversation['surname']; ?></button>
			<?php } ?>
		</div>
	</div>
	<div id="messages_panel" class="col-md-9">
		
	</div>
</div>