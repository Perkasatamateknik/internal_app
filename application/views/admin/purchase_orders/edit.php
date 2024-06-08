<?php $session = $this->session->userdata('username'); ?>
<?php $get_animate = $this->Xin_model->get_content_animate(); ?>
<?php $role_resources_ids = $this->Xin_model->user_role_resource(); ?>
<?php $user_info = $this->Xin_model->read_user_info($session['user_id']); ?>
<?php if (in_array('511', $role_resources_ids)) { ?>
	<div class="card <?php echo $get_animate; ?>">
		<div class="card-header with-elements">
			<span class="card-header-title">
				<a name="" id="" class="btn btn-sm btn-transparent" href="<?= base_url('admin/purchase_orders'); ?>" role="button"><i class="fa fa-arrow-left" aria-hidden="true"></i> <?php echo $this->lang->line('ms_title_back'); ?></a>
			</span>
		</div>
		<div class="card-body">
			<div class="row m-b-1">
				<div class="col-md-12">
					<?php $attributes = array('name' => 'purchase_orders', 'id' => 'purchase_orders', 'autocomplete' => 'off', 'class' => 'm-b-1 add', 'enctype' => 'multipart/form-data'); ?>
					<?php $hidden = array('purchase_orders' => 'UPDATE', 'po_number' => $record->po_number); ?>
					<?php echo form_open('admin/purchase_orders/update', $attributes, $hidden); ?>
					<div class="form-body">
						<div class="row mb-3">
							<div class="col-md-6">
								<div class="form-group">
									<label for="contact_id" class="control-label"><?php echo $this->lang->line('ms_title_contact'); ?></label><br>
									<select class="form-control" name="contact_id" data-plugin="select_contacts" data-placeholder="<?php echo $this->lang->line('ms_title_select_contact'); ?>" required>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="po_number"><?php echo $this->lang->line('ms_order_number'); ?></label>
									<input class="form-control" placeholder="<?php echo $this->lang->line('ms_order_number'); ?>" type="text" value="<?= $record->po_number; ?>" readonly required>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="warehouse_assign"><?php echo $this->lang->line('ms_warehouse_assign'); ?></label>
									<input class="form-control" placeholder="<?php echo $this->lang->line('ms_warehouse_assign'); ?>" id="warehouse_assign" name="warehouse_assign" type="text" value="<?= $record->warehouse_assign; ?>" required>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="reference"><?php echo $this->lang->line('ms_reference'); ?></label>
									<input class="form-control" placeholder="<?php echo $this->lang->line('ms_reference'); ?>" id="reference" name="reference" type="text" value="<?= $record->reference; ?>">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="transaction_date"><?php echo $this->lang->line('ms_purchase_date'); ?></label>
									<input class="form-control" placeholder="<?php echo $this->lang->line('ms_purchase_date'); ?>" id="date" name="date" type="date" value="<?= $record->date; ?>" required>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="due_date"><?php echo $this->lang->line('xin_invoice_due_date'); ?></label>
									<input class="form-control" placeholder="<?php echo $this->lang->line('xin_invoice_due_date'); ?>" id="due_date" name="due_date" type="date" value="<?= $record->due_date; ?>" required>
								</div>
							</div>
							<?php
							$diff = get_termin($record->date, $record->due_date);
							?>
							<div class=" col-md-4">
								<div class="form-group">
									<label for="select_due_date" class="control-label"><?php echo $this->lang->line('xin_select'); ?> <?php echo $this->lang->line('xin_invoice_due_date'); ?></label>
									<select class="form-control" name="select_due_date" id="select_due_date" data-plugin="select_hrm" data-placeholder="<?php echo $this->lang->line('xin_invoice_due_date'); ?>">
										<option value="0" selected><?= $this->lang->line('ms_custom'); ?></option>
										<option value="1" data-type="days" <?= $record->termin == 1 && $diff['type'] == 'days' ? 'selected' : '' ?>>1 <?= $this->lang->line('xin_day'); ?></option>
										<option value="3" data-type="days" <?= $record->termin == 3 && $diff['type'] == 'days' ? 'selected' : '' ?>>3 <?= $this->lang->line('xin_day'); ?></option>
										<option value="7" data-type="days" <?= $record->termin == 7 && $diff['type'] == 'days' ? 'selected' : '' ?>>7 <?= $this->lang->line('xin_day'); ?></option>
										<option value="10" data-type="days" <?= $record->termin == 10 && $diff['type'] == 'days' ? 'selected' : '' ?>>10 <?= $this->lang->line('xin_day'); ?></option>
										<option value="15" data-type="days" <?= $record->termin == 15 && $diff['type'] == 'days' ? 'selected' : '' ?>>15 <?= $this->lang->line('xin_day'); ?></option>
										<option value="20" data-type="days" <?= $record->termin == 20 && $diff['type'] == 'days' ? 'selected' : '' ?>>20 <?= $this->lang->line('xin_day'); ?></option>
										<option value="1" data-type="months" <?= $record->termin == 1 && $diff['type'] == 'months' ? 'selected' : '' ?>>1 <?= $this->lang->line('xin_month'); ?></option>
										<option value="3" data-type="months" <?= $record->termin == 3 && $diff['type'] == 'months' ? 'selected' : '' ?>>3 <?= $this->lang->line('xin_month'); ?></option>
										<option value="6" data-type="months" <?= $record->termin == 6 && $diff['type'] == 'months' ? 'selected' : '' ?>>6 <?= $this->lang->line('xin_month'); ?></option>
										<option value="9" data-type="months" <?= $record->termin == 9 && $diff['type'] == 'months' ? 'selected' : '' ?>>9 <?= $this->lang->line('xin_month'); ?></option>
										<option value="1" data-type="years" <?= $record->termin == 1 && $diff['type'] == 'years' ? 'selected' : '' ?>>1 <?= $this->lang->line('xin_year'); ?></option>
										<option value="2" data-type="years" <?= $record->termin == 2 && $diff['type'] == 'years' ? 'selected' : '' ?>>2 <?= $this->lang->line('xin_year'); ?></option>
									</select>
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
									<label for="delivery_name"><?php echo $this->lang->line('ms_purchase_ref_delivery_name'); ?></label>
									<input class="form-control" placeholder="<?php echo $this->lang->line('ms_purchase_ref_delivery_name'); ?>" id="delivery_name" name="delivery_name" type="text" value="<?= $record->delivery_name; ?>">
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
												<th style="min-width:100px"><?php echo $this->lang->line('xin_project'); ?></th>
												<th><?php echo $this->lang->line('xin_title_taxes'); ?></th>
												<th><?php echo $this->lang->line('xin_discount'); ?></th>
												<th style="min-width:100px;max-width:200px"><?php echo $this->lang->line('xin_title_qty'); ?></th>
												<th style="min-width:100px"><?php echo $this->lang->line('ms_title_unit_price'); ?></th>
												<th style="min-width:150px" class="text-center"><?php echo $this->lang->line('xin_title_sub_total'); ?></th>
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
							</div>
							<div class="col-md-6">
								<table class="table table-borderless">
									<tr>
										<td class="text-right"><strong><?php echo $this->lang->line('ms_delivery_fee'); ?></strong></td>
										<td class="text-right">
											<input type="number" min="0" class="form-control delivery_fee" name="delivery_fee" value="<?= $record->delivery_fee ?? 0; ?>" id="delivery_fee">
										</td>
									</tr>
									<tr>
										<td class="text-right"><strong><?php echo $this->lang->line('xin_amount'); ?></strong></td>
										<td class="text-right">
											<input type="hidden" name="amount" value="<?= $record->amount; ?>" id="amount">
											<strong id="amount_show" class="currency"><?= $this->Xin_model->currency_sign($record->amount) ?></strong>
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