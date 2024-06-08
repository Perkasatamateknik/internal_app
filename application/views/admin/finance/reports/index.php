<?php
/* Employees view
*/
?>
<?php $session = $this->session->userdata('username'); ?>
<?php $get_animate = $this->Xin_model->get_content_animate(); ?>
<?php $role_resources_ids = $this->Xin_model->user_role_resource(); ?>
<?php $user_info = $this->Xin_model->read_user_info($session['user_id']); ?>
<?php $system = $this->Xin_model->read_setting_info(1); ?>
<?php $id = $this->input->get('id') ?? false; ?>

<div class="row mb-3">
	<div class="col-md-12">
		<div class="card">
			<div class="card-header">
				<h6 class="card-title">
					<strong><?= $breadcrumbs; ?></strong>
				</h6>

			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-8">
						<div class="card">
							<div class="card-header">
								<div class="card-title">
									<strong>Finance</strong>
								</div>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="col-md-4">
										<a href="<?= base_url('admin/finance/reports/balance_sheet') ?>" class="btn btn-outline-secondary btn-block mb-3">
											<i class="fa fa-file-text" aria-hidden="true"></i>
											<?= $this->lang->line('ms_title_balance_sheet'); ?>
										</a>
									</div>
									<div class="col-md-4">
										<a href="<?= base_url('admin/finance/reports/executive_summary') ?>" class="btn btn-outline-secondary btn-block mb-3">
											<i class="fas fa-chart-bar    "></i>
											<?= $this->lang->line('ms_title_executive_summary'); ?>
										</a>
									</div>
									<div class="col-md-4">
										<a href="<?= base_url('admin/finance/reports/budget_realization') ?>" class="btn btn-outline-secondary btn-block mb-3">
											<i class="fas fa-chart-pie fa-fw"></i>
											<?= $this->lang->line('ms_title_budget_realization'); ?>
										</a>
									</div>
								</div>
								<div class="row">
									<div class="col-md-4">
										<a href="<?= base_url('admin/finance/reports/cash_flow') ?>" class="btn btn-outline-secondary btn-block mb-3">
											<i class="fas fa-money-bill-alt fa-fw"></i>
											<?= $this->lang->line('ms_title_cash_flow'); ?>
										</a>
									</div>
									<div class="col-md-4">
										<a href="<?= base_url('admin/finance/reports/payable_credit_for_contact') ?>" class="btn btn-outline-secondary btn-block mb-3">
											<i class="fa fa-file fa-fw" aria-hidden="true"></i>
											<?= $this->lang->line('ms_title_payable_credit_for_contact'); ?>
										</a>
									</div>
								</div>
								<div class="row">
									<div class="col-md-4">
										<a href="<?= base_url('admin/finance/reports/profit_and_loss') ?>" class="btn btn-outline-secondary btn-block mb-3">
											<i class="fas fa-money-bill-alt"></i>
											<?= $this->lang->line('ms_title_profit_and_loss'); ?>
										</a>
									</div>
									<div class="col-md-4">
										<a href="<?= base_url('admin/finance/reports/tax') ?>" class="btn btn-outline-secondary btn-block mb-3">
											<i class="fas fa-dollar-sign    "></i>
											<?= $this->lang->line('ms_title_tax'); ?>
										</a>
									</div>
								</div>
								<div class="row">
									<div class="col-md-4">
										<a href="<?= base_url('admin/finance/reports/equity_movement') ?>" class="btn btn-outline-secondary btn-block mb-3">
											<i class="fas fa-balance-scale-left    "></i>
											<?= $this->lang->line('ms_title_equity_movement'); ?>
										</a>
									</div>
									<div class="col-md-4">
										<a href="<?= base_url('admin/finance/reports/expense') ?>" class="btn btn-outline-secondary btn-block mb-3">
											<i class="fas fa-coins    "></i>
											<?= $this->lang->line('ms_title_expense'); ?>
										</a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="card">
							<div class="card-header">
								<div class="card-title">
									<strong>Finance</strong>
								</div>
							</div>
							<div class="card-body">
								<div class="row justify-content-center">
									<?php for ($i = 0; $i < 4; $i++) { ?>
										<div class="col-md-10">
											<a href="#" class="btn btn-light btn-block mb-3">
												<i class="fa fa-paste fa-fw" aria-hidden="true"></i>
												Bank A <?= $i; ?>
											</a>
										</div>
									<?php }; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-auto">
		<style>
			.has-search .form-control {
				padding-left: 2.375rem;
			}

			.has-search .form-control-feedback {
				position: absolute;
				z-index: 2;
				display: block;
				width: 2.375rem;
				height: 2.375rem;
				line-height: 2.375rem;
				text-align: center;
				pointer-events: none;
				color: #aaa;
			}
		</style>
		<div class="form-group has-search">
			<div class="row">

				<div class="col-md-auto">
					<span class="fa fa-search form-control-feedback"></span>
					<input type="text" class="form-control" placeholder="Search" id="cari_data">
				</div>
				<div class="col-md-auto px-0">
					<div class="dropdown open">
						<button class="btn btn-light dropdown-toggle" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Filter
						</button>
						<div class="dropdown-menu" aria-labelledby="triggerId">
							<button class="dropdown-item" href="#">Action</button>
							<button class="dropdown-item disabled" href="#">Disabled action</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row mb-3">
	<div class="col-12">
		<div class="card">
			<div class="card-body">

				<div class="table-responsive">
					<table class="table table-hover table-striped" id="ms_table">
						<thead>
							<tr>
								<th>#</th>
								<th><?= $this->lang->line('ms_title_date'); ?></th>
								<th><?= $this->lang->line('ms_title_number_document'); ?></th>
								<th><?= $this->lang->line('ms_title_transfer'); ?></th>
								<th><?= $this->lang->line('ms_title_ref'); ?></th>
								<th><?= $this->lang->line('ms_title_status'); ?></th>
								<th style="min-width: 150px;"><?= $this->lang->line('ms_title_amount'); ?></th>
							</tr>
						</thead>
						<tbody>

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>