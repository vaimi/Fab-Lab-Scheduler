<table class="table">
	<thead>
    	<tr>
            <th>MID</th>
            <th>Machine name</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Manufacturer</th>
            <th>Model</th>
            <th>Description</th>
       </tr>
   </thead>
   <tbody>
   </tbody>
  <?php foreach ($results as $r) {?>
  <tr>
  	<th scope="row"><?php echo $r['MachineID']?></th>
  	<td><?php echo $r['MachineName']?></td>
  	<td><?php echo $r['StartTime']?></td>
  	<td><?php echo $r['EndTime']?></td>
    <td><?php echo $r['Manufacturer']?></td>
    <td><?php echo $r['Model']?></td>
    <td><?php echo $r['Description']?></td>
  </tr>
  <?php }?>
  
</table>