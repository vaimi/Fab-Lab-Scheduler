<div class="container">
<div class="row">
<?php
foreach ($mdata as $machine) {
	echo '<div class="col-sm-6 col-md-4">';
	echo '	<div class="thumbnail">';
	//echo '		<img src='.asset_url().'images/'.$machine['image'].' alt="Machine image" class="img-rounded">';
	echo '		<div class="caption">';
	echo '			<h3>'.$machine['MachineName'].'</h3>';
	echo '			<p>'.$machine['Description'].'</p>';
	echo '			<p>'.anchor('info/machines/'.$machine['MachineID'], 'Info', 'class="btn btn-success"');
	echo '		</div>';
	echo '	</div>';
	echo '</div>';
}
?>
</div>