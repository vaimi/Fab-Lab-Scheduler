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
	  </tr>
	  <?php }?>
   </tbody>
 
  
</table>
