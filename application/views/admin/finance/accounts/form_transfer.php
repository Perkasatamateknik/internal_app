<div class="row">
	<div class="col-12">
		<h4 class="font-weight-bold mt-3"><?php echo $breadcrumbs; ?></h4>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-header">
				<a href="#" target="" class="btn btn-tranparent"><i class="fa fa-caret-left" aria-hidden="true"></i> <?= $this->lang->line('ms_title_back'); ?></a>
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
							<label for=""><?= $this->lang->line('ms_title_terget_account'); ?></label>
							<input type="text" name="" id="" class="form-control">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for=""><?= $this->lang->line('ms_title_amount'); ?></label>
							<input type="number" name="" id="" class="form-control">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for=""><?= $this->lang->line('ms_title_transfer_date'); ?></label>
							<input type="datetime-local" name="" id="" class="form-control" value="<?= date('d/m/Y H:i'); ?>">
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
							<label for=""><?= $this->lang->line('ms_title_attachment'); ?></label>
							<input type="file" name="" id="" class="form-control">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for=""><?= $this->lang->line('ms_title_note'); ?></label>
							<textarea name="" id="" cols="30" rows="3" class="form-control"></textarea>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>