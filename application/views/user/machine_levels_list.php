<table class="table">
	<thead>
    	<tr>
            <th>MID</th>
            <th>Machine name</th>
            <th>Skill level</th>
            <th>Manufacturer</th>
            <th>Model</th>
            <th>Description</th>
       </tr>
   </thead>
   <tbody>
   </tbody>
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
  
</table>
