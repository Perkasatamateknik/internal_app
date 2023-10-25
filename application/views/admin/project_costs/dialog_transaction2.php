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
		<?php if (in_array('486', $role_resources_ids) && $user_info[0]->user_role_id == 1) { ?>
			<li class="nav-item clickable"> <a href="<?php echo site_url('admin/project_costs/dashboard/'); ?>" data-link-data="<?php echo site_url('admin/cost/dashboard/'); ?>" class="mb-3 nav-link hrsale-link"> <span class="sw-done-icon ion ion-md-speedometer"></span> <span class="sw-icon ion ion-md-speedometer"></span> <?php echo $this->lang->line('ms_cost_dashboard'); ?>
					<div class="text-muted small"><?php echo $this->lang->line('ms_cost_dashboard'); ?></div>
				</a> </li>
		<?php } ?>
		<?php if (in_array('487', $role_resources_ids) || $reports_to > 0) { ?>
			<li class="nav-item clickable"> <a href="<?php echo site_url('admin/project_costs/transactions'); ?>" data-link-data="<?php echo site_url('admin/project_costs/transactions/'); ?>" class="mb-3 nav-link hrsale-link"> <span class="sw-done-icon fas fa-money-bill-wave"></span> <span class="sw-icon fas fa-money-bill-wave"></span> <?php echo $this->lang->line('ms_project_trans'); ?>
					<div class="text-muted small"><?php echo $this->lang->line('xin_set_up'); ?> <?php echo $this->lang->line('ms_project_trans'); ?></div>
				</a> </li>
		<?php } ?>
		<?php if (in_array('487', $role_resources_ids) || $reports_to > 0) { ?>
			<li class="nav-item clickable"> <a href="<?php echo site_url('admin/vendors/'); ?>" data-link-data="<?php echo site_url('admin/vendors/'); ?>" class="mb-3 nav-link hrsale-link"> <span class="sw-done-icon fas fa-user-friends"></span> <span class="sw-icon fas fa-user-friends"></span> <?php echo $this->lang->line('ms_vendors'); ?>
					<div class="text-muted small"><?php echo $this->lang->line('xin_set_up'); ?> <?php echo $this->lang->line('ms_vendors'); ?></div>
				</a> </li>
		<?php } ?>
		<?php if (in_array('487', $role_resources_ids) || $reports_to > 0) { ?>
			<li class="nav-item clickable"> <a href="<?php echo site_url('admin/products/'); ?>" data-link-data="<?php echo site_url('admin/products/'); ?>" class="mb-3 nav-link hrsale-link"> <span class="sw-done-icon fas fa-boxes"></span> <span class="sw-icon fas fa-boxes"></span> <?php echo $this->lang->line('ms_products'); ?>
					<div class="text-muted small"><?php echo $this->lang->line('xin_set_up'); ?> <?php echo $this->lang->line('ms_products'); ?></div>
				</a> </li>
		<?php } ?>
		<?php if (in_array('487', $role_resources_ids) || $reports_to > 0) { ?>
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
			<span class="card-header-title mr-2"><strong> <?php echo $this->lang->line('ms_project_trans_edit'); ?> &nbsp;</strong> #<?= strtoupper($record->invoice_id); ?></span>

		</div>
	</div>
	<div class="card-body">
		<div class="row m-b-1">
			<div class="col-md-12">
				<?php $attributes = array('name' => 'transactions', 'id' => 'transactions', 'autocomplete' => 'off', 'class' => 'm-b-1 add'); ?>
				<?php $hidden = array('transactions' => 'INSERT'); ?>
				<?php echo form_open('admin/project_costs/create_transaction', $attributes, $hidden); ?>
				<input type="hidden" class="items-sub-total" name="items_sub_total" value="0" />
				<input type="hidden" class="items-tax-total" name="items_tax_total" value="0" />
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
								<input class="form-control date" placeholder="<?php echo $this->lang->line('xin_invoice_date'); ?>" readonly="readonly" name="invoice_date" type="text" value="<?php echo $record->invoice_date ?>">
							</div>
						</div>
						<div class="col-md-6" id="ms_prepayment">
							<!--  -->
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
								<table class="datatables-demo table table-striped table-hover" id="item_product">
									<!-- <thead class="thead-light">
										<tr>
											<th>No</th>
											<th style="min-width:200px">Data Item</th>
											<th>Pajak</th>
											<th style="min-width:200px">Tarif Pajak</th>
											<th></th>
											<th style="min-width:100px">Qty/Hr</th>
											<th style="min-width:100px">Price</th>
											<th>Subtotal</th>
											<th class="text-center">Action</th>
										</tr>
									</thead> -->
									<tbody id="formRow">
										<?php for ($i = 0; $i < 10; $i++) {  ?>
											<tr style="border-bottom: 2px solid black;">
												<td>
													<input type="hidden" name="product_id[]" value="11">
													<input type="hidden" name="category_id[]" value="0">
													<label for="">Item</label>
													<input type="text" class="form-control item_name" name="item_name[]" id="item_name" placeholder="Item Name">
													<!-- </td>
											<td> -->
													<br>
													<label for="">Project</label>
													<select class="form-control" data-plugin="select_hrm" name="project_id[]" id="project_id">'
														<?php foreach ($all_projects as $p) { ?>
															<option value="<?php echo $p->project_id; ?>"> <?php echo $p->title; ?></option>
														<?php } ?>
													</select>
												</td>
												<td>
													<label for="">Tax type</label>
													<select class="form-control tax_type" data-plugin="select_hrm" name="tax_type[]" id="tax_type">
														<?php foreach ($all_taxes as $_tax) { ?>
															<?php
															if ($_tax->type == 'percentage') {
																$_tax_type = $_tax->rate . '%';
															} else {
																$_tax_type = $this->Xin_model->currency_sign($_tax->rate);
															}
															?>
															<option tax-type="<?php echo $_tax->type; ?>" tax-rate="<?php echo $_tax->rate; ?>" value="<?php echo $_tax->tax_id; ?>"> <?php echo $_tax->name; ?> (<?php echo $_tax_type; ?>)</option>
														<?php } ?>
													</select>
													<!-- </td>
											<td> -->
													<br>
													<label for="">Tax Rate</label>
													<input type="number" readonly="readonly" class="form-control tax-rate-item" name="tax_rate_item[]" value="0" />
												</td>
												<td>
													<label for="">Qty</label><br>
													<input type="number" class="form-control qty" name="qty[]" id="qty" value="1">
												</td>
												<td>
													<label for="">Price</label><br>
													<input type="number" name="price[]" class="form-control price" value="0" id="price" />
												</td>
												<td>
													<label for="">sub-total</label><br>
													<input type="number" class="form-control sub-total-item" readonly="readonly" name="sub-total-item[]" value="0" />
												</td>
												<td style="text-align:center">
													<label for="">Action</label><br>
													<button type="button" class="btn icon-btn btn-danger waves-effect waves-light remove-item" data-repeater-delete="" onclick="removeRow()"> <span class="fa fa-trash"></span></button>
												</td>
											</tr>
										<?php }; ?>

									</tbody>
									<tfoot>
										<tr>
											<td colspan="3"></td>
											<td>
												<div class="form-group">
													<label for=""><strong><?php echo $this->lang->line('xin_discount_type'); ?></strong></label>
													<select name="discount_type" class="form-control discount_type">
														<option value="1"> <?php echo $this->lang->line('xin_flat'); ?></option>
														<option value="2"> <?php echo $this->lang->line('xin_percent'); ?></option>
													</select>
												</div>
											</td>
											<td>
												<div class="form-group">
													<label for=""><strong><?php echo $this->lang->line('xin_discount'); ?></strong></label>
													<input style="text-align:right" type="text" name="discount_figure" class="form-control discount_figure" value="0" data-valid-num="required">
												</div>
											</td>
											<td colspan="1">
												<div class="form-group">
													<label for=""><strong><?php echo $this->lang->line('xin_discount_amount'); ?></strong></label>
													<input type="text" style="text-align:right" readonly="" name="discount_amount" value="0" class="discount_amount form-control">
												</div>
											</td>
										</tr>
										<tr>
											<td colspan="5" style="text-align:right"><?php echo $this->lang->line('xin_title_sub_total2'); ?></td>
											<td class="text-xs-right"><?php echo $sc_show; ?> <span class="sub_total">0</span></td>
										</tr>
										<tr>
											<td colspan="5" style="text-align:right"><?php echo $this->lang->line('xin_title_tax_c'); ?></td>
											<input type="hidden" class="ftax_total" name="ftax_total" value="0" />
											<td class="text-xs-right"><?php echo $sc_show; ?> <span class="tax_total">0</span></td>
										</tr>
										<tr>
											<td colspan="5" style="text-align:right"><?php echo $this->lang->line('xin_amount'); ?></td>
											<td>
												<div class="form-group">
													<input type="hidden" class="fgrand_total" name="fgrand_total" value="0" />
													<?php echo $sc_show; ?> <strong class="grand_total text-danger">0</strong>
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
								<textarea class="form-control" placeholder="<?php echo $this->lang->line('ms_ref_code'); ?>" name="ref_code" rows="3"></textarea>
							</div>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-md-12">
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

	$(document).on("load", function() {
		getPrepayment();
	});
</script>