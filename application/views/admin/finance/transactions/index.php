<?php
/* Employees view
*/
?>
<?php $session = $this->session->userdata('username'); ?>
<?php $get_animate = $this->Xin_model->get_content_animate(); ?>
<?php $role_resources_ids = $this->Xin_model->user_role_resource(); ?>
<?php $user_info = $this->Xin_model->read_user_info($session['user_id']); ?>
<?php $system = $this->Xin_model->read_setting_info(1); ?>
<?= $id = $this->input->get('id') ?? false; ?>

<div class="row">
	<div class="col-md-4">
		<div class="form-group">
			<h4><?= $breadcrumbs; ?></h4>
		</div>
	</div>
	<div class="col-md-8">
		<div class="row justify-content-end">
			<div class="col-md-auto mx-0 px-1">
				<style>
					.has-search .form-control {
						padding-left: 2.375rem;
					}

					.has-search .form-control-feedback {
						position: absolute;
						z-index: 2;
						display: block;
						width: 2.375rem;
						height: 2.375rem;
						line-height: 2.375rem;
						text-align: center;
						pointer-events: none;
						color: #aaa;
					}
				</style>
				<div class="form-group has-search">
					<span class="fa fa-search form-control-feedback"></span>
					<input type="text" class="form-control" placeholder="Search">
				</div>
			</div>
			<div class="col-md-auto mx-0 px-1">
				<a href="<?= base_url('admin/finance/accounts'); ?>" target="" class="btn btn-white"><i class="fa fa-caret-left" aria-hidden="true"></i> <?= $this->lang->line('ms_title_back'); ?></a>
			</div>
			<div class="col-md-auto mx-0 px-1">
				<a href="#" class="btn btn-white" data-toggle="modal" data-target="#modalAdd"><i class="fas fa-exchange-alt"></i> <?= $this->lang->line('ms_title_add_trans'); ?></a>
			</div>
			<div class="col-md-auto mx-0 px-1">
				<a href="#" target="" class="btn btn-white"><i class="fa fa-print" aria-hidden="true"></i> <?= $this->lang->line('ms_title_add_trans'); ?></a>
			</div>
			<!-- <div class="col-md-auto mx-0 px-1">
				<a href="#" target="" class="btn btn-transparent"><i class="fa fa-cog"></i></a>
			</div> -->
		</div>
	</div>
</div>
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">

				<div class="table-responsive">
					<table class="table table-hover table-striped" id="ms_table">
						<thead>
							<tr>
								<th>#</th>
								<th><?= $this->lang->line('ms_title_date'); ?></th>
								<th><?= $this->lang->line('ms_title_type'); ?></th>
								<th><?= $this->lang->line('ms_title_desc'); ?></th>
								<th><?= $this->lang->line('ms_title_ref'); ?></th>
								<th><?= $this->lang->line('ms_title_debit'); ?></th>
								<th><?= $this->lang->line('ms_title_credit'); ?></th>
								<th><?= $this->lang->line('ms_title_balance'); ?></th>
							</tr>
						</thead>
						<tbody>

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalAdd" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<!-- <div class="modal-header">
				<h5 class="modal-title">sm</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div> -->
			<div class="modal-body">
				<a href="<?= base_url('admin/finance/accounts/create_trans?id=' . $id . '&type=transfer') ?>" class="btn btn-block btn-white text-left"><i class="fa fa-exchange-alt fa-fw mr-3" aria-hidden="true"></i><?= $this->lang->line('ms_title_transfer'); ?></a>
				<!-- <br> -->
				<a href="<?= base_url('admin/finance/accounts/create_trans?id=' . $id . '&type=spend') ?>" class="btn btn-block btn-white text-left"><i class="fa fa-paper-plane fa-fw mr-3" aria-hidden="true"></i><?= $this->lang->line('ms_title_spend'); ?></a>
				<!-- <br> -->
				<a href="<?= base_url('admin/finance/accounts/create_trans?id=' . $id . '&type=receive') ?>" class="btn btn-block btn-white text-left"><i class="fas fa-hand-holding-usd fa-fw mr-3"></i><?= $this->lang->line('ms_title_receive'); ?></a>
				<!-- <br> -->
			</div>
		</div>
	</div>
</div>