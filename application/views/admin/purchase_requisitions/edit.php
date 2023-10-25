<?php
/* User Roles view
*/
?>
<?php $session = $this->session->userdata('username'); ?>
<?php $get_animate = $this->Xin_model->get_content_animate(); ?>
<?php $role_resources_ids = $this->Xin_model->user_role_resource(); ?>
<?php if (in_array('504', $role_resources_ids)) { ?>
	<div class="card <?php echo $get_animate; ?>">
		<div class="card-header with-elements">
			<span class="card-header-title">
				<a name="" id="" class="btn btn-sm btn-transparent" href="<?= base_url('admin/purchase_requisitions'); ?>" role="button"><i class="fa fa-arrow-left" aria-hidden="true"></i> <?php echo $this->lang->line('ms_title_back'); ?></a>
			</span>
		</div>
		<div class="card-body">
			<div class="row m-b-1">
				<div class="col-md-12">
					<?php $attributes = array('id' => 'purchase_requisitions_edit', 'autocomplete' => 'off', 'class' => 'm-b-1 add', 'enctype' => 'multipart/form-data'); ?>
					<?php $hidden = array('purchase_requisitions' => 'UPDATE', 'pr_number' => $record->pr_number); ?>
					<?php echo form_open('admin/purchase_requisitions/update', $attributes, $hidden); ?>
					<div class="form-body">
						<div class="row mb-3">
							<div class="col-md-6">
								<div class="form-group">
									<label for="pr_number"><?php echo $this->lang->line('ms_requisition_number'); ?></label>
									<input class="form-control" placeholder="<?php echo $this->lang->line('ms_requisition_number'); ?>" type="text" value="<?= $record->pr_number; ?>" readonly required>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="priority_status"><?php echo $this->lang->line('xin_p_priority'); ?></label>
									<select name="priority_status" id="priority_status" class="form-control select" data-plugin="select_hrm" data-placeholder="<?php echo $this->lang->line('xin_p_priority'); ?>" required>
										<!-- <option value="1"><?php echo $this->lang->line('xin_highest'); ?></option> -->
										<option value="1" <?= $record->priority_status == 1 ? "selected" : ""; ?>><?php echo $this->lang->line('xin_high'); ?></option>
										<option value="2" <?= $record->priority_status == 2 ? "selected" : ""; ?>><?php echo $this->lang->line('xin_normal'); ?></option>
										<option value="3" <?= $record->priority_status == 3 ? "selected" : ""; ?>><?php echo $this->lang->line('xin_low'); ?></option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="issue_date"><?php echo $this->lang->line('ms_purchase_issue_date'); ?></label>
									<input class="form-control" placeholder="<?php echo $this->lang->line('ms_purchase_issue_date'); ?>" id="issue_date" name="issue_date" type="date" value="<?= $record->issue_date; ?>" required>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="due_approval_date"><?php echo $this->lang->line('ms_purchase_due_approval_date'); ?></label>
									<input class="form-control" placeholder="<?php echo $this->lang->line('ms_purchase_due_approval_date'); ?>" id="due_approval_date" name="due_approval_date" type="date" value="<?= $record->due_approval_date; ?>" required>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="purpose"><?php echo $this->lang->line('ms_purpose'); ?></label>
									<input class="form-control" placeholder="<?php echo $this->lang->line('ms_purpose'); ?>" id="purpose" name="purpose" type="text" value="<?= $record->purpose; ?>" required>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<h5><?php echo $this->lang->line('ms_purchase_shipping_information'); ?></h5>
								<hr>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="ref_expedition_name"><?php echo $this->lang->line('ms_ref_delivery_name'); ?></label>
									<input class="form-control" placeholder="<?php echo $this->lang->line('ms_ref_delivery_name'); ?>" id="ref_expedition_name" name="ref_expedition_name" type="text" value="<?= $record->ref_expedition_name; ?>">
								</div>
							</div>
						</div>

						<br>
						<div class="row">
							<div class="col-md-12">
								<label class="h5" required><?php echo $this->lang->line('ms_purchase_items'); ?></label>
								<hr>
							</div>
							<div class="col-md-12">
								<div class="table-responsive">
									<table class="table table-striped table-hover table-cell-input" id="item_product">
										<thead class="thead-light">
											<tr>
												<th style="min-width:100px"><?php echo $this->lang->line('xin_title_item'); ?></th>
												<th style="min-width:150px;"><?php echo $this->lang->line('xin_project'); ?></th>
												<th style="min-width:50px"><?php echo $this->lang->line('xin_title_qty'); ?></th>
												<th style="min-width:100px"><?php echo $this->lang->line('ms_ref_title_unit_price'); ?></th>
												<th style="min-width:100px"><?php echo $this->lang->line('xin_title_sub_total'); ?></th>
												<th class="text-center"><?php echo $this->lang->line('xin_action'); ?></th>
											</tr>
										</thead>
										<tbody id="formRow">
										</tbody>
										<tfoot>
											<tr>
												<td>
													<button type="button" data-repeater-create="" class="btn btn-success" id="add-invoice-item" onclick="addRow()"> <i class="fa fa-plus"></i> <?php echo $this->lang->line('xin_title_add_item'); ?></button>
												</td>
											</tr>
										</tfoot>
									</table>
								</div>
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="notes"><?php echo $this->lang->line('ms_notes'); ?></label>
									<textarea class="form-control" placeholder="<?php echo $this->lang->line('ms_notes'); ?>" name="notes" rows="3"><?= $record->notes; ?></textarea>
								</div>
								<!-- <div class="form-group">
									<label for="pr_attachment"><?php echo $this->lang->line('xin_attachment'); ?>s</label>
									<input type="file" class="form-control" name="pr_attachment" id="pr_attachment">
								</div> -->
							</div>
							<div class="col-md-6">
								<table class="table table-borderless">
									<tr>
										<td class="text-right"><strong><?php echo $this->lang->line('ms_ref_delivery_fee'); ?></strong></td>
										<td class="text-right">
											<input type="number" min="0" class="form-control ref_delivery_fee" data-type="currency" name="ref_delivery_fee" value="<?= $record->ref_delivery_fee; ?>" id="ref_delivery_fee">
										</td>
									</tr>
									<tr>
										<td class="text-right"><strong><?php echo $this->lang->line('xin_amount'); ?></strong></td>
										<td class="text-right">
											<input type="hidden" name="amount" value="0" id="amount">
											<strong id="amount_show" class="currency">0</strong>
										</td>
									</tr>
									<tr>
										<td></td>
										<td>
											<button type="submit" class="btn btn-primary btn-block"> <i class="far fa-check-square"></i> <?php echo $this->lang->line('xin_save'); ?> </button>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					<?php echo form_close(); ?>
				</div>
			</div>
		</div>
	</div>

<?php } ?>