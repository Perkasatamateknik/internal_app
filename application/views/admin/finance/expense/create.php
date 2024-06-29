<div class="row">
	<div class="col-12">
		<h4 class="font-weight-bold mt-3"><?php echo $breadcrumbs; ?></h4>
	</div>
</div>

<?php $attributes = array('name' => 'expense_form', 'id' => 'expense_form', 'autocomplete' => 'off', 'class' => 'm-b-1 add', 'enctype' => 'multipart/form-data'); ?>
<?php $hidden = array('type' => 'expense', '_token' => $record->trans_number); ?>
<?php echo form_open('admin/finance/expenses/store', $attributes, $hidden); ?>
<div class="row">
	<div class="col-md-12">
		<div class="card mb-3">
			<div class="card-header">
				<a href="javascript:history.back()" class=" btn btn-tranparent"><i class="fa fa-caret-left" aria-hidden="true"></i> <?= $this->lang->line('ms_title_back'); ?></a>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="account_id"><?= $this->lang->line('ms_title_source_account'); ?></label>
							<select name="account_id" id="account_id" class="form-control select2" data-plugin="select_account" data-placeholder="<?= $this->lang->line('ms_title_select_options'); ?>" required>
							</select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="trans_number"><?= $this->lang->line('ms_title_number_document'); ?></label>
							<input type="text" name="trans_number" id="trans_number" class="form-control" readonly value="<?= $record->trans_number; ?>">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="beneficiary"><?= $this->lang->line('ms_title_beneficiary'); ?></label>
							<select name="beneficiary" id="beneficiary" class="form-control" data-plugin="target_contact" data-placeholder="<?= $this->lang->line('ms_title_terget_account'); ?>" required>
							</select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="reference"><?= $this->lang->line('ms_title_ref'); ?></label>
							<input type="text" name="reference" id="reference" class="form-control" value="">
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="date"><?= $this->lang->line('ms_title_transfer_date'); ?></label>
							<input type="date" name="date" id="date" class="form-control" value="<?= date('Y-m-d'); ?>">
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="due_date"><?= $this->lang->line('ms_title_transfer_due_date'); ?></label>
							<input type="date" name="due_date" id="due_date" class="form-control">
						</div>
					</div>
					<div class=" col-md-4">
						<div class="form-group">
							<label for="select_due_date" class="control-label"><?php echo $this->lang->line('xin_select'); ?> <?php echo $this->lang->line('xin_invoice_due_date'); ?></label>
							<select class="form-control" name="select_due_date" id="select_due_date" data-plugin="select_hrm" data-placeholder="<?php echo $this->lang->line('xin_invoice_due_date'); ?>">
								<option value="0" selected><?= $this->lang->line('ms_custom'); ?></option>
								<option value="1" data-type="days">1 <?= $this->lang->line('xin_day'); ?></option>
								<option value="3" data-type="days">3 <?= $this->lang->line('xin_day'); ?></option>
								<option value="7" data-type="days">7 <?= $this->lang->line('xin_day'); ?></option>
								<option value="10" data-type="days">10 <?= $this->lang->line('xin_day'); ?></option>
								<option value="15" data-type="days">15 <?= $this->lang->line('xin_day'); ?></option>
								<option value="20" data-type="days">20 <?= $this->lang->line('xin_day'); ?></option>
								<option value="1" data-type="months">1 <?= $this->lang->line('xin_month'); ?></option>
								<option value="3" data-type="months">3 <?= $this->lang->line('xin_month'); ?></option>
								<option value="6" data-type="months">6 <?= $this->lang->line('xin_month'); ?></option>
								<option value="9" data-type="months">9 <?= $this->lang->line('xin_month'); ?></option>
								<option value="1" data-type="years">1 <?= $this->lang->line('xin_year'); ?></option>
								<option value="2" data-type="years">2 <?= $this->lang->line('xin_year'); ?></option>
							</select>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
<div class="row pb-3">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<div class="row pb-5">
					<div class="col-12">
						<table class="table table-borderless mx-0 mb-0" id="target_accounts">
							<thead class="thead-light">
								<tr>
									<th><?= $this->lang->line('ms_title_account'); ?></th>
									<th><?= $this->lang->line('ms_title_note'); ?></th>
									<th><?= $this->lang->line('ms_title_tax'); ?></th>
									<th><?= $this->lang->line('ms_title_amount'); ?></th>
									<th><?= $this->lang->line('xin_action'); ?></th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
						<button type="button" class="btn btn-secondary ml-2 mt-3" onclick="addRow()"><?= $this->lang->line('xin_add_more'); ?></button>
					</div>
				</div>
				<style>
					.bottom-right {
						/* position: fixed; */
						bottom: 0;
						right: 0;
						margin: 10px;
					}
				</style>
				<div class="row justify-content-end">
					<div class="col-md-6">
						<label for=""><?= $this->lang->line('ms_title_attachment'); ?></label>
						<div id="fileUpload" class="file-container">
						</div>
					</div>
					<div class="col-md-6 align-content-end">
						<div class="row justify-content-end">
							<div class="col-md-8 align-content-end">
								<table class="table table-borderless">
									<tr>
										<td class="text-right">
											<div class="row justify-content-between">
												<div class="col-auto">
													<strong class="pr-3"><?php echo $this->lang->line('xin_amount'); ?></strong>
												</div>
												<div class="col-auto">
													<input type="hidden" name="amount" value="0" id="amount">
													<strong id="amount_show" class="currency">0</strong>
												</div>
											</div>
										</td>
									</tr>
								</table>
								<div class="row">
									<div class="col-6 pr-1">
										<button class="btn btn-secondary btn-block" type="submit" name="act_type" value="save"><?= $this->lang->line('ms_title_save_draft'); ?></button>
									</div>
									<div class="col-6 pl-1">
										<button class="btn btn-primary btn-block" type="submit" name="act_type" value="submit"><?= $this->lang->line('ms_title_process'); ?></button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row pb-3">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<table class="table table-md table-hover table-stripped" id="table_preview">
					<thead class="thead-light">
						<tr>
							<th>#</th>
							<th style="width: 30%;">File Name</th>
							<th>Preview</th>
							<th style="width: 20%;">Size</th>
							<th>Type</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?php echo form_close(); ?>