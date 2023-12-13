<?php $id = $this->input->get('id');

if ($id == '') {
	redirect('admin/expense');
}
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
				<div class="row">
					<div class="col-12">
						<table class="table table-small">
							<tr>
								<td>
									<strong class="p-auto"><?= text_status_trans($record->status, true); ?></strong>
								</td>
								<td>
									<div class="d-flex float-right">
										<button class="btn btn-primary btn-sm"><i class="fa fa-print fa-fw" aria-hidden="true"></i><?= $this->lang->line('xin_print'); ?> </button>
										<a href="<?= base_url('/admin/finance/expense') ?>" target="" class="btn btn-tranparent"><i class="fa fa-caret-left" aria-hidden="true"></i> <?= $this->lang->line('ms_title_back'); ?></a>
										<button class="btn btn-transparent btn-sm"><i class="fa fa-ellipsis-v" aria-hidden="true"></i></button>

									</div>
								</td>
							</tr>
						</table>
					</div>
				</div>
				<div class="row justify-content-between">
					<div class="col-md-auto">
					</div>
					<div class="col-md-auto">
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
									<span><?= $this->lang->line('ms_title_terget_account'); ?></span><br>
									<strong><?= $record->beneficiary; ?></strong>
								</td>
								<td>
									<span><?= $this->lang->line('ms_title_ref'); ?></span><br>
									<strong><?= $record->reference ?? "--"; ?></strong>
								</td>
							</tr>
							<tr>
								<td>
									<span><?= $this->lang->line('ms_title_date'); ?></span><br>
									<strong><?= $this->Xin_model->set_date_format($record->date); ?></strong>
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
										<strong><?= $this->lang->line('ms_title_tax'); ?></strong>
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
										$amount = ($r->amount + $r->tax_rate) + $amount;
								?>
										<tr>
											<td><?= $r->account_name; ?></td>
											<td><?= $r->note; ?></td>
											<td><?= $r->tax_name; ?> <br>
												<small><?= $this->Xin_model->currency_sign($r->tax_rate); ?></small>
											</td>
											<td><?= $this->Xin_model->currency_sign($r->amount); ?></td>
										</tr>
								<?php }
								} ?>
							</tbody>
							<tfoot>
								<tr style="border-top: 1px solid black;">
									<td colspan="3" align="center"><strong><?= $this->lang->line('xin_amount'); ?></strong></td>
									<td><strong><?= $this->Xin_model->currency_sign($amount); ?></strong></td>
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
									<img class="card-img-top" src="<?= base_url('/uploads/finance/expense/' . $attachment->file_view) ?>" alt="" height="150px">
									<div class="card-body p-3">
										<span class="clearfix mt-1">
											<b><?= $this->lang->line('ms_title_attachment'); ?> <?= $i += 1; ?></b>
											<br>
											<small>

												<?php
												$fileSize = filesize('./uploads/finance/expense/' . $attachment->file_name);
												$formattedSize = size($attachment->file_size);
												echo $formattedSize; ?></small>
											<a href="<?= base_url('/uploads/finance/expense/' . $attachment->file_name) ?>" target="_blank" class="btn btn-default btn-sm float-right"><i class="fas fa-cloud-download-alt"></i></a>
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
	<div class="col-md-12">
		<div class="card">
			<div class="card-header">
				<strong><?php echo $this->lang->line('ms_title_purchase_payment'); ?></strong>
			</div>
			<div class="card-body">
				<?php $attributes = array('name' => 'payment_form', 'id' => 'payment_form', 'autocomplete' => 'off', 'class' => 'm-b-1 add', 'enctype' => 'multipart/form-data'); ?>
				<?php $hidden = array('type' => 'transfer', '_token' => $record->expense_id); ?>
				<?php echo form_open('admin/finance/expense/store_payment', $attributes, $hidden); ?>
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
							<input type="hidden" name="account_source" value="<?= $record->account_id; ?>">
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="amount_paid"><?php echo $this->lang->line('ms_payment_amount_paid'); ?></label>
							<input type="number" min="0" max="<?= $amount; ?>" value="0" name="amount_paid" id="amount_paid" class="form-control" placeholder="<?php echo $this->lang->line('ms_payment_amount_paid'); ?>" required>
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
</div>