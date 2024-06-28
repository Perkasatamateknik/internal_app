<!-- <div class="row">
	<div class="col-12">
		<h4 class="font-weight-bold mt-3"><?php echo $breadcrumbs; ?></h4>
	</div>
</div> -->
<?php $attributes = array('name' => 'receivables_form', 'id' => 'receivables_form', 'autocomplete' => 'off', 'class' => 'm-b-1 add', 'enctype' => 'multipart/form-data'); ?>
<?php $hidden = array('type' => 'receivables', '_token' => $record->trans_number); ?>
<?php echo form_open('admin/contacts/receivable_store', $attributes, $hidden); ?>
<div class="row">
	<div class="col-md-12">
		<div class="card mb-3">
			<div class="card-header">
				<a href="javascript:history.back()" target="" class="btn btn-tranparent"><i class="fa fa-caret-left" aria-hidden="true"></i> <?= $this->lang->line('ms_title_back'); ?></a>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label for="account_id"><?= $this->lang->line('ms_title_customer'); ?></label><br>
							<input type="hidden" name="contact_id" value="<?= $contact->contact_id; ?>">
							<?= contact_url($contact->contact_id, $contact->contact_name); ?>
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label for="date"><?= $this->lang->line('ms_title_date'); ?></label>
							<input type="date" name="date" id="date" class="form-control" value="<?= date("Y-m-d"); ?>" required>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="due_date"><?= $this->lang->line('ms_title_due_date'); ?></label>
							<input type="date" name="due_date" id="due_date" class="form-control" required>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="trans_number"><?= $this->lang->line('ms_title_number_document'); ?></label>
							<input type="text" name="trans_number" id="trans_number" class="form-control" readonly value="<?= $record->trans_number; ?>">
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label for="reference"><?= $this->lang->line('ms_title_ref'); ?></label>
							<input type="text" name="reference" id="reference" class="form-control">
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
								<button type="submit" class="btn btn-primary btn-block"> <?php echo $this->lang->line('xin_save'); ?> </button>
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