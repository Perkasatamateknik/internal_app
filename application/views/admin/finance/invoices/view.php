<?php $id = $this->input->get('id');

if ($id == '') {
	redirect('admin/invoices');
}

// error_reporting(0);
?>

<div class="row">
	<div class="col-12">
		<h4 class="font-weight-bold mt-3"><?php echo $breadcrumbs; ?></h4>
	</div>
</div>
<div class="row">
	<div class="col-md-12 mb-3">
		<div class="card">
			<div class="card-body">
				<div class="row justify-content-between">
					<div class="col-md-auto">
						<a href="<?= base_url('/admin/finance/incomes') ?>" target="" class="btn btn-tranparent"><i class="fa fa-caret-left" aria-hidden="true"></i> <?= $this->lang->line('ms_title_back'); ?></a>
					</div>
					<div class="col-md-auto">
						<div class="row">
							<div class="col-md-auto px-0">
								<a href="<?= base_url('/admin/finance/invoices/print?id=' . $record->trans_number) ?>" target="_blank" class=" btn btn-primary btn-sm"><i class="fa fa-print fa-fw" aria-hidden="true"></i><?= $this->lang->line('xin_print'); ?> </a>
							</div>
							<div class="col-md-auto">
								<div class="dropdown d-flex">
									<button class="btn btn-transparent btn-sm" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<i class="fa fa-ellipsis-v" aria-hidden="true"></i>
									</button>
									<div class="dropdown-menu" aria-labelledby="triggerId">
										<a class="dropdown-item" href="<?= base_url('/admin/finance/accounts/spend_print?type=export&id=' . $record->trans_number) ?>" target="_blank">Export PDF</a>
										<a class="dropdown-item" href="#">Export Excell</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-12">
						<table class="table table-borderless">
							<tr>
								<td>
									<span><?= $this->lang->line('ms_title_source_account'); ?></span><br>
									<strong><?= $record->source_account; ?></strong>
								</td>
								<td>
									<span><?= $this->lang->line('ms_title_number_document'); ?></span><br>
									<strong><?= $record->trans_number; ?></strong>
								</td>
							</tr>
							<tr>
								<td>
									<span><?= $this->lang->line('ms_title_client'); ?></span><br>
									<strong><?= $record->client; ?></strong>
								</td>
								<td>
									<span><?= $this->lang->line('ms_title_ref'); ?></span><br>
									<strong><?= $record->reference ?? "--"; ?></strong>
								</td>
							</tr>
							<tr>
								<td>
									<span><?= $this->lang->line('ms_title_date'); ?></span><br>
									<strong><?= $this->Xin_model->set_date_format($record->publish_date); ?></strong>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row" id="details" data-id="<?= $record->invoice_id; ?>">


	<!-- <div class="col-md-12" id="view_attachment" data-id="<?= $record->invoice_id; ?>">
	</div> -->

	<?php if ($record->sisa_tagihan != 0) {; ?>
		<div class="col-md-12">
			<div class="card mb-3">
				<div class="card-header">
					<strong><?php echo $this->lang->line('ms_title_purchase_payment'); ?></strong>
				</div>
				<div class="card-body">
					<?php $attributes = array('name' => 'payment_form', 'id' => 'payment_form', 'autocomplete' => 'off', 'class' => 'm-b-1 add', 'enctype' => 'multipart/form-data'); ?>
					<?php $hidden = array('type' => 'transfer', '_token' => $record->spend_id); ?>
					<?php echo form_open('admin/finance/accounts/store_spend_payment', $attributes, $hidden); ?>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label for="date"><?php echo $this->lang->line('ms_payment_date'); ?></label>
								<input type="date" name="date" id="date" class="form-control" placeholder="<?php echo $this->lang->line('ms_payment_date'); ?>" required>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="payment_ref"><?php echo $this->lang->line('ms_payment_ref'); ?></label>
								<input type="text" name="payment_ref" id="payment_ref" class="form-control" placeholder="<?php echo $this->lang->line('ms_payment_ref'); ?>" required>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="attachment"><?php echo $this->lang->line('xin_attachment'); ?></label>
								<input type="file" class="form-control" name="attachment" id="attachment" required>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="account_source"><?php echo $this->lang->line('ms_payment_account_source'); ?></label>
								<input type="text" class="form-control" placeholder="<?php echo $this->lang->line('ms_payment_account_source'); ?>" readonly value="<?= $record->source_account; ?>">
								<input type="hidden" name="source_payment_account" value="<?= $record->account_id; ?>">
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="amount_paid"><?php echo $this->lang->line('ms_payment_amount_paid'); ?></label>
								<input type="number" min="0" max="<?= $record->sisa_tagihan; ?>" value="<?= $record->sisa_tagihan; ?>" name="amount_paid" id="amount_paid" class="form-control" placeholder="<?php echo $this->lang->line('ms_payment_amount_paid'); ?>" required>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="amount_paid">&nbsp;</label>
								<button type="submit" class="btn btn-primary btn-block"> <i class="far fa-check-square"></i> <?php echo $this->lang->line('xin_save'); ?> </button>
							</div>
						</div>
					</div>
					<?php echo form_close(); ?>
				</div>
			</div>
		</div>
	<?php }; ?>

	<?php if ($record->jumlah_dibayar != 0) {; ?>
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<strong><?php echo $this->lang->line('ms_purchase_log'); ?></strong>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<div class="table-responsive">
								<table class="table table-striped table" id="ms_table_items">
									<thead>
										<tr>
											<th><?php echo $this->lang->line('ms_purchase_date'); ?></th>
											<th><?php echo $this->lang->line('ms_purchase_pic'); ?></th>
											<th><?php echo $this->lang->line('ms_title_desc'); ?></th>
											<th><?php echo $this->lang->line('ms_title_accounts'); ?></th>
											<th><?php echo $this->lang->line('xin_amount'); ?></th>
										</tr>
									</thead>
									<tbody>
										<?php
										// dd($record->log_payments);
										foreach ($record->log_payments as $key => $value) {
											if (empty($value->first_name) or empty($value->last_name)) {
												$pic = "--";
											} else {
												$pic = $value->first_name . "  " . $value->last_name;
											}
										?>
											<tr>
												<td><?= $this->Xin_model->set_date_format($value->date); ?></td>
												<td><?= $pic; ?></td>
												<td><?= $value->note; ?></td>
												<td><?= "<b>$value->account_name</b>" . "  " . $value->account_code; ?></td>
												<td><?= $this->Xin_model->currency_sign($value->amount); ?></td>
											</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php }; ?>
</div>