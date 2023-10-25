<?php
/* Employees view
*/
?>
<?php $session = $this->session->userdata('username'); ?>
<?php $get_animate = $this->Xin_model->get_content_animate(); ?>
<?php $role_resources_ids = $this->Xin_model->user_role_resource(); ?>
<?php $user_info = $this->Xin_model->read_user_info($session['user_id']); ?>
<?php $system = $this->Xin_model->read_setting_info(1); ?>
<?php
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
<?php if (in_array('471', $role_resources_ids) || $user_info[0]->user_role_id == 1) { ?>
	<div class="row">
		<?php if (in_array('471', $role_resources_ids)) { ?>
			<div class="col-xl-4 col-md-6 align-items-strdetch">
				<!-- Daily progress chart -->
				<div class="card rounded-3 mb-4">
					<h6 class="card-header with-elements border-0 pr-0 pb-0">
						<div class="card-header-title"><?php echo $this->lang->line('ms_purchase_'); ?></div>
					</h6>
					<div class="row">
						<div class="col-md-12">
							<div style="height:250px;">
								<canvas id="last_month_trans" style="display: block; height: 300px; width:auto;"></canvas>
							</div>
						</div>
						<div class="col-md-12">
							<div class="overflow-scrolls py-4 px-3" style="overflow:auto; height:300px;">
								<div class="table-responsive">
									<table class="table mb-0 table_last_month_trans">
										<tbody>
										</tbody>
									</table>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		<?php  } ?>
		<?php if (in_array('472', $role_resources_ids) || $user_info[0]->user_role_id == 1) { ?>
			<div class="col-xl-8 col-md-6 align-items-strdetch">

				<div class="row">
					<div class="col-md-6">
						<a href="" class="">
							<div class="card mb-4">
								<div class="card-body">
									<div class="d-flex align-items-center">
										<div class="ion ion-ios-calculator display-4 text-info"></div>
										<div class="ml-3">
											<div class="text-muted small"><?php echo $this->lang->line('ms_purchase_requisitions'); ?></div>
											<div class="text-large"><?php echo $this->Xin_model->currency_sign($this->Project_costs_model->get_trans_remaining_payment()); ?></div>
										</div>
									</div>
								</div>
							</div>
						</a>
					</div>
					<div class="col-md-6">
						<a href="" class="">
							<div class="card mb-4">
								<div class="card-body">
									<div class="d-flex align-items-center">
										<div class="ion ion-ios-calculator display-4 text-info"></div>
										<div class="ml-3">
											<div class="text-muted small"><?php echo $this->lang->line('ms_purchase_orders'); ?></div>
											<div class="text-large"><?php echo $this->Xin_model->currency_sign($this->Project_costs_model->get_trans_remaining_payment()); ?></div>
										</div>
									</div>
								</div>
							</div>
						</a>
					</div>
					<div class="col-md-6">
						<a href="" class="">
							<div class="card mb-4">
								<div class="card-body">
									<div class="d-flex align-items-center">
										<div class="ion ion-ios-calculator display-4 text-info"></div>
										<div class="ml-3">
											<div class="text-muted small"><?php echo $this->lang->line('ms_purchase_deliveries'); ?></div>
											<div class="text-large"><?php echo $this->Xin_model->currency_sign($this->Project_costs_model->get_trans_remaining_payment()); ?></div>
										</div>
									</div>
								</div>
							</div>
						</a>
					</div>
					<div class="col-md-6">
						<a href="" class="">
							<div class="card mb-4">
								<div class="card-body">
									<div class="row">
										<div class="col-md-12">
											<div class="text-muted small"><?php echo $this->lang->line('ms_purchase_invoices'); ?></div>
										</div>
										<div class="col-md-12">

											<div class="row">
												<div class="d-flex align-items-center">

													<div class="col-md-6">
														<div class="ion ion-ios-calculator display-4 text-info"></div>
														<!-- <div class="ml-0">
															</div> -->
														<div class="text-large"><?php echo $this->Xin_model->currency_sign($this->Project_costs_model->get_trans_remaining_payment()); ?></div>
													</div>
													<div class="col-md-6 small">
														<h5>Prioritas</h5>
														<table class="table table-sm">
															<tr>
																<td>HEIGH</td>
																<td class="text-success font-weight-bold">100</td>
															</tr>
															<tr>
																<td>MEDIUM</td>
																<td class="text-warning font-weight-bold">3</td>
															</tr>
															<tr>
																<td>LOW</td>
																<td class="text-danger font-weight-bold">5</td>
															</tr>
														</table>
													</div>
												</div>
											</div>
										</div>
									</div>

								</div>
							</div>
						</a>
					</div>
				</div>
				<!-- Daily progress chart -->
				<div class="card mb-4">
					<h6 class="card-header with-elements border-0 pr-0 pb-0">
						<div class="card-header-title"><?php echo $this->lang->line('ms_trans_last_month_vendors'); ?></div>
					</h6>
					<div class="row">
						<div class="col-md-6">
							<div class="overflow-scrolls py-4 px-3" style="overflow:auto; height:300px;">
								<div class="table-responsive">
									<table class="table mb-0 table_last_month_trans_vendor">
										<tbody>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div style="height:250px;">
								<canvas id="last_month_trans_vendor" style="display: block; height: 300px; width:auto;"></canvas>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php  } ?>
	</div>

<?php  } ?>