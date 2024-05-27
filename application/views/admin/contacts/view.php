<?php $session = $this->session->userdata('username'); ?>
<?php $get_animate = $this->Xin_model->get_content_animate(); ?>
<?php $role_resources_ids = $this->Xin_model->user_role_resource(); ?>
<?php $user_info = $this->Xin_model->read_user_info($session['user_id']); ?>
<?php $system = $this->Xin_model->read_setting_info(1); ?>
<div class="row mb-3">
	<div class="col-md-12">
		<div class="card">
			<div class="card-body">
				<div class="row ">
					<div class="col-md-10">
						<div class="row">
							<div class="col-md-auto">
								<img src="https://via.placeholder.com/75x75.png/001155?text=<?= urlencode($record->contact_name); ?>" alt="" class="rounded-circle img-fluid">
							</div>
							<div class="col-md-auto align-content-center">
								<h3><?= $record->contact_name; ?></h3>
								<span><?= $record->contact_type; ?></span>
							</div>
						</div>
					</div>
					<div class="col-md-2 align-content-center">
						<a name="" id="" class="btn btn-warning float-right" href="<?= base_url('/admin/contacts') ?>" role="button">
							<i class="fa fa-angle-left" aria-hidden="true"></i>
							<?= $this->lang->line('ms_title_back'); ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row mb-3">
	<div class="col-md-3">
		<div class="card">
			<div class="card-header mb-0 pb-0">
				<h4><?= $this->lang->line('ms_title_contact_detail'); ?> <button onclick="modalEdit(<?= $record->contact_id; ?>)" id="" class="btn btn-default border-0 px-1 py-0 pb-1" role="button"><i class="fa fa-pencil" aria-hidden="true"></i></button></h4>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-sm table-borderless">
						<tr>
							<th class="font-weight-bold"><?= $this->lang->line('ms_title_contact_name'); ?></th>
							<td class="text-right"><?= $record->contact_name; ?></td>
						</tr>
						<tr>
							<th class="font-weight-bold"><?= $this->lang->line('ms_title_company'); ?></th>
							<td class="text-right"><?= $record->company_name; ?></td>
						</tr>
						<tr>
							<th class="font-weight-bold"><?= $this->lang->line('ms_title_billing_address'); ?></th>
							<td class="text-right"><?= $record->billing_address ?? "--"; ?></td>
						</tr>
						<tr>
							<th class="font-weight-bold"><?= $this->lang->line('ms_title_email_address'); ?></th>
							<td class="text-right"><?= $record->email_address ?? "--"; ?></td>
						</tr>
						<tr>
							<th class="font-weight-bold"><?= $this->lang->line('xin_phone'); ?></th>
							<td class="text-right"><?= $record->phone_number ?? "--"; ?></td>
						</tr>
						<tr>
							<th class="font-weight-bold"><?= $this->lang->line('ms_title_date_of_birth'); ?></th>
							<td class="text-right"><?= $record->date_of_birth ?? "--"; ?></td>
						</tr>
						<tr>
							<th class="font-weight-bold"><?= $this->lang->line('xin_country'); ?></th>
							<td class="text-right"><?= $record->country_name ?? "--"; ?></td>
						</tr>
					</table>
				</div>
				<div id="modal-view"></div>
			</div>
		</div>
	</div>
	<div class="col-md-9">
		<div class="card">
			<div class="card-body">
				<div class="row justify-content-end">
					<div class="col-md-auto pr-1">
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" type="button" id="triggerTrans" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i class="fa fa-plus" aria-hidden="true"></i> <?= $this->lang->line('ms_title_add_trans'); ?>
							</button>
							<div class="dropdown-menu" aria-labelledby="triggerTrans">
								<a class="dropdown-item" href="<?= base_url('admin/contacts/create_trans?type=utang&back_id=' . $record->contact_id) ?>"><i class="fas fa-file-invoice-dollar fa-fw mr-3" aria-hidden="true"></i><?= $this->lang->line('ms_title_liabilities'); ?></a>
								<!-- <br> -->
								<a class="dropdown-item" href="<?= base_url('admin/contacts/create_trans?type=piutang&back_id=' . $record->contact_id) ?>" class="btn btn-block btn-white text-left"><i class="fas fa-hand-holding-usd fa-fw mr-3" aria-hidden="true"></i><?= $this->lang->line('ms_title_receivables'); ?></a>
								<!-- <br> -->
							</div>
						</div>
					</div>
					<div class="col-md-auto pl-1">
						<a name="" id="" class="btn btn-primary mx-1" href="<?= base_url('/admin/contacts/print') ?>" role="button">
							<i class="fa fa-print" aria-hidden="true"></i>
							<?= $this->lang->line('xin_print'); ?>
						</a>
					</div>
				</div>
				<br>
				<div class="">
					<div class="row">
						<div class="col-sm-3 p-1">
							<div class="card">
								<div class="card-body p-2">
									<div class="row ">
										<div class="col-md-auto pr-0 align-content-center">
											<h1 class="font-weight-bold m-0"><?= $count_liabilities->count; ?></h1>
										</div>
										<div class="col-md-auto align-content-center">
											<span><?= $this->lang->line('ms_title_liability_doc'); ?></span>
										</div>
									</div>
									<hr class="m-1">
									<div class="row justify-content-between">
										<div class="col-md-auto">
											<small><?= $this->lang->line('ms_title_total'); ?></small>
										</div>
										<div class="col-md-auto">
											<small><?= $this->Xin_model->currency_sign($count_liabilities->amount_bill); ?></small>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-3 p-1">
							<div class="card">
								<div class="card-body p-2">
									<div class="row ">
										<div class="col-md-auto pr-0 align-content-center">
											<h1 class="font-weight-bold m-0"><?= $count_receivables->count; ?></h1>
										</div>
										<div class="col-md-auto align-content-center">
											<span><?= $this->lang->line('ms_title_receivable_doc'); ?></span>
										</div>
									</div>
									<hr class="m-1">
									<div class="row justify-content-between">
										<div class="col-md-auto">
											<small><?= $this->lang->line('ms_title_total'); ?></small>
										</div>
										<div class="col-md-auto">
											<small><?= $this->Xin_model->currency_sign($count_receivables->amount_bill); ?></small>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-3 p-1">
							<div class="card">
								<div class="card-body p-2">
									<div class="row ">
										<div class="col-md-auto pr-0 align-content-center">
											<h1 class="font-weight-bold m-0"><?= $count_liabilities->count_late; ?></h1>
										</div>
										<div class="col-md-auto align-content-center">
											<span><?= $this->lang->line('ms_title_liability_due_date'); ?></span>
										</div>
									</div>
									<hr class="m-1">
									<div class="row justify-content-between">
										<div class="col-md-auto">
											<small><?= $this->lang->line('ms_title_total'); ?></small>
										</div>
										<div class="col-md-auto">
											<small><?= $this->Xin_model->currency_sign($count_liabilities->amount_bill_late); ?></small>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-3 p-1">
							<div class="card">
								<div class="card-body p-2">
									<div class="row ">
										<div class="col-md-auto pr-0 align-content-center">
											<h1 class="font-weight-bold m-0"><?= $count_receivables->count_late; ?></h1>
										</div>
										<div class="col-md-auto align-content-center">
											<span><?= $this->lang->line('ms_title_receivable_due_date'); ?></span>
										</div>
									</div>
									<hr class="m-1">
									<div class="row justify-content-between">
										<div class="col-md-auto">
											<small><?= $this->lang->line('ms_title_total'); ?></small>
										</div>
										<div class="col-md-auto">
											<small><?= $this->Xin_model->currency_sign($count_receivables->amount_bill_late); ?></small>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<hr>
			<div class="card-body">
				<div class="row justify-content-between">
					<div class="col-md-auto">
						<h4><?= $this->lang->line('ms_title_transactions'); ?></h4>
					</div>
					<div class="col-md-4">
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
							<span class="fa fa-search form-control-feedback"></span>
							<input type="text" class="form-control" placeholder="Search" id="cari_data">
						</div>
					</div>
					<div class="table-responsive">
						<table class="table w-100" id="ms_table_trans" data-id="<?= $record->contact_id; ?>">
							<thead>
								<tr>
									<th style="width: 15%;"><?= $this->lang->line('ms_title_date'); ?></th>
									<th style="width: 40%;"><?= $this->lang->line('ms_title_transactions'); ?></th>
									<th style="width: 20%;"><?= $this->lang->line('ms_title_desc'); ?></th>
									<th style="width: 25%;"><?= $this->lang->line('ms_title_amount'); ?></th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
			</div>
			<hr>
			<div class="card-body">
				<div class="row">
					<div class="col-md-6 p-4">
						<h4><?= $this->lang->line('ms_title_outstanding_receivables'); ?></h4>
						<div class="card bg-card">
							<div class="card-body">
								<div class="row" style="max-height:500px; overflow-y:scroll">
									<?php foreach ($receivables as $r) { ?>
										<div class="col-md-12 p-2">
											<div class="card p-2">
												<div class="card-body p-2">
													<div class="row justify-content-between">
														<div class="col-md-auto">
															<a href="<?= base_url('admin/contacts/receivable_view/' . $r->trans_number . "?back_id=" . $record->contact_id) ?>" class="text-large font-weight-semibold"><?= $r->trans_number ?></a>
														</div>
														<div class="col-md-auto align-content-end">
															<small class="text-danger"><?= dateDiff($r->date, $r->due_date, " left"); ?></small>
														</div>
													</div>
													<hr class="m-1">
													<div class="row justify-content-between">
														<div class="col-md-auto font-weight-semibold">
															<?= $this->lang->line('ms_title_total'); ?>
															:
															<?= $this->Xin_model->currency_sign($r->amount_total); ?>
														</div>
														<div class="col-md-auto ">
															<small><?= $r->due_date ?></small>
														</div>
													</div>
												</div>
											</div>
										</div>
									<?php }; ?>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6 p-4">
						<h4><?= $this->lang->line('ms_title_unpaid_debt'); ?></h4>
						<div class="card bg-card">
							<div class="card-body">
								<div class="row" style="max-height:500px; overflow-y:scroll">
									<?php foreach ($liabilities as $r) { ?>
										<div class="col-md-12 p-2">
											<div class="card p-2">
												<div class="card-body p-2">
													<div class="row justify-content-between">
														<div class="col-md-auto">
															<a href="<?= base_url('admin/contacts/liability_view/' . $r->trans_number . "?back_id=" . $record->contact_id) ?>" class="text-large font-weight-semibold"><?= $r->trans_number; ?></a>
														</div>
														<div class="col-md-auto align-content-end">
															<small class="text-danger"><?= dateDiff($r->date, $r->due_date, " left"); ?></small>
														</div>
													</div>
													<hr class="m-1">
													<div class="row justify-content-between">
														<div class="col-md-auto font-weight-semibold">
															<?= $this->lang->line('ms_title_total'); ?>
															:
															<?= $this->Xin_model->currency_sign($r->amount_total); ?>
														</div>
														<div class="col-md-auto ">
															<small><?= $r->due_date ?></small>
														</div>
													</div>
												</div>
											</div>
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