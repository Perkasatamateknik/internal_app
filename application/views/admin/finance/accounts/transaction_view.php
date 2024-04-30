<?php

$id = $this->input->get('id');
$back_id = $this->input->get('back_id');

if ($id == '' or $back_id == '') {
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
						<a href="<?= base_url('/admin/finance/accounts/transactions?id=' . $back_id) ?>" target="" class="btn btn-tranparent"><i class="fa fa-caret-left" aria-hidden="true"></i> <?= $this->lang->line('ms_title_back'); ?></a>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-12">
						<table class="table table-borderless">
							<tr>
								<td>
									<span><?= $this->lang->line('ms_title_trans_type'); ?></span><br>
									<strong><?= $record->trans_type; ?></strong>
								</td>
								<td>
									<span><?= $this->lang->line('ms_title_trans_pic'); ?></span><br>
									<strong><?= $record->user_paid; ?></strong>
								</td>
							</tr>
							<tr>
								<td>
									<span><?= $this->lang->line('ms_title_date'); ?></span><br>
									<strong><?= $this->Xin_model->set_date_format($record->date); ?></strong>
								</td>
								<td>
									<span><?= $this->lang->line('ms_title_trans_cash_flow'); ?></span><br>
									<strong><?= ucfirst($record->type); ?></strong>
								</td>
							</tr>
							<tr>
								<td>
									<span><?= $this->lang->line('ms_title_amount'); ?></span><br>
									<strong><?= $this->Xin_model->currency_sign($record->amount); ?></strong>
								</td>
								<td>
									<span><?= $this->lang->line('ms_title_note'); ?></span><br>
									<strong><?= $record->note; ?></strong>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php if (!is_null($record->attachment)) { ?>
		<div class="col-md-12 mb-3">
			<div class="card">
				<div class="card-header">
					<strong><?php echo $this->lang->line('xin_attachment'); ?></strong><br>
				</div>
				<div class="card-body">
					<div class="row">
						<?php

						$attachment = $record->attachment;
						$explode = explode(".", $attachment);
						$image = ['png', 'jpg', 'jpeg', 'gif'];
						var_dump($explode[1]);
						if (!in_array($explode[1], $image)) {
							$attachment = 'pdf.png';
						} else {
							$attachment = $attachment;
						}
						?>
						<div class="col-md-2 col-sm-6 mb-sm-3">
							<div class="card border-secondary">
								<img class="card-img-top" src="<?= base_url('/uploads/finance/account_trans/' . $attachment) ?>" alt="" height="150px">
								<div class="card-body p-3">
									<span class="clearfix mt-1">
										<b><?= $this->lang->line('ms_title_attachment'); ?></b>
										<br>
										<small>

											<?php
											$fileSize = filesize('./uploads/finance/account_trans/' . $attachment);
											$formattedSize = 78;
											echo $formattedSize; ?></small>
										<a href="<?= base_url('/uploads/finance/account_trans/' . $attachment) ?>" target="_blank" class="btn btn-default btn-sm float-right"><i class="fas fa-cloud-download-alt"></i></a>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php }; ?>
</div>