<script>
	function delete_reservation(reservation_id)
	{
		if (confirm('Are you sure you want to delete the reservation?') == false)
			return;

		$.ajax({
			type: "POST",
			url: "<?php echo base_url('user/delete_reservation'); ?>/" + reservation_id,
			dataType: "json",
			success: function(data) 
			{
				window.alert(data.message);
				$('#reservation_' + reservation_id).fadeOut('fast');
			},
			error: function(data)
			{
				window.alert(data.message);
			}
		});
	}
</script>

<script src="<?php echo asset_url();?>js/sortable.min.js"></script>
<table class="sortable-theme-bootstrap table table-striped" data-sortable>
	<thead>
    	<tr>
            <th>MID</th>
            <th>Machine name</th>
            <th data-sorted="true" data-sorted-direction="ascending">Start Time</th>
            <th>End Time</th>
       </tr>
   </thead>
   <tbody>
	    <?php foreach ($results as $r) {?>
	  <tr id="reservation_<?=$r['ReservationID']; ?>">
	  	<td><?php echo $r['MachineID']?></td>
	  	<td><?php echo $r['MachineName']?></td>
	  	<td><?php echo $r['StartTime']?></td>
	  	<td><?php echo $r['EndTime']?></td>
	    <td>
	    	<a type="button" class="btn btn-danger" onclick="delete_reservation(<?=$r['ReservationID']; ?>);">
				<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete
			</a>
	    </td>
	  </tr>
	  <?php }?>
   </tbody>
 
  
</table>
