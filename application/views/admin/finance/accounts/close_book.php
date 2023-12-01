<style>
	input[type="date"] {
		outline: none !important;
		border: 0px solid !important;
	}
</style>
<div class="row">
	<div class="col-md-12">
		<div class="card mb-3">
			<div class="card-header with-elements"> <span class="card-header-title mr-2"><strong><?php echo $this->lang->line('ms_title_close_book'); ?></strong></span>
				<div class="card-body">
					<table class="table table-stripped table-hover" id="xin_table">
						<thead>
							<tr>
								<th><?= $this->lang->line('ms_title_period'); ?></th>
								<th><?= $this->lang->line('ms_title_note'); ?></th>
								<th><?= $this->lang->line('ms_title_loss_proft'); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>2023-09-01 - 2023-09-10</td>
								<td>Opening Balance</td>
								<td>00</td>
							</tr>
							<tr>
								<td>2023-09-05 - 2023-09-10</td>
								<td>Salary Payment</td>
								<td>00</td>
							</tr>
							<tr>
								<td>2023-09-10 - 2023-09-15</td>
								<td>Office Supplies</td>
								<td>00</td>
							</tr>
							<tr>
								<td>2023-09-15 - 2023-09-30</td>
								<td>Office Supplies</td>
								<td>00</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>