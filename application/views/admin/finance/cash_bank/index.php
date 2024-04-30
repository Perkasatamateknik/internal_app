<div class="row">

	<div class="col-md-12">
		<div>
			<h4 class="font-weight-bold mt-3"><?php echo $breadcrumbs; ?>
				<button type="button" class="ml-3 btn btn-outline-success" data-toggle="modal" data-target="#add-modal-data">
					<span class="ion ion-md-add"></span> <?php echo $this->lang->line('xin_add_new'); ?>
					<?php echo $this->lang->line('ms_title_cash_bank'); ?>
				</button>
			</h4>
		</div>
	</div>
</div>
<div class="row">
	<?php foreach ($records as $r) {; ?>
		<div class="col-md-4">
			<a href="<?= base_url('admin/finance/accounts/transactions?id=' . $r->account_id); ?>">
				<div class="card mb-3" style="border-radius: 0.75em;">
					<div class="p-3 pb-0 bg-primary text-white" style="border-radius: 0.75em 0.75em 0 0 ;">
						<div class="row justify-content-between">
							<div class="col-auto text-white">
								<strong class="card-title"><?= $r->account_name; ?></strong><br>
								<span><?= $r->account_code; ?></span>
							</div>
							<div class="col-auto">
								<a href="#" class="btn btn-transparent p-0" data-toggle="modal" data-target="#edit-modal-data" data-id="<?= $r->account_id; ?>"><i class="fa fa-cog text-white" aria-hidden="true"></i></a>
							</div>
						</div>
					</div>
					<div class="card-body bg-primary text-white pt-0">
						<span>Account Number</span>
						<br>
						<strong><?= $r->account_number; ?></strong>
					</div>
					<div class="card-footer">
						<div class="row justify-content-end">
							<div class="col-auto">
								<span>Balance</span>
								<br>
								<h3><?= $this->Xin_model->currency_sign($r->saldo); ?></h3>
							</div>
						</div>
					</div>
				</div>
			</a>
		</div>
	<?php }; ?>
</div>