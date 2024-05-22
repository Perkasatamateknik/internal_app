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
									<strong><?= $record->contact_name; ?></strong>
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
							<tfoot>
								<tr style="border-top: 1px solid black;">
									<td></td>
									<td><strong><?= $this->lang->line('xin_amount'); ?></strong></td>
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
									<img class="card-img-top" src="<?= base_url('/uploads/finance/account_spend/' . $attachment->file_view) ?>" alt="" height="150px">
									<div class="card-body p-3">
										<span class="clearfix mt-1">
											<b><?= $this->lang->line('ms_title_attachment'); ?> <?= $i += 1; ?></b>
											<br>
											<small>

												<?php
												$fileSize = filesize('./uploads/finance/account_spend/' . $attachment->file_name);
												$formattedSize = size($attachment->file_size);
												echo $formattedSize; ?></small>
											<a href="<?= base_url('/uploads/finance/account_spend/' . $attachment->file_name) ?>" target="_blank" class="btn btn-default btn-sm float-right"><i class="fas fa-cloud-download-alt"></i></a>
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
</div>