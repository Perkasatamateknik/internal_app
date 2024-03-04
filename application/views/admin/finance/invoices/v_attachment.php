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