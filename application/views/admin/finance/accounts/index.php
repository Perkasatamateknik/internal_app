<div class="row">
	<div class="col-md-12">
		<div class="card mb-3">
			<div class="card-header with-elements"> <span class="card-header-title mr-2"><strong><?php echo $this->lang->line('ms_title_cash_bank'); ?></strong></span>
				<div class="card-header-elements ml-md-auto">
					<a href="#" class="btn btn-primary btn-sm">
						<span class="ion ion-md-add"></span> Saldo Awal
					</a>
					&nbsp;
					<a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addAccount">
						<span class="ion ion-md-add"></span> Add Account
					</a>
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

<div class="modal fade" id="addAccount" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Tambah Akun</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<?php $attributes = array('name' => 'accounts', 'id' => 'add_account', 'autocomplete' => 'off', 'class' => 'form'); ?>
			<?php echo form_open('admin/finance/accounts/insert', $attributes); ?>
			<div class="modal-body">
				<div class="form-group">
					<label for="account_name">Nama Akun</label>
					<input type="text" name="account_name" id="account_name" class="form-control" required>
				</div>
				<div class="form-group">
					<label for="account_code">Code</label>
					<input type="text" name="account_code" id="account_code" class="form-control" required>
				</div>
				<div class="form-group">
					<label for="account_number">Account Number</label>
					<input type="text" name="account_number" id="account_number" class="form-control" required>
				</div>
				<div class="form-group">
					<label for="category_id">Select Category</label>
					<select class="form-control" name="category_id" id="category_id" data-plugin="select_hrm" data-placeholder="Pilih">
						<?php foreach ($categories as $r) {; ?>
							<option value="<?= $r->category_id; ?>"><?= $r->category_name; ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-group">
					<label for="account_origin">Account Origin</label>
					<input type="text" name="account_origin" id="account_origin" class="form-control" required>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Save</button>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>