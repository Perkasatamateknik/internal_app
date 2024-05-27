<?php

if (!is_null($this->input->get('back_id'))) {
	$back = "view/" . $this->input->get('back_id');
} else {
	$back = "receivables";
} ?>

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
						<a href="<?= base_url('/admin/contacts/' . $back) ?>" target="" class="btn btn-tranparent"><i class="fa fa-caret-left" aria-hidden="true"></i> <?= $this->lang->line('ms_title_back'); ?></a>
					</div>
					<div class="col-md-auto">
						<div class="row">
							<div class="col-md-auto px-0">
								<a href="<?= base_url('/admin/finance/receivables/print?id=' . $record->trans_number) ?>" target="_blank" class=" btn btn-primary btn-sm"><i class="fa fa-print fa-fw" aria-hidden="true"></i><?= $this->lang->line('xin_print'); ?> </a>
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
									<span><?= $this->lang->line('ms_title_customer'); ?></span><br>
									<a href="<?= base_url('/admin/contacts/view/' . $record->contact_id) ?>" type="button" class="font-weight-bold"><?= $record->contact_name; ?></a>

								</td>
							</tr>
							<tr>
								<td>
									<span><?= $this->lang->line('ms_title_date'); ?></span><br>
									<strong><?= $this->Xin_model->set_date_format($record->date) ?></strong>
								</td>
								<td>
									<span><?= $this->lang->line('ms_title_due_date'); ?></span><br>
									<strong><?= $this->Xin_model->set_date_format($record->due_date) ?></strong>
								</td>
								<td>
									<span><?= $this->lang->line('ms_title_number_document'); ?></span><br>
									<strong><?= $record->trans_number; ?></strong>
								</td>
							</tr>
							<tr>
								<td>
									<span><?= $this->lang->line('ms_title_ref'); ?></span><br>
									<strong><?= $record->reference ?? "--"; ?></strong>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>


	<div class="col-md-12 mb-3">
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-12">
						<table class="table table-borderless table-striped table-hover">
							<thead class="thead-light">
								<tr>
									<th>
										<strong><?= $this->lang->line('ms_title_account'); ?></strong>
									</th>
									<th>
										<strong><?= $this->lang->line('ms_title_note'); ?></strong>
									</th>
									<th>
										<strong><?= $this->lang->line('ms_title_amount'); ?></strong>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php

								$amount = 0;
								if (!is_null($items)) {
									foreach ($items as $r) {
										$amount += $r->amount;
								?>
										<tr>
											<td><?= $r->account_name; ?></td>
											<td><?= $r->note; ?></td>
											<td><?= $this->Xin_model->currency_sign($r->amount); ?></td>
										</tr>
								<?php }
								} ?>
							</tbody>
							<tfoot style="border-top: 1px solid black;">
								<tr>
									<td></td>
									<td><strong><?= $this->lang->line('xin_amount'); ?></strong></td>
									<td><strong><?= $this->Xin_model->currency_sign($amount); ?></strong></td>
								</tr>
								<?php if ($payment->jumlah_dibayar != 0) {
									foreach ($payment->log_payments as $key => $r) { ?>
										<tr class="text-underline">
											<td></td>
											<td><strong><?php echo $this->lang->line('ms_payment_i'); ?> <?= $key += 1; ?></strong></td>
											<td>
												<strong id="payments"><?= $this->Xin_model->currency_sign($r->amount); ?></strong>
											</td>
										</tr>
								<?php }
								}; ?>
								<tr>
									<td></td>
									<td><strong><?= $this->lang->line('ms_title_remaining_bill'); ?></strong></td>
									<td><strong class="text-danger"><?= $this->Xin_model->currency_sign($payment->sisa_tagihan); ?></strong></td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php if (count($attachments) > 0) { ?>
		<div class="col-md-12 mb-3">
			<div class="card">
				<div class="card-header">
					<strong><?php echo $this->lang->line('xin_attachment'); ?></strong><br>

				</div>
				<div class="card-body">
					<div class="row">
						<?php foreach ($attachments as $i => $attachment) {

							$image = ['png', 'jpg', 'jpeg', 'gif'];
							if (!in_array($attachment->file_ext, $image)) {
								$attachment->file_view = 'pdf.png';
							} else {
								$attachment->file_view = $attachment->file_name;
							}
						?>
							<div class="col-md-2 col-sm-6 mb-sm-3">
								<div class="card border-secondary">
									<img class="card-img-top" src="<?= base_url('/uploads/contact/receivables/' . $attachment->file_view) ?>" alt="" height="150px">
									<div class="card-body p-3">
										<span class="clearfix mt-1">
											<b><?= $this->lang->line('ms_title_attachment'); ?> <?= $i += 1; ?></b>
											<br>
											<small>

												<?php
												$fileSize = filesize('./uploads/contact/receivables/' . $attachment->file_name);
												$formattedSize = size($attachment->file_size);
												echo $formattedSize; ?></small>
											<a href="<?= base_url('/uploads/contact/receivables/' . $attachment->file_name) ?>" target="_blank" class="btn btn-default btn-sm float-right"><i class="fas fa-cloud-download-alt"></i></a>
										</span>
									</div>
								</div>
							</div>
						<?php }; ?>
					</div>
				</div>
			</div>
		</div>
	<?php }; ?>

	<?php if ($payment->sisa_tagihan != 0) {; ?>
		<div class="col-md-12">
			<div class="card mb-3">
				<div class="card-header">
					<strong><?php echo $this->lang->line('ms_title_purchase_payment'); ?></strong>
				</div>
				<div class="card-body">
					<?php $attributes = array('name' => 'payment_fordm', 'id' => 'payment_form', 'autocomplete' => 'off', 'class' => 'm-b-1 add', 'enctype' => 'multipart/form-data'); ?>
					<?php $hidden = array('type' => 'spend', '_token' => $record->trans_number); ?>
					<?php echo form_open('admin/contacts/receivable_store_payment', $attributes, $hidden); ?>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label for="date"><?php echo $this->lang->line('ms_payment_date'); ?></label>
								<input type="date" name="date" id="date" class="form-control" placeholder="<?php echo $this->lang->line('ms_payment_date'); ?>" value="<?= date('Y-m-d') ?>" required>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="payment_ref"><?php echo $this->lang->line('ms_payment_ref'); ?></label>
								<input type="text" name="payment_ref" id="payment_ref" class="form-control" placeholder="<?php echo $this->lang->line('ms_payment_ref'); ?>">
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="attachment"><?php echo $this->lang->line('xin_attachment'); ?></label>
								<input type="file" class="form-control" name="attachment" id="attachment">
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="source_payment_account"><?php echo $this->lang->line('ms_payment_account_source'); ?></label>
								<select class="form-control" name="source_payment_account" data-plugin="select_accounts" data-placeholder="<?php echo $this->lang->line('ms_payment_account_source'); ?>" required>

								</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="amount_paid"><?php echo $this->lang->line('ms_payment_amount_paid'); ?></label>
								<input type="number" min="0" max="<?= $payment->sisa_tagihan; ?>" value="<?= $payment->sisa_tagihan; ?>" name="amount_paid" id="amount_paid" class="form-control" placeholder="<?php echo $this->lang->line('ms_payment_amount_paid'); ?>" required>
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

	<?php if ($payment->jumlah_dibayar != 0) {; ?>
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
										foreach ($payment->log_payments as $key => $value) {
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
												<td><a href="<?= base_url('admin/finance/accounts/transactions?id=' . $value->account_id) ?>" class=""><?= "<strong>$value->account_name</strong>" . "  " . $value->account_code; ?></a></td>
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