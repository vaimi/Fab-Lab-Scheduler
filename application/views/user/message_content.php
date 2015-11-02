<div class="panel panel-default">
	<div class="panel-body">
		<?php for ($i=0; $i<count($texts); $i++) {?>
			<?php if ($i==0 || $texts[$i]['sender_id'] != $texts[$i-1]['sender_id'] || strtotime($texts[$i]['date_sent']) - strtotime($texts[$i-1]['date_sent']) > 60 ) { ?>
			<div style="margin-left:5px;margin-right:5px;padding: 9px 14px;margin-bottom: 14px;background-color: #f7f7f9;border: 1px solid #e1e1e8;border-radius: 4px;">
			<?php } ?>
				<p><?php echo $texts[$i]['message']; ?> - <?php echo $texts[$i]['date_sent']; ?></p>
			<?php if ($i == count($texts)-1 || ($i < count($texts)-1 && ($texts[$i]['sender_id'] != $texts[$i+1]['sender_id'] || strtotime($texts[$i+1]['date_sent']) - strtotime($texts[$i]['date_sent']) > 60))) {?> 
			</div>
			<?php } ?>
		<?php } ?>
	</div>
	<div class="panel-footer">Panel footer</div>
</div>