<div class="row">
	<div class="col-12">
		<h4 class="font-weight-bold mt-3"><?php echo $breadcrumbs; ?></h4>
	</div>
</div>

<?php $attributes = array('name' => 'receive_form', 'id' => 'receive_form', 'autocomplete' => 'off', 'class' => 'm-b-1 add', 'enctype' => 'multipart/form-data'); ?>
<?php $hidden = array('type' => 'receive', '_token' => $record->receive_id); ?>
<?php echo form_open('admin/finance/accounts/store_trans', $attributes, $hidden); ?>
<div class="row">
	<div class="col-md-12">
		<div class="card mb-3">
			<div class="card-header">
				<a href="<?= base_url('admin/finance/accounts/trans_doc') ?>" target="" class="btn btn-tranparent"><i class="fa fa-caret-left" aria-hidden="true"></i> <?= $this->lang->line('ms_title_back'); ?></a>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="vendor_id"><?= $this->lang->line('ms_title_fund_source'); ?></label>
							<select name="vendor_id" id="vendor_id" class="form-control select2" data-plugin="select_vendor" data-placeholder="<?= $this->lang->line('ms_title_select_options'); ?>" required>
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
							<label for="account_id"><?= $this->lang->line('ms_title_receive_account'); ?></label>
							<select name="receive_account_id" id="receive_account_id" class="form-control select2" data-plugin="select_account" data-placeholder="<?= $this->lang->line('ms_title_select_options'); ?>" required>
							</select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="reference"><?= $this->lang->line('ms_title_ref'); ?></label>
							<input type="text" name="reference" id="reference" class="form-control">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="date"><?= $this->lang->line('ms_title_transfer_date'); ?></label>
							<input type="datetime-local" name="date" id="date" class="form-control">
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
					<div class="col-md-6">
						<div class="row justify-content-end">
							<div class="col-auto">
								<button class="btn btn-secondary" type="submit" name="act_type" value="save"><?= $this->lang->line('ms_title_save_draft'); ?></button>
								<button class="btn btn-primary" type="submit" name="act_type" value="submit"><?= $this->lang->line('ms_title_process'); ?></button>
							</div>
							<div class="col-auto">
							</div>
						</div>
						<!-- <div class="d-flex bottom-right">
						</div> -->
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