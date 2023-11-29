<?php $id = $this->input->get('id');

if ($id == '') {
	redirect('admin/finance/accounts');
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
				<div class="row justify-content-between">
					<div class="col-md-auto">
						<a href="<?= base_url('/admin/finance/accounts') ?>" target="" class="btn btn-tranparent"><i class="fa fa-caret-left" aria-hidden="true"></i> <?= $this->lang->line('ms_title_back'); ?></a>
					</div>
					<div class="col-md-auto">
						<button class="btn btn-primary btn-sm"><i class="fa fa-print fa-fw" aria-hidden="true"></i><?= $this->lang->line('xin_print'); ?> </button> <button class="btn btn-transparent btn-sm"><i class="fa fa-ellipsis-v" aria-hidden="true"></i></button>
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
									<strong><?= $record->target_account; ?></strong>
								</td>
								<td>
									<span><?= $this->lang->line('ms_title_ref'); ?></span><br>
									<strong><?= $record->ref; ?></strong>
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
						<table class="table table-borderless">
							<thead>
								<tr>
									<th>
										<strong><?= $this->lang->line('ms_title_desc'); ?></strong>
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
								<tr>
									<td><?= $record->note; ?></td>
									<td><?= $record->note; ?></td>
									<td><?= $this->Xin_model->currency_sign($record->amount); ?></td>
								</tr>
								<tr>
									<td colspan="3"></td>
								</tr>
								<tr>
									<td colspan="3"></td>
								</tr>
							</tbody>
							<tfoot>
								<tr style="border-top: 1px solid black;">
									<td colspan="2" align="center"><strong><?= $this->lang->line('xin_amount'); ?></strong></td>
									<td><strong>000:000:000</strong></td>
								</tr>
							</tfoot>
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
						<label for=""><?php echo $this->lang->line('xin_attachment'); ?></label><br>
					</div>
					<div class="col-md-3 col-sm-12 mb-sm-3">
						<div class="card">
							<img class="card-img-top" src="<?= base_url('/uploads/purchase/invoices/') ?>" alt="">
							<div class="card-body p-3">
								<span class="clearfix mt-1">
									<span><?php
											$fileSize = filesize('./uploads/purchase/invoices/');
											$formattedSize = size($fileSize);
											echo $formattedSize; ?></span>
									<a href="#" class="btn btn-default btn-sm float-right"><i class="fas fa-cloud-download-alt"></i></a>
								</span>
							</div>
						</div>
					</div>
					<div class="col-md-3 col-sm-12 mb-sm-3">
						<div class="card">
							<img class="card-img-top" src="<?= base_url('/uploads/purchase/invoices/') ?>" alt="">
							<div class="card-body p-3">
								<span class="clearfix mt-1">
									<span><?php
											$fileSize = filesize('./uploads/purchase/invoices/');
											$formattedSize = size($fileSize);
											echo $formattedSize; ?></span>
									<a href="#" class="btn btn-default btn-sm float-right"><i class="fas fa-cloud-download-alt"></i></a>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-12">
		<div class="card">
			<div class="card-header">
				<strong><?php echo $this->lang->line('ms_title_purchase_payment'); ?></strong>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="date"><?php echo $this->lang->line('ms_payment_date'); ?></label>
							<input type="date" name="date" id="date" class="form-control" placeholder="<?php echo $this->lang->line('ms_payment_date'); ?>">
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
							<label for="attachment"><?php echo $this->lang->line('xin_attachment'); ?>s</label>
							<input type="file" class="form-control" name="attachment" id="attachment" multiple>
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
							<input type="number" min="0" name="amount_paid" id="amount_paid" class="form-control" placeholder="<?php echo $this->lang->line('ms_payment_amount_paid'); ?>">
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="amount_paid">&nbsp;</label>
							<button type="submit" class="btn btn-primary btn-block"> <i class="far fa-check-square"></i> <?php echo $this->lang->line('xin_save'); ?> </button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>