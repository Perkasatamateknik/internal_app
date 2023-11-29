<?php $id = $this->input->get('id');

if ($id == '') {
	redirect('admin/finance/accounts');
}
?>
<div class="row">
	<div class="col-12">
		<h4 class="font-weight-bold mt-3"><?php echo $breadcrumbs; ?></h4>
	</div>
</div>
<?php $attributes = array('name' => 'transfers_form', 'id' => 'transfers_form', 'autocomplete' => 'off', 'class' => 'm-b-1 add', 'enctype' => 'multipart/form-data'); ?>
<?php $hidden = array('type' => 'transfer', '_token' => $record->transfer_id); ?>
<?php echo form_open('admin/finance/accounts/store_trans', $attributes, $hidden); ?>
<div class="row">
	<div class="col-md-12 mb-3">
		<div class="card">
			<div class="card-header">
				<a href="<?= base_url('admin/finance/accounts/transactions?id=' . $id) ?>" target="" class="btn btn-tranparent"><i class="fa fa-caret-left" aria-hidden="true"></i> <?= $this->lang->line('ms_title_back'); ?></a>
			</div>
			<div class="card-body">


				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for=""><?= $this->lang->line('ms_title_source_account'); ?></label>
							<select name="account_id" id="account_id" class="form-control select2" data-plugin="select_account" data-placeholder="<?= $this->lang->line('ms_title_select_options'); ?>" required>
							</select>
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
							</select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="amount"><?= $this->lang->line('ms_title_amount'); ?></label>
							<input type="number" name="amount" id="amount" class="form-control" required>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="date"><?= $this->lang->line('ms_title_transfer_date'); ?></label>
							<input type="datetime-local" name="date" id="date" class="form-control" value="<?= date('d/m/Y H:i'); ?>" required>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="ref"><?= $this->lang->line('ms_title_ref'); ?></label>
							<input type="text" name="ref" id="ref" class="form-control" required>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for=""><?= $this->lang->line('ms_title_attachment'); ?></label>
							<input type="file" name="" id="" class="form-control">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="note"><?= $this->lang->line('ms_title_note'); ?></label>
							<textarea name="note" id="note" cols="30" rows="3" class="form-control"></textarea>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<button class="btn btn-primary" type="submit"><?= $this->lang->line('ms_title_process'); ?></button>
						<button class="btn btn-secondary" type="submit"><?= $this->lang->line('ms_title_save_draft'); ?></button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-12">
		<div class="card">
			<div class="card-body">
				<div class="row" id="placeAttachment">
					<style>
						.inputfile-box {
							position: relative;
						}

						.inputfile {
							display: none;
						}

						.container {
							display: inline-block;
							width: 100%;
						}

						.file-box {
							display: inline-block;
							width: 100%;
							border: 1px solid;
							padding: 5px 0px 5px 5px;
							box-sizing: border-box;
							height: calc(2rem - 2px);
						}

						.file-button {
							background: red;
							padding: 5px;
							position: absolute;
							border: 1px solid;
							top: 0px;
							right: 0px;
						}
					</style>
					<div class="col-md-12">
						<div class="form-group">
							<label for="attachment"><?= $this->lang->line('ms_title_attachment'); ?></label>
							<input type="file" name="attachment[]" id="attachment" class="form-control">
						</div>
					</div>
					<div class="inputfile-box">
						<input type="file" id="file" class="inputfile" onchange='uploadFile(this)'>
						<label for="file">
							<span id="file-name" class="file-box"></span>
							<span class="file-button">
								<i class="fa fa-upload" aria-hidden="true"></i>
								Select File
							</span>
						</label>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<button class="btn btn-success btn-sm" type="button" onclick="addAttachment()"><?= $this->lang->line('xin_title_add_item'); ?></button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php echo form_close(); ?>