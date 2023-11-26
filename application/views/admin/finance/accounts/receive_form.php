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
<div class="row">
	<div class="col-md-12">
		<div class="card mb-3">
			<div class="card-header">
				<a href="<?= base_url('admin/finance/accounts/transactions?id=' . $id) ?>" target="" class="btn btn-tranparent"><i class="fa fa-caret-left" aria-hidden="true"></i> <?= $this->lang->line('ms_title_back'); ?></a>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for=""><?= $this->lang->line('ms_title_source_account'); ?></label>
							<input type="text" name="" id="" class="form-control">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for=""><?= $this->lang->line('ms_title_number_document'); ?></label>
							<input type="text" name="" id="" class="form-control">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for=""><?= $this->lang->line('ms_title_beneficiary'); ?></label>
							<input type="text" name="" id="" class="form-control">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for=""><?= $this->lang->line('ms_title_ref'); ?></label>
							<input type="text" name="" id="" class="form-control">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for=""><?= $this->lang->line('ms_title_transfer_date'); ?></label>
							<input type="datetime-local" name="" id="" class="form-control" value="<?= date('d/m/Y H:i'); ?>">
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<div class="row pb-5">
					<div class="col-12">
						<table class="table table-borderless mx-0 mb-0">
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
								<tr>
									<td>
										<div class="form-group">
											<input type="text" name="" id="" class="form-control">
										</div>
									</td>
									<td>
										<div class="form-group">
											<input type="text" name="" id="" class="form-control">
										</div>
									</td>
									<td>
										<div class="form-group">
											<input type="text" name="" id="" class="form-control">
										</div>
									</td>
									<td>
										<div class="form-group">
											<input type="text" name="" id="" class="form-control">
										</div>
									</td>
									<td>
										<button type="button" name="" id="" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></button>
									</td>
								</tr>
							</tbody>
						</table>
						<button type="button" class="btn btn-secondary ml-2"><?= $this->lang->line('xin_add_more'); ?></button>
					</div>
				</div>
				<div class="row justify-content-end">
					<div class="col-md-6">
						<div class="form-group">
							<label for=""><?= $this->lang->line('ms_title_attachment'); ?></label>
							<input type="file" name="" id="" class="form-control">
						</div>
					</div>
					<div class="col-md-6">
						<div class="bottom-right">
							<button class="btn btn-primary float-right btn-block" type="submit"><?= $this->lang->line('xin_save'); ?></button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>