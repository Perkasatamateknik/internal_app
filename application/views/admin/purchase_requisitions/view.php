<?php
/* User Roles view
*/
?>
<?php $session = $this->session->userdata('username'); ?>
<?php $get_animate = $this->Xin_model->get_content_animate(); ?>
<?php $role_resources_ids = $this->Xin_model->user_role_resource(); ?>
<?php if (in_array('503', $role_resources_ids)) { ?>
	<div class="card <?php echo $get_animate; ?>">
		<div class="card-header with-elements justify-content-end align-center">
			<span class="card-header-title mr-2 my-0">
				<a name="" id="" class="btn btn-sm btn-transparent pl-2" href="<?= base_url('admin/purchase_requisitions'); ?>" role="button"><i class="fa fa-arrow-left" aria-hidden="true"></i> <?php echo $this->lang->line('ms_title_back'); ?></a>
			</span>
			<div class="ml-md-auto">
				<?php if ($record->purchase_status == 1) {; ?>
					<?php if (in_array('509', $role_resources_ids)) { ?>
						<a href="<?= base_url('admin/purchase_orders?id=' . $record->pr_number); ?>" class="btn btn-sm btn-primary"> <span class="ion ion-md-add"></span> <?php echo $this->lang->line('ms_create_purchase_orders'); ?></a>
					<?php }; ?>
					<?php if (in_array('506', $role_resources_ids)) { ?>
						<button type="button" class="ml-2 btn btn-sm btn-danger" id="btnReject" data-id="<?= $record->pr_number; ?>" data-msg="<?php echo $this->lang->line('ms_confirm_reject_purchase'); ?>"> <span class="ion ion-md-remove-circle"></span> <?php echo $this->lang->line('ms_reject_purchase'); ?></button>
					<?php }; ?>
				<?php }; ?>
				<a href="<?= base_url('admin/purchase_requisitions/print/' . $record->pr_number) ?>" target="_blank" class="ml-2 btn btn-sm btn-info"> <span class="ion ion-md-print"></span> <?php echo $this->lang->line('xin_print'); ?></a>
			</div>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-12">
					<table class="table table-sm table-borderless">
						<tr>
							<td>
								<label><?php echo $this->lang->line('ms_purchase_number'); ?></label><br />
								<strong><?= $record->pr_number; ?></strong>
							</td>
							<td>
								<label><?php echo $this->lang->line('xin_p_priority'); ?></label><br />
								<strong><?= priority_stats($record->priority_status); ?></strong>
							</td>
							<td>
								<label><?php echo $this->lang->line('ms_status'); ?></label><br>
								<strong id="pr_id" data-id="<?= $record->pr_number; ?>"><?= purchase_stats($record->purchase_status); ?></strong>
							</td>

						</tr>
						<tr>
							<td>
								<label><?php echo $this->lang->line('ms_purchase_pic'); ?></label><br />
								<strong><?= $record->added_by; ?></strong>
							</td>
							<td colspan="2">
								<label><?php echo $this->lang->line('xin_department_name'); ?></label><br />
								<strong><?= $record->department; ?></strong>
							</td>
						</tr>
						<tr>
							<td>
								<label><?php echo $this->lang->line('ms_purchase_issue_date');
										?></label><br />
								<strong><?= $this->Xin_model->set_date_format($record->issue_date);
										?></strong>
							</td>
							<td colspan="2">
								<label><?php echo $this->lang->line('ms_purchase_due_approval_date');
										?></label><br />
								<strong><?= $this->Xin_model->set_date_format($record->due_approval_date);
										?></strong>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<label><?php echo $this->lang->line('ms_purpose'); ?></label><br />
								<strong><?= $record->purpose; ?></strong>
							</td>
						</tr>
						<tr>
							<td colspan="3">
								<label><?php echo $this->lang->line('ms_purchase_ref_delivery_name');
										?></label><br />
								<strong><?= $record->ref_expedition_name ?? "--"; ?></strong>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
	<br>
	<div class="card mx-0">
		<div class="card-body">
			<div class="row">
				<div class="col-md-12">
					<table class="table table-striped table w-100" id="ms_table_items">
						<thead>
							<tr>
								<th><?php echo $this->lang->line('xin_id_no'); ?></th>
								<th style="min-width: 20%;"><?php echo $this->lang->line('xin_title_item'); ?></th>
								<th style="min-width: 20%;"><?php echo $this->lang->line('xin_project'); ?></th>
								<th><?php echo $this->lang->line('xin_title_qty'); ?></th>
								<th>
									<?php echo $this->lang->line('ms_ref_title_unit_price'); ?>
								</th>
								<th style="min-width: 150px">
									<?php echo $this->lang->line('xin_title_sub_total'); ?>
								</th>
							</tr>
						</thead>
						<tbody id="formRow">
							<?php foreach ($records as $i => $r) {; ?>
								<tr>
									<td><?= $i += 1; ?></td>
									<td><?= $r[0]; ?></td>
									<td><?= $r[1]; ?></td>
									<td><?= $r[2]; ?></td>
									<td><?= $r[3]; ?></td>
									<td><?= $r[4]; ?></td>
								</tr>
							<?php }; ?>
						</tbody>
					</table>
				</div>
				<div class="col-md-12">
					<br><br>
				</div>
				<div class="col-md-8 col-sm-8">
					<div class="form-group">
						<label for=""><?php echo $this->lang->line('ms_notes'); ?></label>
						<div class="purporse">
							<?= $record->notes; ?>
						</div>
					</div>
					<?php if (!is_null($record->attachment)) { ?>
						<br>
						<div class="row">
							<div class="col-md-4">
								<label for=""><?php echo $this->lang->line('xin_attachment'); ?></label>
								<div class="card">
									<img class="card-img-top" src="<?= base_url('/uploads/purchase/requisitions/' . $record->attachment) ?>" alt="">
									<div class="card-body p-3">
										<span class="clearfix mt-1">
											<span><?php
													$fileSize = filesize('./uploads/purchase/requisitions/' . $record->attachment);
													$formattedSize = size($fileSize);
													echo $formattedSize; ?></span>
											<a href="#" class="btn btn-default btn-sm float-right"><i class="fas fa-cloud-download-alt"></i></a>
										</span>
									</div>
								</div>
							</div>
						</div>
					<?php }; ?>
				</div>
				<div class="col-md-4 col-sm-4">
					<table class="table table-sm table-borderless">
						<tr>
							<th><?= $this->lang->line('ms_delivery_fee'); ?></th>
							<td><strong><?= $this->Xin_model->currency_sign($record->ref_delivery_fee); ?></strong></td>
						</tr>
						<tr>
							<th><?= $this->lang->line('xin_title_total'); ?></th>
							<td><strong class="text-danger"><?= $this->Xin_model->currency_sign($record->amount + $record->ref_delivery_fee); ?></strong></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
	<br>
	<div class="card">
		<div class="card-header">
			<strong><?php echo $this->lang->line('ms_purchase_log'); ?></strong>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-12">
					<div class="table-responsive">
						<table class="table table-striped table" id="ms_table_items">
							<thead>
								<tr>
									<th><?php echo $this->lang->line('ms_purchase_date'); ?></th>
									<th><?php echo $this->lang->line('ms_purchase_pic'); ?></th>
									<th><?php echo $this->lang->line('ms_purchase_origin'); ?></th>
									<th><?php echo $this->lang->line('ms_purchase_number'); ?></th>
									<th><?php echo $this->lang->line('xin_amount'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($log as $key => $value) { ?>
									<tr>
										<td><?= $this->Xin_model->set_date_format($value->date); ?></td>
										<td><?= $value->pic; ?></td>
										<td><?= $value->origin; ?></td>
										<td><?= $value->number; ?></td>
										<td><?= $value->amount; ?></td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php } ?>