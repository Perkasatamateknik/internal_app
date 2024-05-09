<div class="row">
	<div class="col-12">
		<h4 class="font-weight-bold mt-3"><?php echo $breadcrumbs; ?></h4>
	</div>
</div>
<?php $attributes = array('name' => 'transfers_form', 'id' => 'transfers_form', 'autocomplete' => 'off', 'class' => 'm-b-1 add', 'enctype' => 'multipart/form-data'); ?>
<?php $hidden = array('type' => 'transfer', '_token' => $record->trans_number); ?>
<?php echo form_open('admin/finance/accounts/transfer_update', $attributes, $hidden); ?>
<input type="hidden" value="<?= $record->account_id; ?>" name="account_id">
<input type="hidden" value="<?= $record->terget_account_id; ?>" name="old_target_account">
<div class="row">
	<div class="col-md-12 mb-3">
		<div class="card">
			<div class="card-header">
				<a href="<?= base_url('admin/finance/accounts/trans_doc') ?>" target="" class="btn btn-tranparent"><i class="fa fa-caret-left" aria-hidden="true"></i> <?= $this->lang->line('ms_title_back'); ?></a>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for=""><?= $this->lang->line('ms_title_source_account'); ?></label>
							<input type="text" value="<?= $record->source_account; ?>" class="form-control" readonly>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="trans_number"><?= $this->lang->line('ms_title_number_document'); ?></label>
							<input type="text" name="trans_number" id="trans_number" class="form-control" value="<?= $record->trans_number; ?>" required readonly>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="terget_account"><?= $this->lang->line('ms_title_terget_account'); ?></label>
							<select name="target_account" id="terget_account" class="form-control" data-plugin="terget_account" data-placeholder="<?= $this->lang->line('ms_title_terget_account'); ?>" required>
								<option value="<?= $record->terget_account_id; ?>"><?= $record->target_account; ?></option>
							</select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="amount"><?= $this->lang->line('ms_title_amount'); ?></label>
							<input type="number" name="amount" id="amount" min="0" class="form-control number-separator" required value="<?= $record->amount; ?>">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="date"><?= $this->lang->line('ms_title_transfer_date'); ?></label>
							<input type="date" name="date" id="date" class="form-control" value="<?= $record->date ?>" required>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="ref"><?= $this->lang->line('ms_title_ref'); ?></label>
							<input type="text" name="ref" id="ref" class="form-control" value="<?= $record->ref; ?>" required>
						</div>
					</div>
					<div class="col-md-6">
						<label for="note"><?= $this->lang->line('ms_title_attachment'); ?></label>

						<div id="fileUpload" class="file-container">
						</div>
						<div class="mt-3" id="preview-upload">

						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="note"><?= $this->lang->line('ms_title_note'); ?></label>
							<textarea name="note" id="note" cols="30" rows="3" class="form-control"><?= $record->note; ?></textarea>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div id="fileUpload" class="file-container">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-12 mb-3">
		<div class="card">
			<div class="card-body">
				<div class="row" id="preview-upload">
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
							<?php foreach ($attachments as $i =>  $a) {; ?>
								<tr>
									<td><?= $i += 1; ?></td>
									<td><?= $a->file_name; ?></td>
									<td><?= $a->file_name; ?></td>
								</tr>
							<?php }; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-12">
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-12">
						<button class="btn btn-primary" type="submit" name="act_type" value="save"><?= $this->lang->line('ms_title_save_change'); ?></button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php echo form_close(); ?>