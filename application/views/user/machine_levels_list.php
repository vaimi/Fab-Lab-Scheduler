<script type="text/javascript" src="<?=asset_url()?>js/bootstrap-rating.min.js"></script>
<script src="<?php echo asset_url();?>js/sortable.min.js"></script>
<table class="sortable-theme-bootstrap table table-striped" data-sortable>
	<thead>
    	<tr>
            <th data-sorted="true" data-sorted-direction="ascending">MID</th>
            <th>Machine name</th>
            <th>Manufacturer</th>
            <th>Model</th>
            <th>Skill level</th>
       </tr>
   </thead>
   <tbody>
    <?php foreach ($results as $r) {?>
	  <tr>
	  	<td><?php echo $r['mid']?></td>
	  	<td><?php echo $r['machine_name']?></td>
	    <td><?php echo $r['manufacturer']?></td>
	    <td><?php echo $r['model']?></td>
      <td data-value="<?php echo $r['level']?>"><input type="hidden" class="rating" data-readonly value="<?php echo $r['level']?>"></td>
	  </tr>
	  <?php }?>
   </tbody>
 
  
</table>
