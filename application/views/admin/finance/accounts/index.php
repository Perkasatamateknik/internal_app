<div class="row">
	<div class="col-md-12">
		<div class="card mb-3">
			<div class="card-header with-elements"> <span class="card-header-title mr-2"><strong><?php echo $this->lang->line('ms_title_cash_bank'); ?></strong></span>
				<div class="card-header-elements ml-md-auto">
					<a href="#" class="btn btn-primary btn-sm">
						<span class="ion ion-md-add"></span> Saldo Awal
					</a>
					&nbsp;
					<button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#add-modal-data">
						<span class="ion ion-md-add"></span> Add Account
					</button>
					&nbsp;
					<a href="#" class="btn btn-primary btn-sm">
						<span class="ion ion-md-attachment"></span> Manual Jurnal
					</a>
					&nbsp;
					<a href="#" class="btn btn-primary btn-sm">
						<span class="ion ion-md-book"></span> Close Book
					</a>
					&nbsp;
					<a href="#" class="btn btn-primary btn-sm">
						<span class="ion ion-md-print"></span> Print
					</a>
				</div>
			</div>
			<div class="card-body">
				<table class="table table-stripped table-hover" id="xin_table">
					<thead>
						<tr>
							<th><?= $this->lang->line('ms_title_account_code'); ?></th>
							<th><?= $this->lang->line('ms_title_account_name'); ?></th>
							<th><?= $this->lang->line('ms_title_account_category'); ?></th>
							<th><?= $this->lang->line('ms_title_account_balance'); ?></th>
						</tr>
					</thead>
					<!-- <tbody>
						<tr>
							<td>2023-09-01</td>
							<td>Opening Balance</td>
							<td></td>
							<td></td>
							<td>10,000.00</td>
						</tr>
						<tr>
							<td>2023-09-05</td>
							<td>Salary Payment</td>
							<td>5,000.00</td>
							<td></td>
							<td>15,000.00</td>
						</tr>
						<tr>
							<td>2023-09-10</td>
							<td>Office Supplies</td>
							<td>200.00</td>
							<td></td>
							<td>14,800.00</td>
						</tr>
						<tr>
							<td>2023-09-15</td>
							<td>Rent Payment</td>
							<td>2,000.00</td>
							<td></td>
							<td>12,800.00</td>
						</tr>
						<tr>
							<td>2023-09-30</td>
							<td>Utilities</td>
							<td>500.00</td>
							<td></td>
							<td>12,300.00</td>
						</tr>
					</tbody> -->
				</table>
			</div>
		</div>
	</div>
</div>