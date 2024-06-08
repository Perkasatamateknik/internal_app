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
			<div class="card-body">
				<div class="row justify-content-between">
					<div class="col-md-auto">
						<b><?= $this->lang->line('ms_title_balance_sheet'); ?></b>
					</div>
					<div class="col-md-auto">
						<div class="row">
							<div class="col-md-auto px-1">
								<a href="<?= base_url('/admin/finance/reports/balance_sheet_print?id=') ?>" target="_blank" class=" btn btn-success btn-sm"><i class="fa fa-print fa-fw" aria-hidden="true"></i><?= $this->lang->line('xin_print'); ?> </a>
							</div>
							<div class="col-md-auto px-1">
								<a href="<?= base_url('/admin/finance/reports') ?>" target="" class="btn btn-warning btn-sm"><i class="fa fa-caret-left" aria-hidden="true"></i> <?= $this->lang->line('ms_title_back'); ?></a>
							</div>
							<div class="col-md-auto px-1">
								<div class="dropdown d-flex">
									<button class="btn btn-transparent btn-sm" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<i class="fa fa-ellipsis-v" aria-hidden="true"></i>
									</button>
									<div class="dropdown-menu" aria-labelledby="triggerId">
										<a class="dropdown-item" href="#">Export Excell</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<hr>
				<div class="row justify-content-end pt3">
					<form action="" method="get" id="form-filter">
						<div class="col-md-auto px-3">
							<div class="row">
								<!-- <div class="col-md-auto px-1">
								<div class="btn-group">
									<button class="btn btn-outline-secondary dropdown-toggle btn-sm" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<?= $this->lang->line('ms_title_compare'); ?>
									</button>
									<div class="dropdown-menu dropdown-menu-right" aria-labelledby="triggerId">
										<a class="dropdown-item" href="<?= base_url('admin/finance/reports/balance_sheet') ?>?compare=month"><?= $this->lang->line('ms_title_month'); ?></a>
										<a class="dropdown-item" href="<?= base_url('admin/finance/reports/balance_sheet') ?>?compare=year"><?= $this->lang->line('ms_title_year'); ?></a>
									</div>
								</div>
							</div> -->
								<div class="col-md-auto px-1">
									<!-- <div class="btn-group">
									<button class="btn btn-outline-secondary dropdown-toggle btn-sm" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<?= $this->lang->line('ms_title_period'); ?>
									</button>
									<div class="dropdown-menu dropdown-menu-right" aria-labelledby="triggerId">
										<a class="dropdown-item" href="<?= base_url('admin/finance/reports/balance_sheet') ?>?period=1">1 <?= $this->lang->line('ms_title_period'); ?></a>
										<a class="dropdown-item" href="<?= base_url('admin/finance/reports/balance_sheet') ?>?period=2">2 <?= $this->lang->line('ms_title_period'); ?></a>
										<a class="dropdown-item" href="<?= base_url('admin/finance/reports/balance_sheet') ?>?period=3">3 <?= $this->lang->line('ms_title_period'); ?></a>
										<a class="dropdown-item" href="<?= base_url('admin/finance/reports/balance_sheet') ?>?period=4">4 <?= $this->lang->line('ms_title_period'); ?></a>
									</div>
								</div> -->

									<div class="col-md-auto px-1">
										<div class="form-group">
											<select class="form-control form-control-sm filter-data" name="period" id="">
												<?php
												$period = $this->input->get('period'); ?>
												<option value="" <?= $period == '' ? 'selected' : ''; ?>><?= $this->lang->line('ms_title_period'); ?></option>
												<?php
												for ($i = 1; $i <= 4; $i++) {  ?>
													<option value="<?= $i; ?>" <?= $i == $period ? 'selected' : ''; ?>><?= $i; ?><?= $this->lang->line('ms_title_period'); ?></option>
												<?php	}; ?>

												<!-- <option value="2">2<?= $this->lang->line('ms_title_period'); ?></option>
												<option value="3">3<?= $this->lang->line('ms_title_period'); ?></option>
												<option value="4">4<?= $this->lang->line('ms_title_period'); ?></option> -->
											</select>
										</div>
									</div>
								</div>
								<!-- <div class="col-md-auto px-1">
								<div class="btn-group">
									<button class="btn btn-outline-secondary dropdown-toggle btn-sm" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<?= $this->lang->line('ms_title_month_year'); ?>
									</button>
									<div class="dropdown-menu dropdown-menu-right" aria-labelledby="triggerId">
										<a class="dropdown-item" href="#">Action</a>
										<h6 class="dropdown-header">Section header</h6>
										<a class="dropdown-item" href="#">Action</a>
									</div>
								</div>
							</div> -->
								<div class="col-md-auto px-1">
									<div class="form-group">
										<select class="form-control form-control-sm filter-data" name="compare" id="" data-placeholder="<?= $this->lang->line('ms_title_compare'); ?>">
											<?php

											$compare = $this->input->get('compare');; ?>
											<option value="" <?= $compare == '' ? 'selected' : ''; ?>><?= $this->lang->line('ms_title_compare'); ?></option>

											<?php
											foreach (['month' => $this->lang->line('ms_title_month'), 'year' => $this->lang->line('ms_title_year')] as $key => $val) {; ?>
												<option value="<?= $key; ?>" <?= $key == $compare ? 'selected' : ''; ?>><?= $val ?></option>
											<?php }; ?>
											<!-- <option value="month"><?= $this->lang->line('ms_title_month'); ?></option>
											<option value="year"><?= $this->lang->line('ms_title_year'); ?></option> -->
										</select>
									</div>
								</div>
								<div class="col-md-auto px-1">
									<div class="form-group">
										<input type="date" name="date" class="form-control form-control-sm filter-data" value="<?= date('Y-m-d'); ?>">
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
				<div class="table-reponseive pt-3">
					<table class="table w-100">
						<thead>
							<tr>
								<th style="min-width:37%">Asset</th>
								<th>Date</th>
								<th>Date</th>
								<th>Date</th>
								<th>Date</th>
								<th>Date</th>
							</tr>
						</thead>
					</table>
				</div>

			</div>
		</div>
	</div>
</div>