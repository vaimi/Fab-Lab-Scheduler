<script src="<?php echo asset_url();?>js/sortable.min.js"></script>
<div class="container">
	<table class="sortable-theme-bootstrap table table-striped" data-sortable>
		<thead>
			<tr>
				<th data-sorted="true" data-sorted-direction="ascending">ID</th>
				<th>Machine</th>
				<th>Reserved for</th>
				<th data-sortable="false">Actions</th>
			</tr>
		</thead>
		<tbody>
				<?php 
				foreach ($rdata as $row) {
					echo '<tr>';
					echo '<td>'.$row['id'].'</td>';
					echo '<td>'.$row['machine'].'</td>';
					echo '<td>'.$row['reserved'].'</td>';
					echo '<td><a type="button" class="btn btn-warning" href="#cancelModal" data-toggle="modal" data-reservation-id='.$row['id'].'><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Cancel</a>';
					echo '</tr>';
				}
				?>
		</tbody>
	</table>
</div>
<div id="cancelModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Log in</h4>
      </div>
      <div class="modal-body">
        <p>Some text in the modal.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>