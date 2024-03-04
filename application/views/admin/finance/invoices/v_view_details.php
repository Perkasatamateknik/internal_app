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
										<td><?= $r->project_name; ?></td>
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