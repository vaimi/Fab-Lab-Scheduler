<script src="<?php echo asset_url();?>js/sortable.min.js"></script>
<table class="sortable-theme-bootstrap table table-striped" data-sortable>
	<thead>
    	<tr>
            <th data-sorted="true" data-sorted-direction="ascending">MID</th>
            <th>Machine name</th>
            <th>Skill level</th>
            <th>Manufacturer</th>
            <th>Model</th>
            <th>Description</th>
       </tr>
   </thead>
   <tbody>
    <?php foreach ($results as $r) {?>
	  <tr>
	  	<th scope="row"><?php echo $r['mid']?></th>
	  	<td><?php echo $r['machine_name']?></td>
	  	<td><?php echo $r['level']?></td>
	    <td><?php echo $r['manufacturer']?></td>
	    <td><?php echo $r['model']?></td>
	    <td><?php echo $r['description']?></td>
	  </tr>
	  <?php }?>
   </tbody>
 
  
</table>
