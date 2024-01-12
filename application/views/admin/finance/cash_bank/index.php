<div class="row">

	<div class="col-md-12">
		<div>
			<h4 class="font-weight-bold mt-3"><?php echo $breadcrumbs; ?>
				<button type="button" class="ml-3 btn btn-outline-success" data-toggle="modal" data-target="#modelId">
					<span class="ion ion-md-add"></span> <?php echo $this->lang->line('xin_add_new'); ?>
					<?php echo $this->lang->line('ms_title_cash_bankk.'); ?>
				</button>
			</h4>
		</div>
	</div>
</div>
<div class="row">
	<?php foreach ($records as $r) {; ?>
		<div class="col-md-4">
			<div class="card mb-3" style="border-radius: 1.5em;">
				<div class="p-3 bg-primary text-white" style="border-radius: 1.5em 1.5em 0 0 ;">
					<div class="row justify-content-between">
						<div class="col-auto">
							<strong class="card-title"><?= $r->account_name; ?></strong><br>
							<span><?= $r->account_code; ?></span>
						</div>
						<div class="col-auto">
							<a href="#" class="btn btn-transparent p-0"><i class="fa fa-cog text-white" aria-hidden="true"></i></a>
						</div>
					</div>
				</div>
				<div class="card-body bg-primary text-white">
					<span>Account Number</span>
					<br>
					<strong><?= $r->account_number; ?></strong>
				</div>
				<div class="card-footer">
					<div class="row justify-content-end">
						<div class="col-auto">
							<span>Balance</span>
							<br>
							<h3><?= $this->Xin_model->currency_sign(rand(1000000000000, 1000000000000)); ?></h3>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php }; ?>
</div>