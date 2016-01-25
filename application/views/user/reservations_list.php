<script src="<?php echo asset_url();?>js/sortable.min.js"></script>
<table class="sortable-theme-bootstrap table table-striped" data-sortable>
	<thead>
    	<tr>
            <th><?=$this->lang->line('fablab_profile_reservations_table_mid');?></th>
            <th><?=$this->lang->line('fablab_profile_reservations_table_name');?></th>
            <th data-sorted="true" data-sorted-direction="ascending"><?=$this->lang->line('fablab_profile_reservations_table_start');?></th>
            <th><?=$this->lang->line('fablab_profile_reservations_table_end');?></th>
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
