<?php
/* User Roles view
*/
?>
<?php $session = $this->session->userdata('username'); ?>
<?php $get_animate = $this->Xin_model->get_content_animate(); ?>
<?php $role_resources_ids = $this->Xin_model->user_role_resource(); ?>
<?php if ($po_number != false) { ?>
	<?php if (in_array('515', $role_resources_ids)) { ?>
		<?php $attributes = array('name' => 'purchase_deliveries', 'id' => 'purchase_deliveries', 'autocomplete' => 'off', 'class' => 'm-b-1 add', 'enctype' => 'multipart/form-data'); ?>
		<?php $hidden = array('purchase_deliveries' => 'INSERT', 'selected_contact' => $record->contact_id); ?>
		<?php echo form_open('admin/purchase_deliveries/insert', $attributes, $hidden); ?>
		<div class="card mb-4 <?php echo $get_animate; ?>">
			<div id="accordion">
				<div class="card-header with-elements"> <span class="card-header-title mr-2"><strong><?php echo $this->lang->line('xin_add_new'); ?></strong> <?php echo $this->lang->line('ms_purchase_orders'); ?></span>
					<div class="card-header-elements ml-md-auto">
						<a class="text-dark collapsed" data-toggle="collapse" href="#add_role_form" aria-expanded="false">
							<!-- <button type="button" class="btn btn-xs btn-primary"> <span class="ion ion-md-add"></span> <?php echo $this->lang->line('xin_add_new'); ?></button> -->
						</a>
					</div>
				</div>
				<div id="add_role_form" class="collapse show add-form <?php echo $get_animate; ?>" data-parent="#accordion" style="">
					<div class="card-body">
						<div class="row m-b-1">
							<div class="col-md-12">
								<div class="form-body">
									<div class="row mb-3">
										<div class="col-md-6">
											<div class="form-group">
												<label for="contact_id" class="control-label"><?php echo $this->lang->line('ms_title_select_contact'); ?></label><br>
												<select class="form-control" name="contact_id" data-plugin="select_contacts" data-placeholder="<?php echo $this->lang->line('ms_title_select_contact'); ?>" required>
												</select>
												<input id="po_number" name="po_number" type="hidden" value="<?= $po_number; ?>">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="pd_number"><?php echo $this->lang->line('ms_delivery_number'); ?></label>
												<input class="form-control" placeholder="<?php echo $this->lang->line('ms_delivery_number'); ?>" id="pd_number" name="pd_number" type="text" value="<?= $pd_number; ?>" readonly required>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="faktur_number"><?php echo $this->lang->line('xin_invoice_number'); ?></label>
												<input class="form-control" placeholder="<?php echo $this->lang->line('xin_invoice_number'); ?>" id="faktur_number" name="faktur_number" type="text" value="<?= $record->faktur_number; ?>" required>
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
												<label for="date"><?php echo $this->lang->line('ms_purchase_date'); ?></label>
												<input class="form-control" placeholder="<?php echo $this->lang->line('ms_purchase_date'); ?>" id="date" name="date" type="date" required>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="reference"><?php echo $this->lang->line('ms_reference'); ?></label>
												<input class="form-control" placeholder="<?php echo $this->lang->line('ms_reference'); ?>" id="reference" name="reference" type="text" required>
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
												<label for="delivery_date"><?php echo $this->lang->line('ms_delivery_date'); ?></label>
												<input class="form-control" placeholder="<?php echo $this->lang->line('ms_delivery_date'); ?>" id="delivery_date" name="delivery_date" type="date" required>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label for="delivery_name"><?php echo $this->lang->line('ms_delivery_name'); ?></label>
												<input class="form-control" placeholder="<?php echo $this->lang->line('ms_delivery_name'); ?>" id="delivery_name" name="delivery_name" value="<?= $record->delivery_name; ?>" type="text" required>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label for="delivery_number"><?php echo $this->lang->line('ms_delivery_number'); ?></label>
												<input class="form-control" placeholder="<?php echo $this->lang->line('ms_delivery_number'); ?>" id="delivery_number" name="delivery_number" type="text" required>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="card">
			<div class="card-body">
				<br>
				<div class="row">
					<div class="col-md-12">
						<label class="h5" required><?php echo $this->lang->line('ms_purchase_items'); ?></label>
						<hr>
					</div>
					<div class="col-md-12">
						<div class="table-responsive">
							<table class="table table-striped table-hover table-cell-input w-100" id="ms_table_itemss">
								<thead class="thead-light">
									<tr>
										<th><?php echo $this->lang->line('xin_title_item'); ?></th>
										<th><?php echo $this->lang->line('xin_project'); ?></th>
										<th><?php echo $this->lang->line('xin_title_qty'); ?></th>
										<th style="max-width:50px"><?php echo $this->lang->line('ms_uoms'); ?></th>
									</tr>
								</thead>
								<tbody id="formRow">
									<?php
									$count = 0;
									foreach ($record_items as $r) {
										$count += $r->quantity;
									?>
										<tr>
											<td style="width: 30%;">
												<?= $r->product_name ?? "--"; ?>
												<input type="hidden" name="row_product_id[]" value="<?= $r->product_id; ?>">
												<input type="hidden" name="row_product_name[]" value="<?= $r->product_name; ?>">
												<input type="hidden" name="row_product_number[]" value="<?= $r->product_number; ?>">
											</td>
											<td style="width: 40%;">
												<?= $r->project_name ?? "--"; ?>
												<input type="hidden" name="row_project_id[]" value="<?= $r->project_id; ?>">
												<input type="hidden" name="row_project_name[]" value="<?= $r->project_name; ?>">

											</td>
											<td style="width: 10%;">
												<input type="number" name="row_quantity[]" value="<?= $r->quantity; ?>" max="<?= $r->quantity; ?>" min="0" class="form-control" readonly>
												<input type="hidden" name="row_original_quantity[]" value="<?= $r->quantity; ?>">

												<!-- <input type="hidden" name="row_tax[]" value="<?= $r->quantity; ?>"> -->
											</td>
											<td style="width: 20%;">
												<?= $r->uom_name ?? "--"; ?>
												<input type="hidden" name="row_uom_id[]" value="<?= $r->uom_id; ?>">
												<input type="hidden" name="row_uom_name[]" value="<?= $r->uom_name; ?>">
											</td>
										</tr>
									<?php }; ?>
								</tbody>
								<tfoot class="tfoot-light">
									<tr>
										<td colspan="2"><b><?php echo $this->lang->line('ms_total_item'); ?></b></td>
										<td colspan="2" class="text-danger"><b><?= $count; ?></b></td>
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
							<textarea class="form-control" placeholder="<?php echo $this->lang->line('ms_notes'); ?>" name="notes" rows="3"></textarea>
						</div>
						<div class="form-group">
							<label for="attachment"><?php echo $this->lang->line('xin_attachment'); ?>s</label>
							<input type="file" class="form-control" name="attachment" id="attachment">
						</div>
					</div>
					<div class="col-md-6">
						<table class="table table-borderless">
							<tr>
								<td class="text-right"><strong><?php echo $this->lang->line('ms_delivery_fee'); ?></strong></td>
								<td class="text-right">
									<input type="number" min="0" class="form-control delivery_fee" name="delivery_fee" value="<?= $record->delivery_fee; ?>" id="delivery_fee" required>
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
		</div>
		<?php echo form_close(); ?>

	<?php } ?>
<?php } else { ?>

	<div class="card <?php echo $get_animate; ?>">
		<div class="card-header with-elements"> <span class="card-header-title mr-2"><strong><?php echo $this->lang->line('xin_list_all'); ?></strong> <?php echo $this->lang->line('ms_purchase_requisitions'); ?></span>
		</div>
		<div class="card-body">
			<div class="box-datatable table-responsive">
				<table class="datatables-demo table table-striped" id="xin_table_purchase_deliveries">
					<thead>
						<tr>
							<th><?php echo $this->lang->line('xin_action'); ?></th>
							<th><?php echo $this->lang->line('ms_purchase_number'); ?></th>
							<th><?php echo $this->lang->line('ms_title_contact'); ?></th>
							<th><?php echo $this->lang->line('ms_date'); ?></th>
							<th><?php echo $this->lang->line('ms_status'); ?></th>
							<th><?php echo $this->lang->line('ms_reference'); ?></th>
							<th style="min-width:100px"><?php echo $this->lang->line('xin_amount'); ?></th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>

<?php }; ?>