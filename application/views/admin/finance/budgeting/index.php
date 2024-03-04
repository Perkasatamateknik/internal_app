<div class="row">
	<div class="col-md-12">
		<div class="card border-primary-right mb-3">
			<div class="card-body">
				<div class="row justify-content-between align-content-center">
					<div class="col-md-auto">
						<div class="d-flex h-100">
							<h4 class="p-0 m-0"><?= $breadcrumbs; ?></h4>
							<a href="<?= base_url('admin/finance/budgeting/create'); ?>" target="" class="btn btn-primary">
								<i class="fa fa-plus" aria-hidden="true"></i> <?= $this->lang->line('xin_insert'); ?>
							</a>
						</div>
					</div>
					<div class="col-md-auto">
						<a href="<?= base_url('admin/finance/budgeting/create'); ?>" target="" class="btn btn-primary">
							<i class="fa fa-plus" aria-hidden="true"></i> <?= $this->lang->line('xin_insert'); ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<?php foreach ($records as $r) { ?>
		<div class="col-md-12">
			<div class="card rounded-3 mb-3">
				<div class="card-body">
					<div class="row justify-content-between">
						<div class="col-md-auto">
							<h4><?= $r->year; ?> <?= $this->lang->line('ms_title_budget'); ?></h4>
						</div>
						<div class="col-md-auto">
							<a href="<?= base_url('admin/finance/budgeting/create'); ?>" target="" class="btn btn-white"><i class="fa fa-chevron-right" aria-hidden="true"></i> <?= $this->lang->line('ms_title_add_budget'); ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php }; ?>
</div>