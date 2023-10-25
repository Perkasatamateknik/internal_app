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
<?php if (in_array('474', $role_resources_ids)) { ?>

	<div class="card mb-4 <?php echo $get_animate; ?>">
		<div id="accordion">
			<div class="card-header with-elements"> <span class="card-header-title mr-2"><strong><?php echo $this->lang->line('xin_add_new'); ?></strong> <?php echo $this->lang->line('ms_project_trans'); ?></span>
				<div class="card-header-elements ml-md-auto"> <a class="text-dark collapsed" data-toggle="collapse" href="#add_role_form" aria-expanded="false">
						<button type="button" class="btn btn-xs btn-primary"> <span class="ion ion-md-add"></span> <?php echo $this->lang->line('xin_add_new'); ?></button>
					</a> </div>
			</div>
			<div id="add_role_form" class="collapse add-form <?php echo $get_animate; ?>" data-parent="#accordion" style="">
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
											<select class="form-control select" name="vendor" data-plugin="select_hrm" data-placeholder="<?php echo $this->lang->line('ms_vendor_name'); ?>">
												<option value=""></option>
												<?php foreach ($vresult as $vendor) { ?>
													<option value="<?php echo $vendor->vendor_id ?>"> <?php echo $vendor->vendor_name ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="invoice_date"><?php echo $this->lang->line('xin_invoice_number'); ?></label>
											<input class="form-control" placeholder="<?php echo $this->lang->line('xin_invoice_number'); ?>" name="invoice_number" type="text">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="ms_vendor" class="control-label"><?php echo $this->lang->line('ms_status'); ?></label>
											<select class="form-control" name="status" data-plugin="select_hrm" data-placeholder="<?php echo $this->lang->line('ms_status'); ?>" onchange="getPrepayment(this)">
												<option value="0"><?= $this->lang->line('ms_status_pending') ?></option>
												<option value="1"><?= $this->lang->line('ms_status_prepayment') ?></option>
												<option value="2"><?= $this->lang->line('ms_status_paid') ?></option>
											</select>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="invoice_date"><?php echo $this->lang->line('xin_invoice_date'); ?></label>
											<input class="form-control date" placeholder="<?php echo $this->lang->line('xin_invoice_date'); ?>" readonly="readonly" name="invoice_date" type="date" value="" id="invoice_date">
										</div>
									</div>

									<div class="col-md-6">
										<div class="form-group">
											<label for="invoice_due_date"><?php echo $this->lang->line('xin_invoice_due_date'); ?></label>
											<input class="form-control date" placeholder="<?php echo $this->lang->line('xin_invoice_due_date'); ?>" readonly="readonly" name="invoice_due_date" type="date" value="" id="invoice_due_date">
										</div>
									</div>
									<div class=" col-md-6">
										<div class="form-group">
											<label for="select_due_date" class="control-label"><?php echo $this->lang->line('xin_select'); ?> <?php echo $this->lang->line('xin_invoice_due_date'); ?></label>
											<select class="form-control" name="select_due_date" id="select_due_date" data-plugin="select_hrm" data-placeholder="<?php echo $this->lang->line('xin_invoice_due_date'); ?>">
												<option value="0" selected><?= $this->lang->line('ms_custom'); ?></option>
												<option value="1" data-type="days">1 <?= $this->lang->line('xin_day'); ?></option>
												<option value="3" data-type="days">3 <?= $this->lang->line('xin_day'); ?></option>
												<option value="7" data-type="days">7 <?= $this->lang->line('xin_day'); ?></option>
												<option value="10" data-type="days">10 <?= $this->lang->line('xin_day'); ?></option>
												<option value="15" data-type="days">15 <?= $this->lang->line('xin_day'); ?></option>
												<option value="20" data-type="days">20 <?= $this->lang->line('xin_day'); ?></option>
												<option value="1" data-type="months">1 <?= $this->lang->line('xin_month'); ?></option>
												<option value="3" data-type="months">3 <?= $this->lang->line('xin_month'); ?></option>
												<option value="6" data-type="months">6 <?= $this->lang->line('xin_month'); ?></option>
												<option value="9" data-type="months">9 <?= $this->lang->line('xin_month'); ?></option>
												<option value="1" data-type="years">1 <?= $this->lang->line('xin_year'); ?></option>
												<option value="2" data-type="years">2 <?= $this->lang->line('xin_year'); ?></option>
											</select>
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
											<table class="datatables-demo table table-striped" id="item_product">
												<thead class="thead-light">
													<tr>
														<!-- <th>No</th> -->
														<th style="min-width:100px"><?php echo $this->lang->line('xin_title_item'); ?></th>
														<th style="max-width:100px"><?php echo $this->lang->line('xin_project'); ?></th>
														<th><?php echo $this->lang->line('xin_title_taxes'); ?></th>
														<th><?php echo $this->lang->line('xin_title_tax_rate'); ?></th>
														<th><?php echo $this->lang->line('ms_discount_title'); ?></th>
														<th><?php echo $this->lang->line('ms_discount_value'); ?></th>
														<th><?php echo $this->lang->line('xin_title_unit_price'); ?></th>
														<th><?php echo $this->lang->line('xin_title_qty_hrs'); ?></th>
														<th><?php echo $this->lang->line('xin_title_sub_total'); ?></th>
														<th class="text-center"><?php echo $this->lang->line('xin_action'); ?></th>
													</tr>
												</thead>
												<tbody id="formRow">
												</tbody>
												<tfoot>
													<tr>
														<td colspan="8" style="text-align:right"><?php echo $this->lang->line('xin_title_sub_total2'); ?></td>
														<td colspan="2" class="text-xs-right"><?php echo $sc_show; ?> <span class="sub_total">0</span></td>
													</tr>
													<tr>
														<td colspan="8" style="text-align:right"><?php echo $this->lang->line('xin_title_tax_c'); ?>
															<input type="hidden" class="ftax_total" name="ftax_total" value="0" />
														</td>
														<td colspan="2" class="text-xs-right"><?php echo $sc_show; ?> <span class="tax_total">0</span></td>
													</tr>
													<tr>
														<td colspan="8" style="text-align:right"><?php echo $this->lang->line('xin_amount'); ?></td>
														<td colspan="2">
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
		</div>
	</div>

<?php } ?>

<div class="card <?php echo $get_animate; ?>">
	<div class="card-header with-elements"> <span class="card-header-title mr-2"><strong><?php echo $this->lang->line('xin_list_all'); ?></strong> <?php echo $this->lang->line('ms_project_trans'); ?></span>
	</div>
	<div class="card-body">
		<div class="box-datatable table-responsive">
			<table class="datatables-demo table table-striped" id="xin_table_project_costs">
				<thead>
					<tr>
						<th><?php echo $this->lang->line('xin_action'); ?></th>
						<th><?php echo $this->lang->line('ms_invoice_number'); ?></th>
						<th><?php echo $this->lang->line('ms_invoice_date'); ?></th>
						<th><?php echo $this->lang->line('ms_vendors'); ?></th>
						<th><?php echo $this->lang->line('ms_status'); ?></th>
						<th><?php echo $this->lang->line('ms_highlights'); ?></th>
						<th><?php echo $this->lang->line('ms_reference'); ?></th>
						<th style="min-width:100px"><?php echo $this->lang->line('xin_amount'); ?></th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
</div>
<style type="text/css">
	.k-in {
		display: none !important;
	}
</style>

<?php if (in_array('474', $role_resources_ids)) { ?>
	<script>
		function getPrepayment(e) {
			var html = '<div class="form-group"><label for="ms_prepayment"> <?php echo $this->lang->line('ms_prepayment'); ?></label><input class="form-control" placeholder="<?php echo $this->lang->line('ms_prepayment'); ?>" name="prepayment" type="number" value = "0"></div>';
			var value = e.value;
			if (value == 1) {
				$('#ms_prepayment').html(html);
			} else {
				$('#ms_prepayment').html('');
			}
		}
	</script>
<?php } ?>