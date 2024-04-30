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

<style>
	div.table-mini-container {
		overflow: auto;
		min-width: 100px;
	}

	table.table-mini {
		width: 100%;
		font-size: 0.6rem;
	}

	table.table-mini th,
	table.table-mini td {
		padding: 0.1rem;
	}


	table.table-mini>tbody>tr>td:not(:first-child) {
		min-width: 15px;
	}

	.container-chart {
		display: flex;
		justify-content: center;
		/* Horizontal centering */
		align-items: center;
		/* Vertical centering */
		margin: 0;
		height: 330px;
	}

	.chart-body {
		height: 200px;
		width: auto;
		/* Adjust the width of the chart container as needed */
	}

	.container-chart::-webkit-scrollbar {
		width: 0;
		/* Remove scrollbar width */
		height: 0;
		/* Remove scrollbar height */
	}
</style>
<?php if (in_array('471', $role_resources_ids) || $user_info[0]->user_role_id == 1) { ?>
	<div class="row">
		<?php if (in_array('471', $role_resources_ids)) { ?>
			<div class="col-xl-4 col-md-6 align-items-strdetch d-flex">
				<!-- Daily progress chart -->
				<div class="card mb-4 flex-fill">
					<div class="card-header with-elements">
						<span class="card-header-title mr-2"><strong><?php echo $this->lang->line('ms_purchase_transactions'); ?></strong></span>
						<div class="card-header-elements ml-md-auto">
							<input type="month" name="month" id="monthSelector" class="form-control form-control-sm">
						</div>
						<div class="card-header-elements ml-md-auto">
							<select class="form-control form-control-sm" name="selected" id="valueSelector">
								<option value="category"><?php echo $this->lang->line('ms_title_categories'); ?></option>
								<option value="sub-category"><?php echo $this->lang->line('ms_title_sub_categories'); ?></option>
							</select>
						</div>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-md-12">
								<div class="container-chart">
									<canvas id="get_purchase_selecteds" class="chart-body"></canvas>
								</div>
							</div>
							<div class="col-md-12">
								<div class="overflow-scrolls py-4 px-3 container-chart" style="overflow:auto;">
									<div class="table-responsive">
										<table class="table mb-0 table-sm table-bordered" id="table_purchase_selected">
											<thead class="thead-light">
												<tr>
													<th id="setname"></th>
													<th><?= $this->lang->line('xin_amount') ?></th>
												</tr>
											</thead>
											<tbody>
											</tbody>
											<tfoot>
											</tfoot>
										</table>
									</div>
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
						<a href="<?= base_url('admin/purchase_requisitions') ?>" class="">
							<div class="card mb-4">
								<div class="card-body">
									<div class="row">
										<div class="col-md-12">
											<div class="text-muted small"><?php echo $this->lang->line('ms_purchase_requisitions'); ?></div>
										</div>
										<div class="col-md-12">
											<div class="row justify-content-between">
												<div class="col-md-6 align-items-center">
													<center>
														<br>
														<span class="lnr lnr-cart display-4"></span>
														<br>
														<strong class="text-large"><?= $data['count']['pr'] ?></strong>
													</center>
												</div>
												<div class="col-md-6 small">
													<h5><?= $this->lang->line('ms_priority_status') ?></h5>
													<div class="table-mini-container">
														<table class="table table-mini">
															<?php foreach ($data['pr_data'] as $key => $val) {
																$split = explode("_", $key);
															?>
																<tr>
																	<td class="text-uppercase"><?= priority_stats($split[1]); ?></td>
																	<td class="text-success font-weight-bold"><?= $val; ?></td>
																</tr>
															<?php }; ?>
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
					<?php error_reporting(0); ?>
					<div class="col-md-6">
						<a href="<?= base_url('admin/purchase_orders') ?>" class="">
							<div class="card mb-4">
								<div class="card-body">
									<div class="row">
										<div class="col-md-12">
											<div class="text-muted small"><?php echo $this->lang->line('ms_purchase_orders'); ?></div>
										</div>
										<div class="col-md-12">
											<div class="row justify-content-between">
												<div class="col-md-6 align-items-center">
													<center>
														<br>
														<span class="lnr lnr-inbox display-4"></span>
														<br>
														<strong class="text-large"><?= $data['count']['po']; ?></strong>
													</center>
												</div>
												<div class="col-md-6 small">
													<h5><?= $this->lang->line('ms_status') ?></h5>
													<table class="table table-mini">
														<?php foreach ($data['po_data'] as $key => $val) {
															$split = explode("_", $key);

														?>
															<tr>
																<td class="text-uppercase"><?= po_stats($split[1]); ?></td>
																<td class="text-success font-weight-bold"><?= $val; ?></td>
															</tr>
														<?php }; ?>
													</table>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</a>
					</div>
					<div class="col-md-6">
						<a href="<?= base_url('admin/purchase_deliveries') ?>" class="">
							<div class="card mb-4">
								<div class="card-body">
									<div class="row">
										<div class="col-md-12">
											<div class="text-muted small"><?php echo $this->lang->line('ms_purchase_deliveries'); ?></div>
										</div>
										<div class="col-md-12">
											<div class="row justify-content-between">
												<div class="col-md-6 align-items-center">
													<center>
														<br>
														<span class="lnr lnr-exit-up display-4"></span>
														<br>
														<strong class="text-large"><?= $data['count']['pd']; ?></strong>
													</center>
												</div>
												<div class="col-md-6 small">
													<h5><?= $this->lang->line('ms_status') ?></h5>
													<table class="table table-mini">
														<?php foreach ($data['pd_data'] as $key => $val) {
															$split = explode("_", $key);
														?>
															<tr>
																<td class="text-uppercase"><?= pd_stats($split[1]); ?></td>
																<td class="text-success font-weight-bold"><?= $val; ?></td>
															</tr>
														<?php }; ?>
													</table>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</a>
					</div>
					<div class="col-md-6">
						<a href="<?= base_url('admin/purchase_invoices') ?>" class="">
							<div class="card mb-4">
								<div class="card-body">
									<div class="row">
										<div class="col-md-12">
											<div class="text-muted small"><?php echo $this->lang->line('ms_purchase_invoices'); ?></div>
										</div>
										<div class="col-md-12">
											<div class="row justify-content-between">
												<div class="col-md-6 align-items-center">
													<center>
														<br>
														<span class="lnr lnr-file-empty display-4"></span>
														<br>
														<strong class="text-large"><?= $data['count']['pi']; ?></strong>
													</center>
												</div>
												<div class="col-md-6 small">
													<h5><?= $this->lang->line('ms_status') ?></h5>
													<table class="table table-mini">
														<?php foreach ($data['pi_data'] as $key => $val) {
															$split = explode("_", $key);
														?>
															<tr>
																<td class="text-uppercase"><?= pi_stats($split[1]); ?></td>
																<td class="text-success font-weight-bold"><?= $val; ?></td>
															</tr>
														<?php }; ?>
													</table>
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
				<div class="row">
					<div class="col-md-12">
						<div class="card mb-4">
							<div class="card-header with-elements">
								<h6 class="mb-0	"><?php echo $this->lang->line('ms_purchase_by_vendors'); ?></h6>
								<div class="card-header-elements ml-md-auto">
									<input type="month" name="month" id="monthSelectorVendor" class="form-control form-control-sm">
								</div>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="col-md-4">
										<div class="container-chart">
											<canvas id="get_purchase_by_vendorss" class="chart-body"></canvas>
										</div>
									</div>
									<div class="col-md-8">
										<div class="overflow-scrolls py-4 px-3 container-chart" style="overflow:auto;">
											<div class="table-responsive">
												<table class="table mb-0 table-sm table-bordered" id="table_purchase_by_vendors">
													<thead class="thead-light">
														<tr>
															<th><?= $this->lang->line('ms_vendors') ?></th>
															<th><?= $this->lang->line('xin_amount') ?></th>
														</tr>
													</thead>
													<tbody>
													</tbody>
													<tfoot>

													</tfoot>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php  } ?>
	</div>
<?php  } ?>