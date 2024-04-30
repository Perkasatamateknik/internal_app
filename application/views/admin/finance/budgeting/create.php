<div class="row">
	<div class="col-md-6">
		<div class="form-group">
			<h4><?= $breadcrumbs; ?></h4>
		</div>
	</div>
</div>

<div class="row mb-3">
	<div class="col-md-12">
		<div class="card">
			<div class="card-body">
				<div class="row">
					
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row mb-3">
	<div class="col-md-12">
		<button class="btn btn-primary" onclick="addCardBudget()">
			<i class="fa fa-plus" aria-hidden="true"></i>
			<?= $this->lang->line('xin_add_new'); ?>
			<?= $this->lang->line('xin_departments'); ?>
		</button>
	</div>
</div>

<script>
	var ms_title_select_department = "<?= $this->lang->line('ms_title_select_department'); ?>";
	var ms_title_budget_name = "<?= $this->lang->line('ms_title_budget_name'); ?>";
	var ms_title_account = "<?= $this->lang->line('ms_title_account'); ?>";
	var ms_select_account = "<?= $this->lang->line('ms_select_account'); ?>";
	var ms_title_amount = "<?= $this->lang->line('ms_title_amount'); ?>";
	var xin_title_add_item = "<?= $this->lang->line('xin_title_add_item'); ?>";
</script>
