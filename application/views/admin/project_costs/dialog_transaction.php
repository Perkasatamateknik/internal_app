<?php
/* User Roles view
*/
?>
<?php $session = $this->session->userdata('username'); ?>
<?php $get_animate = $this->Xin_model->get_content_animate(); ?>
<?php $role_resources_ids = $this->Xin_model->user_role_resource(); ?>
<?php $user_info = $this->Xin_model->read_user_info($session['user_id']); ?>
<?php $vresult = $this->Vendor_model->gel_all_vendor()->result(); ?>
<?php $presult = $this->Product_model->gel_all_product()->result(); ?>
<?php $presult = $this->Project_model->get_projects()->result(); ?>
<?php $system_setting = $this->Xin_model->read_setting_info(1); ?>
<?php

// reports to 
// reports to 
$reports_to = get_reports_team_data($session['user_id']); ?>
<div id="smartwizard-2" class="smartwizard-example sw-main sw-theme-default">
	<ul class="nav nav-tabs step-anchor">
		<?php if (in_array('470', $role_resources_ids) && $user_info[0]->user_role_id == 1) { ?>
			<li class="nav-item clickable"> <a href="<?php echo site_url('admin/project_costs/dashboard/'); ?>" data-link-data="<?php echo site_url('admin/cost/dashboard/'); ?>" class="mb-3 nav-link hrsale-link"> <span class="sw-done-icon ion ion-md-speedometer"></span> <span class="sw-icon ion ion-md-speedometer"></span> <?php echo $this->lang->line('ms_cost_dashboard'); ?>
					<div class="text-muted small"><?php echo $this->lang->line('ms_cost_dashboard'); ?></div>
				</a> </li>
		<?php } ?>
		<?php if (in_array('473', $role_resources_ids) || $reports_to > 0) { ?>
			<li class="nav-item clickable"> <a href="<?php echo site_url('admin/project_costs/transactions'); ?>" data-link-data="<?php echo site_url('admin/project_costs/transactions/'); ?>" class="mb-3 nav-link hrsale-link"> <span class="sw-done-icon fas fa-money-bill-wave"></span> <span class="sw-icon fas fa-money-bill-wave"></span> <?php echo $this->lang->line('ms_project_trans'); ?>
					<div class="text-muted small"><?php echo $this->lang->line('xin_set_up'); ?> <?php echo $this->lang->line('ms_project_trans'); ?></div>
				</a> </li>
		<?php } ?>
		<?php if (in_array('478', $role_resources_ids) || $reports_to > 0) { ?>
			<li class="nav-item clickable"> <a href="<?php echo site_url('admin/vendors/'); ?>" data-link-data="<?php echo site_url('admin/vendors/'); ?>" class="mb-3 nav-link hrsale-link"> <span class="sw-done-icon fas fa-user-friends"></span> <span class="sw-icon fas fa-user-friends"></span> <?php echo $this->lang->line('ms_vendors'); ?>
					<div class="text-muted small"><?php echo $this->lang->line('xin_set_up'); ?> <?php echo $this->lang->line('ms_vendors'); ?></div>
				</a> </li>
		<?php } ?>
		<?php if (in_array('482', $role_resources_ids) || $reports_to > 0) { ?>
			<li class="nav-item clickable"> <a href="<?php echo site_url('admin/products/'); ?>" data-link-data="<?php echo site_url('admin/products/'); ?>" class="mb-3 nav-link hrsale-link"> <span class="sw-done-icon fas fa-boxes"></span> <span class="sw-icon fas fa-boxes"></span> <?php echo $this->lang->line('ms_products'); ?>
					<div class="text-muted small"><?php echo $this->lang->line('xin_set_up'); ?> <?php echo $this->lang->line('ms_products'); ?></div>
				</a> </li>
		<?php } ?>
		<?php if (in_array('490', $role_resources_ids) || $reports_to > 0) { ?>
			<li class="nav-item clickable"> <a href="<?php echo site_url('admin/product_categories/sub'); ?>" data-link-data="<?php echo site_url('admin/product_categories/sub'); ?>" class="mb-3 nav-link hrsale-link"> <span class="sw-done-icon fas fa-tags"></span> <span class="sw-icon fas fa-tags"></span> <?php echo $this->lang->line('ms_product_sub_categories'); ?>
					<div class="text-muted small"><?php echo $this->lang->line('xin_set_up'); ?> <?php echo $this->lang->line('ms_product_sub_categories'); ?></div>
				</a> </li>
		<?php } ?>
		<?php if (in_array('486', $role_resources_ids) || $reports_to > 0) { ?>
			<li class="nav-item clickable"> <a href="<?php echo site_url('admin/product_categories/'); ?>" data-link-data="<?php echo site_url('admin/product_categories'); ?>" class="mb-3 nav-link hrsale-link"> <span class="sw-done-icon fas fa-cogs"></span> <span class="sw-icon fas fa-cogs"></span> <?php echo $this->lang->line('ms_product_categories'); ?>
					<div class="text-muted small"><?php echo $this->lang->line('xin_set_up'); ?> <?php echo $this->lang->line('ms_product_categories'); ?></div>
				</a> </li>
		<?php } ?>
	</ul>
</div>

<hr class="border-light m-0 mb-3">
<div class="card <?php echo $get_animate; ?>">
	<div class="card-header with-elements">
		<a class="card-header-title" href=" <?= base_url('admin/project_costs/transactions'); ?>"><i class="fas fa-arrow-left"> </i> <?php echo $this->lang->line('ms_go_back'); ?> </a> &nbsp;
		<div class="card-header-elements ml-md-auto">
			<span class="card-header-title mr-2"><strong> <?php echo $this->lang->line('ms_project_trans_edit'); ?> &nbsp;</strong> #<?= strtoupper($record->invoice_number); ?></span>

		</div>
	</div>
	<div class="card-body">
		<div class="row m-b-1">
			<div class="col-md-12">
				<?php $attributes = array('name' => 'ed_transactions', 'id' => 'ed_transactions', 'autocomplete' => 'off', 'class' => 'm-b-1 add'); ?>
				<?php $hidden = array('transactions' => 'UPDATE'); ?>
				<?php echo form_open('admin/project_costs/update_transaction', $attributes, $hidden); ?>
				<input type="hidden" class="items-sub-total" name="items_sub_total" value="0" />
				<input type="hidden" class="items-tax-total" name="items_tax_total" value="0" />
				<input type="hidden" class="project_cost_id" name="project_cost_id" value="<?= $record->project_cost_id; ?>" />
				<div class="form-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="ms_vendor" class="control-label"><?php echo $this->lang->line('ms_vendor_name'); ?></label>
								<select class="form-control" name="vendor" data-plugin="select_hrm" data-placeholder="<?php echo $this->lang->line('ms_vendor_name'); ?>">
									<option value=""></option>
									<?php foreach ($vresult as $vendor) { ?>
										<option value="<?= $vendor->vendor_id ?>" <?= $record->vendor_id == $vendor->vendor_id ? 'selected' : ''; ?>> <?php echo $vendor->vendor_name ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="invoice_date"><?php echo $this->lang->line('xin_invoice_number'); ?></label>
								<input class="form-control" placeholder="<?php echo $this->lang->line('xin_invoice_number'); ?>" name="invoice_number" type="text" value="<?php echo $record->invoice_number ?>">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="ms_vendor" class="control-label"><?php echo $this->lang->line('ms_status'); ?></label>
								<select class="form-control" name="status" data-plugin="select_hrm" data-placeholder="<?php echo $this->lang->line('ms_status'); ?>" onchange="getPrepayment(this)">
									<option value="0" <?= $record->status == 0 ? 'selected' : ''; ?>><?= $this->lang->line('ms_status_pending') ?></option>
									<option value="1" <?= $record->status == 1 ? 'selected' : ''; ?>><?= $this->lang->line('ms_status_prepayment') ?></option>
									<option value="2" <?= $record->status == 2 ? 'selected' : ''; ?>><?= $this->lang->line('ms_status_paid') ?></option>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="invoice_date"><?php echo $this->lang->line('xin_invoice_date'); ?></label>
								<input class="form-control date" placeholder="<?php echo $this->lang->line('xin_invoice_date'); ?>" readonly="readonly" name="invoice_date" type="text" value="<?php echo date('Y-m-d', strtotime($record->invoice_date)) ?>">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="invoice_date"><?php echo $this->lang->line('xin_invoice_due_date'); ?></label>
								<input class="form-control date" placeholder="<?php echo $this->lang->line('xin_invoice_due_date'); ?>" readonly="readonly" name="invoice_due_date" type="text" value="" id="invoice_due_date" value="<?php echo date('Y-m-d', strtotime($record->invoice_due_date)) ?>">
							</div>
						</div>
						<div class=" col-md-6">
							<div class="form-group">
								<label for="select_due_date" class="control-label"><?php echo $this->lang->line('xin_select'); ?> <?php echo $this->lang->line('xin_invoice_due_date'); ?></label>
								<select class="form-control" name="select_due_date" id="select_due_date" data-plugin="select_hrm" data-placeholder="<?php echo $this->lang->line('xin_invoice_due_date'); ?>">
									<option value="0" selected disabled><?= $this->lang->line('ms_custom'); ?></option>
									<?php $due_date = [10, 15, 30, 60, 90, 180, 360];

									foreach ($due_date as $d) { ?>
										<option value="<?= $d; ?>" <?= $d == $record->due_date_type ? 'selected' : ''; ?>><?php if (in_array($d, [10, 15, 30])) {
																																echo $d . " " . $this->lang->line('xin_day');
																															} else {
																																echo $d / 30 . " " . $this->lang->line('ms_month');
																															} ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-md-6" id="ms_prepayment">
							<?php if ($record->status == 1) { ?>
								<div class="form-group">
									<label for="ms_prepayment"> <?php echo $this->lang->line('ms_prepayment'); ?></label>
									<input class="form-control" placeholder="<?php echo $this->lang->line('ms_prepayment'); ?>" name="prepayment" type="number" value="<?= $record->prepayment; ?>">
								</div>
							<?php }; ?>
						</div>

					</div>

					<div class="row">
						<div class="col-md-12">
							<div class="form-group overflow-hidden1">
								<div class="col-xs-12">
									<button type="button" data-repeater-create="" class="btn btn-primary" id="add-invoice-item" onclick="addRow()"> <i class="fa fa-plus"></i> <?php echo $this->lang->line('xin_title_add_item'); ?></button>
								</div>
							</div>
							<hr>
							<?php
							$ar_sc = explode('- ', $system_setting[0]->default_currency_symbol);
							$sc_show = $ar_sc[1];
							?>
							<div class="table-responsive">
								<table class="datatables-demo table table-striped" id="item_product">
									<thead class="thead-light">
										<tr>
											<!-- <th>No</th> -->
											<th style="min-width:200px"><?php echo $this->lang->line('xin_title_item'); ?></th>
											<th><?php echo $this->lang->line('xin_projects'); ?></th>
											<th><?php echo $this->lang->line('xin_title_taxes'); ?></th>
											<th><?php echo $this->lang->line('xin_title_tax_rate'); ?></th>

											<th><?php echo $this->lang->line('ms_discount_title'); ?></th>
											<th><?php echo $this->lang->line('ms_discount_value'); ?></th>
											<th><?php echo $this->lang->line('xin_title_qty_hrs'); ?></th>
											<th><?php echo $this->lang->line('xin_title_unit_price'); ?></th>
											<th><?php echo $this->lang->line('xin_title_sub_total'); ?></th>
											<th class="text-center"><?php echo $this->lang->line('xin_action'); ?></th>
										</tr>
									</thead>
									<tbody id="formRow">
										<?php foreach ($res_products as $i => $r) { ?>
											<tr id="row-<?= $i ?>" class="item-row">
												<td>
													<input type="hidden" name="recently_id[]" value="<?= $r->recently_id; ?>">
													<input type="hidden" name="product_id[]" value="<?= $r->product_id; ?>">
													<input type="hidden" name="sub_category_id[]" value="<?= $r->sub_category_id; ?>">
													<input type="text" class="form-control form-control-sm item_name" name="item_name[]" id="item_name" placeholder="Item Name" value="<?= $r->product_name ?>">
												</td>
												<td>
													<select class="form-control form-control-sm" name="project_id[]" id="project_id">'
														<?php foreach ($all_projects as $p) { ?>
															<option value="<?php echo $p->project_id; ?>" <?= $r->project_id == $p->project_id ? 'selected' : ''; ?>> <?php echo $p->title; ?></option>
														<?php } ?>
													</select>
												</td>
												<td>
													<select class="form-control form-control-sm tax_type" name="tax_type[]" id="tax_type">
														<?php foreach ($all_taxes as $_tax) { ?>
															<?php
															if ($_tax->type == 'percentage') {
																$_tax_type = $_tax->rate . '%';
															} else {
																$_tax_type = $this->Xin_model->currency_sign($_tax->rate);
															}
															?>
															<option tax-type="<?php echo $_tax->type; ?>" tax-rate="<?php echo $_tax->rate; ?>" value="<?php echo $_tax->tax_id; ?>" <?= $r->tax_id == $_tax->tax_id ? 'selected' : ''; ?>> <?php echo $_tax->name; ?> (<?php echo $_tax_type; ?>)</option>
														<?php } ?>
													</select>
												</td>
												<td><input type="number" readonly="readonly" class="form-control form-control-sm tax-rate-item" name="tax_rate_item[]" value="<?= $r->tax_rate; ?>" /></td>
												<td>
													<select class="form-control form-control-sm discount_type" name="discount_type[]" id="tax_type">
														<?php foreach ($all_discount as $ad) { ?>
															<?php
															if ($ad->discount_type === 0) {
																$discount_type = $ad->discount_value . '%';
															} else {
																$discount_type = $this->Xin_model->currency_sign($ad->discount_value);
															}
															?>
															<option discount-type="<?php echo $ad->discount_type; ?>" dicount-rate="<?php echo $ad->discount_value; ?>" value="<?php echo $ad->discount_id; ?>" <?= $ad->discount_id == $r->discount_id ? 'selected' : ''; ?>> <?php echo $ad->discount_name; ?> (<?php echo $discount_type; ?>)</option>
														<?php } ?>
													</select>
												</td>
												<td><input type="number" readonly="readonly" class="form-control form-control-sm discount-rate-item" name="discount_rate_item[]" value="<?= $r->tax_rate; ?>" /></td>
												<td><input type="number" class="form-control form-control-sm qty" name="qty[]" id="qty" value="<?= $r->qty; ?>"></td>
												<td><input type="number" name="price[]" class="form-control form-control-sm price" value="<?= $r->price; ?>" id="price" /></td>
												<td><input type="number" class="form-control form-control-sm sub-total-item" readonly="readonly" name="sub-total-item[]" value="<?= $r->amount; ?>" /></td>
												<td style=" text-align:center"><button type="button" class="btn icon-btn btn-xs btn-danger waves-effect waves-light remove-item" data-repeater-delete="" onclick="removeRow()"> <span class="fa fa-trash"></span></button></td>
											</tr>
										<?php }; ?>
									</tbody>
									<tfoot>
										<tr>
											<td colspan="8" style="text-align:right"><?php echo $this->lang->line('xin_title_sub_total2'); ?></td>
											<td colspan="2" class="text-xs-right"><?php echo $sc_show; ?> <span class="sub_total number_format"><?= $record->amount; ?></span></td>
										</tr>
										<tr>
											<td colspan="8" style="text-align:right"><?php echo $this->lang->line('xin_title_tax_c'); ?>
												<input type="hidden" class="ftax_total" name="ftax_total" value="0" />
											</td>
											<td colspan="2" class="text-xs-right"><?php echo $sc_show; ?> <span class="tax_total number_format"><?= $record->tax_total; ?></span></td>
										</tr>
										<tr>
											<td colspan="8" style="text-align:right"><?php echo $this->lang->line('xin_amount'); ?></td>
											<td colspan="2">
												<div class="form-group">
													<input type="hidden" class="fgrand_total" name="fgrand_total" value="0" />
													<?php echo $sc_show; ?> <strong class="grand_total text-danger number_format"><?= $record->amount + $record->tax_total; ?></strong>
												</div>
											</td>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>

					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="ref_code"><?php echo $this->lang->line('ms_ref_code'); ?></label>
								<textarea class="form-control" placeholder="<?php echo $this->lang->line('ms_ref_code'); ?>" name="ref_code" rows="3"><?= $record->ref_code; ?></textarea>
							</div>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-md-12">
							<div class="float-left">
								<div class="alert alert-danger" role="alert">
									<h4 class="alert-heading"><?php echo $this->lang->line('xin_title_alert'); ?></h4>

									<hr>
									<p class="mb-0"><?php echo $this->lang->line('ms_trans_info'); ?></p>
									<a name="" id="" class="btn btn-warning btn-sm mt-3" href="<?= current_url(); ?>" role="button"><?php echo $this->lang->line('ms_reload'); ?></a>
								</div>
							</div>
							<div class="form-actions box-footer float-right">
								<button type="submit" class="btn btn-primary"> <i class="far fa-check-square"></i> <?php echo $this->lang->line('xin_save'); ?> </button>
							</div>
						</div>
					</div>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
<style type="text/css">
	.k-in {
		display: none !important;
	}
</style>

<script>
	function getPrepayment(e) {
		var html = '<div class="form-group"><label for="ms_prepayment"> <?php echo $this->lang->line('ms_prepayment'); ?></label><input class="form-control" placeholder="<?php echo $this->lang->line('ms_prepayment'); ?>" name="prepayment" type="number" value="<?= $record->prepayment; ?>"></div>';
		var value = e.value;
		if (value == 1) {
			$('#ms_prepayment').html(html);
		} else {
			$('#ms_prepayment').html('');
		}
	}
</script>