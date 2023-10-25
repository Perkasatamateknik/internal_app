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
			<span class="card-header-title mr-2"><strong> <?php echo $this->lang->line('ms_project_trans_detail'); ?> &nbsp;</strong> #<?= strtoupper($result->invoice_id); ?></span>

		</div>
	</div>
	<div class="card-body">
		<div class="box-datatable table-responsive">
			<table class="table table-borderless">
				<tr>
					<td>
						<strong><?php echo $this->lang->line('ms_vendors'); ?></strong> <br>
						<?= $result->vendor_name; ?>
					</td>
					<td>
						<strong><?php echo $this->lang->line('ms_invoice_number'); ?></strong> <br>
						<?= $result->invoice_number; ?>
					</td>
				</tr>
				<tr>
					<td>
						<strong><?php echo $this->lang->line('xin_invoice_date'); ?></strong> <br>
						<?= $result->invoice_date; ?>
					</td>
					<td>
						<strong> <?php echo $this->lang->line('ms_reference'); ?></strong> <br>
						<?= $result->ref_code; ?>
					</td>
				</tr>
				<tr>
					<td>
						<strong><?php echo $this->lang->line('xin_invoice_due_date'); ?></strong> <br>
						<?= $result->invoice_due_date; ?>
					</td>
					<td>
						<strong><?php echo $this->lang->line('ms_trans_status'); ?></strong> <br>
						<?= $result->status_payment; ?>
					</td>
				</tr>
				<?php if ($result->status_payment_code == 1) { ?>
					<tr>
						<td>
							<strong><?php echo $this->lang->line('ms_trans_prepayment'); ?></strong> <br>
							<?= $this->Xin_model->currency_sign($result->prepayment); ?>
						</td>
						<td></td>
					</tr>
				<?php }; ?>
			</table>
			<br>
			<input type="hidden" name="project_cost_id" id="project_cost_id" value="<?php echo $result->id; ?>" />
			<table class="datatables-demo table table-striped table-borderles table-hover" id="xin_table_php">
				<thead>
					<tr>
						<th><?php echo $this->lang->line('xin_projects'); ?></th>
						<th><?php echo $this->lang->line('ms_products'); ?></th>
						<th><?php echo $this->lang->line('xin_title_qty_hrs'); ?></th>
						<th><?php echo $this->lang->line('ms_product_uom'); ?></th>
						<th><?php echo $this->lang->line('ms_product_price'); ?></th>
						<th><?php echo $this->lang->line('xin_title_tax'); ?></th>
						<th><?php echo $this->lang->line('xin_amount'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if (!is_null($record)) {
						$amount = 0;
						foreach ($record as $detail) {
							$amount += $detail->amount;
					?>
							<tr>
								<td><?= $detail->project_title; ?></td>
								<td>
									<?php echo $detail->product_name; ?>
									<br>
									<small><strong><?php echo $this->lang->line('ms_product_number'); ?>: </strong><?= $detail->product_number; ?></small>
								</td>
								<td><?= $detail->qty; ?></td>
								<td><?= $detail->uom_id; ?></td>
								<td><?php echo $detail->price; ?></td>
								<td><?= $this->Xin_model->currency_sign($detail->tax_rate); ?></td>
								<td><?php echo $this->Xin_model->currency_sign($detail->amount) ?> </td>
							</tr>
					<?php }
					} ?>
					<tr>
						<td colspan="6" align="right">Pajak</td>
						<td><?= $this->Xin_model->currency_sign($result->tax_total); ?></td>
					</tr>
					<tr>
						<td colspan="6" align="right">Diskon</td>
						<td><?= $this->Xin_model->currency_sign($result->discount); ?></td>
					</tr>
					<tr>
						<td colspan="6" align="right">Total</td>
						<td><?= $this->Xin_model->currency_sign($amount); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
<style type="text/css">
	.k-in {
		display: none !important;
	}
</style>
