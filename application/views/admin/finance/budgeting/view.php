<div class="row">
	<div class="col-md-12">
		<div class="card rounded-3 mb-3">
			<div class="card-body">
				<div class="row justify-content-between">
					<div class="col-md-auto">
						<h4><?= $record->year; ?> <?= $this->lang->line('ms_title_budget'); ?></h4>
					</div>
					<div class="col-md-auto">
						<?php if ($record->status == 1) {; ?>
							<button class="btn btn-primary" data-toggle="modal" data-target="#show_modal" data-id="<?= $this->input->get('id'); ?>"><i class="fa fa-plus" aria-hidden="true"></i> <?= $this->lang->line('ms_title_add_budget_data'); ?></button>
						<?php } else {; ?>
							<button class="btn btn-primary" disabled><i class="fa fa-plus" aria-hidden="true"></i> <?= $this->lang->line('ms_title_add_budget_data'); ?></button>
						<?php }; ?>
						<button class="btn btn-secondary" data-toggle="modal" data-target="#"><i class="fa fa-chevron" aria-hidden="true"></i> <?= $this->lang->line('ms_title_add_budget'); ?>Tombol</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row" id="view_budget_data">
	<?php foreach ($records as $r) { ?>
		<div class="col-md-12">
			<div class="card rounded-3 mb-3">
				<div class="card-header">
					<h4 class="card-title"><?= $r->department_name; ?></h4>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table w-100">
							<thead>
								<tr>
									<th style="min-width: 35%;"><?= $this->lang->line('ms_title_budget_name'); ?></th>
									<th style="min-width: 40%;"><?= $this->lang->line('ms_title_accounts'); ?></th>
									<th style="min-width: 25%;"><?= $this->lang->line('ms_title_amount'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								$total = 0;
								foreach ($r->budget_data as $bd) {
									$total += $bd->amount;  ?>
									<tr>
										<td><?= $bd->budget_name; ?></td>
										<td><?= $bd->account_name; ?></td>
										<td><?= $this->Xin_model->currency_sign($bd->amount); ?></td>
									</tr>
								<?php }; ?>
							</tbody>
							<tfoot>
								<tr style="background-color: bisque;">
									<td colspan="2" class="text-right">
										<b><?= $this->lang->line('xin_title_total'); ?></b>
									</td>
									<td>
										<b><?= $this->Xin_model->currency_sign($total); ?></b>
									</td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	<?php }; ?>
</div>

<script>
	var budget_id = "<?= $this->input->get('id'); ?>";
</script>