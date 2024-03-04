<?php
/* Employees view
*/
?>
<?php $session = $this->session->userdata('username'); ?>
<?php $get_animate = $this->Xin_model->get_content_animate(); ?>
<?php $role_resources_ids = $this->Xin_model->user_role_resource(); ?>
<?php $user_info = $this->Xin_model->read_user_info($session['user_id']); ?>
<?php $system = $this->Xin_model->read_setting_info(1); ?>
<?= null; ?>
<div class="row mb-3">
	<div class="col-md-12">
		<div class="card">
			<div class="row">
				<div class="col-md-2">
					<div class="card border-0 bg-primary text-white h-100">
						<div class="card-body d-flex align-items-center">
							<h4 class="my-auto"><?= $this->lang->line('ms_title_amount_expense'); ?></h4>
						</div>
					</div>
				</div>
				<div class="col-md-5">
					<div class="card border-0 shadow-none h-100">
						<div class="card-body">
							<span><?= $this->lang->line('ms_title_payment_paid'); ?></span>
							<br>
							<h1><?= $this->Xin_model->currency_sign($payments['paid']) ?></h1>
							<?= $payments['text_paid']; ?>
						</div>
					</div>
				</div>
				<div class="col-md-5">
					<div class="card border-0 shadow-none h-100">
						<div class="card-body">
							<span><?= $this->lang->line('ms_title_payment_unpaid'); ?></span>
							<br>
							<h1><?= $this->Xin_model->currency_sign($payments['unpaid']) ?></h1>
							<?= $payments['text_unpaid']; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- <div class="row mb-3">
	<div class="col-md-12">
		<table class="table w-100 table-borderless m-0" cellspacing="0" cellpadding="0">
			<tr>
				<td class="h-100">
					<div class="card border-0 bg-primary text-white ">
						<div class="card-body">
							<h4><?= $this->lang->line('ms_title_amount_expense'); ?></h4>
						</div>
					</div>
				</td>
				<td>
					<div class="card border-0 shadow-none h-100">
						<div class="card-body">
							<span><?= $this->lang->line('ms_title_payment_paid'); ?></span>
							<br>
							<h1><?= $this->Xin_model->currency_sign($payments['paid']) ?></h1>
							<?= $payments['text_paid']; ?>
						</div>
					</div>
				</td>
				<td>
					<div class="card border-0 shadow-none h-100">
						<div class="card-body">
							<span><?= $this->lang->line('ms_title_payment_unpaid'); ?></span>
							<br>
							<h1><?= $this->Xin_model->currency_sign($payments['unpaid']) ?></h1>
							<?= $payments['text_unpaid']; ?>
						</div>
					</div>
				</td>
			</tr>
		</table>
		<div class="card m-0 p-0">
		</div>
	</div>
</div> -->

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