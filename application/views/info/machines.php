<div class="container">
	<div class="panel-group" id="accordion" role="tablist">
		<?php foreach($machineGroups as $mg): ?>
			<div id="machine_group_<?=$mg['MachineGroupID']?>" class="panel panel-info">
				<div class="panel-heading" role="tab" id="heading_c<?=$mg['MachineGroupID']?>">
					<h4 class="panel-title pull-left">
						<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse_<?=$mg['MachineGroupID']?>">
							<?=$mg['MachineGroupID']?> <?=$mg['Name']?>
						</a>
						-
						<a role="button" href="#" onclick="$('#machine_group_detail_<?=$mg['MachineGroupID']?>').modal('show');return false;"><?=$this->lang->line('fablab_info_machines_table_header')?></a>
					</h4>
					<div class="modal fade" id="machine_group_detail_<?=$mg['MachineGroupID']?>">
						<div class="modal-dialog">
							<div class="modal-content">
							
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title"><?=$mg['Name']?></h4>
								</div>
								<div class="modal-body">
									<?=$mg['Description']?>
								</div>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<div id="collapse_<?=$mg['MachineGroupID']?>" class="panel-collapse collapse" role="tabpanel">
					<table class="table table-hover machine_table table-striped" id="m_table_<?=$mg['MachineGroupID']?>">
						<thead>
							<tr>
								<th class="col-md-1"><?=$this->lang->line('fablab_info_machines_table_column_1')?></th>
								<th class="col-md-9"><?=$this->lang->line('fablab_info_machines_table_column_2')?></th>
							</tr>
						</thead>
						<tbody id="machine_in_group_<?$mg['MachineGroupID'];?>">
							<?php foreach($mg['machines'] as $m):?>
								<tr id="machine_<?=$m->MachineID?>">
									<td><?=$m->MachineID?></td>
									<td><a role="button" href="#" onclick="$('#machine_detail_<?php echo $m->MachineID; ?>').modal('show');return false;"><?=$m->Manufacturer?> <?=$m->Model?></a></td>
									<div class="modal fade" id="machine_detail_<?php echo $m->MachineID; ?>">
										<div class="modal-dialog">
											<div class="modal-content">
											
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title"><?=$m->MachineName?></h4>
												</div>
												<div class="modal-body">
													<div><?=$this->lang->line('fablab_info_machines_details_manufacturer')?><b><?=$m->Manufacturer?></b></div>
													<div><?=$this->lang->line('fablab_info_machines_details_model')?><b><?=$m->Model?></b></div>
													<div><?=$this->lang->line('fablab_info_machines_details_supervision')?><b><?php if ($m->NeedSupervision) echo 'Yes'; else echo 'No'; ?></b></div>
													<hr/>
													<?=$m->Description?>
												</div>
											</div>
										</div>
									</div>
									
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
