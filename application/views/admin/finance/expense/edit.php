<div class="row">
	<div class="col-12">
		<h4 class="font-weight-bold mt-3"><?php echo $breadcrumbs; ?></h4>
	</div>
</div>



<!-- Modal -->
<div class="modal fade" id="modelId" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Modal title</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				Body
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary">Save</button>
			</div>
		</div>
	</div>
</div>
<?php $attributes = array('name' => 'expense_form', 'id' => 'expense_form', 'autocomplete' => 'off', 'class' => 'm-b-1 add', 'enctype' => 'multipart/form-data'); ?>
<?php $hidden = array('expense'  => 'UPDATE', '_token' => $record->trans_number); ?>
<?php echo form_open('admin/finance/expenses/store', $attributes, $hidden); ?>
<div class="row">
	<div class="col-md-12">
		<div class="card mb-3">
			<div class="card-header">
				<a href="javascript:history.back()" class="btn btn-tranparent"><i class="fa fa-caret-left" aria-hidden="true"></i> <?= $this->lang->line('ms_title_back'); ?></a>
			</div>
			<!-- Button trigger modal -->
			<button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#modelId">
				Launch
			</button>
			<div class="card-body">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="account_id"><?= $this->lang->line('ms_title_source_account'); ?></label>
							<input type="text" class="form-control" value="<?= $record->source_account; ?>" readonly>
							<input type="hidden" name="account_id" value="<?= $record->source_account; ?>">
							<input type="hidden" name="trans_number" id="trans_number" value="<?= $record->trans_number; ?>">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="trans_number"><?= $this->lang->line('ms_title_number_document'); ?></label>
							<input type="text" class="form-control" readonly value="<?= $record->trans_number; ?>">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="beneficiary"><?= $this->lang->line('ms_title_beneficiary'); ?></label>
							<select name="beneficiary" id="beneficiary" class="form-control" data-plugin="terget_account" data-placeholder="<?= $this->lang->line('ms_title_terget_account'); ?>" required>
								<option value="<?= $record->beneficiary; ?>"><?= $record->beneficiary_name; ?></option>
							</select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="reference"><?= $this->lang->line('ms_title_ref'); ?></label>
							<input type="text" name="reference" id="reference" class="form-control" value="<?= $record->reference; ?>">
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="date"><?= $this->lang->line('ms_title_transfer_date'); ?></label>
							<input type="date" name="date" id="date" class="form-control" value="" value="<?= $record->date; ?>">
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="due_date"><?= $this->lang->line('ms_title_transfer_due_date'); ?></label>
							<input type="date" name="due_date" id="due_date" class="form-control" value="<?= $record->due_date; ?>">
						</div>
					</div>
					<div class=" col-md-4">
						<?php
						$diff = get_termin($record->date, $record->due_date);
						?>
						<div class="form-group">
							<label for="select_due_date" class="control-label"><?php echo $this->lang->line('xin_select'); ?> <?php echo $this->lang->line('xin_invoice_due_date'); ?></label>
							<select class="form-control" name="select_due_date" id="select_due_date" data-plugin="select_hrm" data-placeholder="<?php echo $this->lang->line('xin_invoice_due_date'); ?>">
								<option value="0" selected><?= $this->lang->line('ms_custom'); ?></option>
								<option value="1" data-type="days" <?= $record->term == 1 && $diff['type'] == 'days' ? 'selected' : '' ?>>1 <?= $this->lang->line('xin_day'); ?></option>
								<option value="3" data-type="days" <?= $record->term == 3 && $diff['type'] == 'days' ? 'selected' : '' ?>>3 <?= $this->lang->line('xin_day'); ?></option>
								<option value="7" data-type="days" <?= $record->term == 7 && $diff['type'] == 'days' ? 'selected' : '' ?>>7 <?= $this->lang->line('xin_day'); ?></option>
								<option value="10" data-type="days" <?= $record->term == 10 && $diff['type'] == 'days' ? 'selected' : '' ?>>10 <?= $this->lang->line('xin_day'); ?></option>
								<option value="15" data-type="days" <?= $record->term == 15 && $diff['type'] == 'days' ? 'selected' : '' ?>>15 <?= $this->lang->line('xin_day'); ?></option>
								<option value="20" data-type="days" <?= $record->term == 20 && $diff['type'] == 'days' ? 'selected' : '' ?>>20 <?= $this->lang->line('xin_day'); ?></option>
								<option value="1" data-type="months" <?= $record->term == 1 && $diff['type'] == 'months' ? 'selected' : '' ?>>1 <?= $this->lang->line('xin_month'); ?></option>
								<option value="3" data-type="months" <?= $record->term == 3 && $diff['type'] == 'months' ? 'selected' : '' ?>>3 <?= $this->lang->line('xin_month'); ?></option>
								<option value="6" data-type="months" <?= $record->term == 6 && $diff['type'] == 'months' ? 'selected' : '' ?>>6 <?= $this->lang->line('xin_month'); ?></option>
								<option value="9" data-type="months" <?= $record->term == 9 && $diff['type'] == 'months' ? 'selected' : '' ?>>9 <?= $this->lang->line('xin_month'); ?></option>
								<option value="1" data-type="years" <?= $record->term == 1 && $diff['type'] == 'years' ? 'selected' : '' ?>>1 <?= $this->lang->line('xin_year'); ?></option>
								<option value="2" data-type="years" <?= $record->term == 2 && $diff['type'] == 'years' ? 'selected' : '' ?>>2 <?= $this->lang->line('xin_year'); ?></option>
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
					<div class="col-md-6">
						<div class="row justify-content-end">
							<div class="col-auto">
								<button class="btn btn-primary" type="submit" name="act_type" value="submit"><?= $this->lang->line('xin_save'); ?></button>
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


<!-- Button trigger modal -->
<button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#modelId">
	Launch
</button>

<!-- Modal -->
<div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Modal title</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="container-fluid">
					Add rows here
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary">Save</button>
			</div>
		</div>
	</div>
</div>

<script>
	$('#exampleModal').on('show.bs.modal', event => {
		var button = $(event.relatedTarget);
		var modal = $(this);
		// Use above variables to manipulate the DOM

	});
</script>