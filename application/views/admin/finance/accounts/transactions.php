<?php
/* Employees view
*/
?>
<?php $session = $this->session->userdata('username'); ?>
<?php $get_animate = $this->Xin_model->get_content_animate(); ?>
<?php $role_resources_ids = $this->Xin_model->user_role_resource(); ?>
<?php $user_info = $this->Xin_model->read_user_info($session['user_id']); ?>
<?php $system = $this->Xin_model->read_setting_info(1); ?>
<?php $id = $this->input->get('id'); ?>

<div class="row">
	<div class="col-md-4 col-sm-12">
		<div class="form-group">
			<h4><?= $breadcrumbs; ?></h4>
		</div>
	</div>
	<div class="col-md-8 col-sm-12">
		<div class="row justify-content-end">
			<div class="col-md-auto">
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
					<input type="search" class="form-control" placeholder="Search" name="cari_data" id="cari_data">
				</div>
			</div>
			<div class=" col-md-auto mx-0 px-1">
				<a href="<?= base_url('admin/finance/accounts'); ?>" target="" class="btn btn-white"><i class="fa fa-caret-left" aria-hidden="true"></i> <?= $this->lang->line('ms_title_back'); ?></a>
			</div>
			<div class="col-md-auto mx-0 px-1">
				<div class="dropdown">
					<button class="btn btn-white dropdown-toggle" type="button" id="triggerTrans" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fas fa-exchange-alt"></i> <?= $this->lang->line('ms_title_add_trans'); ?>
					</button>
					<div class="dropdown-menu" aria-labelledby="triggerTrans">
						<a class="dropdown-item" href="<?= base_url('admin/finance/accounts/create_trans?type=transfer') ?>"><i class="fa fa-exchange-alt fa-fw mr-3" aria-hidden="true"></i><?= $this->lang->line('ms_title_transfer'); ?></a>
						<!-- <br> -->
						<a class="dropdown-item" href="<?= base_url('admin/finance/accounts/create_trans?type=spend') ?>" class="btn btn-block btn-white text-left"><i class="fa fa-paper-plane fa-fw mr-3" aria-hidden="true"></i><?= $this->lang->line('ms_title_spend'); ?></a>
						<!-- <br> -->
						<a class="dropdown-item" href="<?= base_url('admin/finance/accounts/create_trans?type=receive') ?>" class="btn btn-block btn-white text-left"><i class="fas fa-hand-holding-usd fa-fw mr-3"></i><?= $this->lang->line('ms_title_receive'); ?></a>
						<!-- <br> -->
					</div>
				</div>
			</div>
			<div class="col-md-auto mx-0 px-1">
				<a href="<?= base_url('admin/finance/accounts/print?id=' . $id) ?>" target="" class="btn btn-white"><i class="fa fa-print" aria-hidden="true"></i> <?= $this->lang->line('ms_title_print_trans'); ?></a>
			</div>
			<div class="col-md-auto mx-0 px-1">
				<a href="#" target="" class="btn btn-transparent"><i class="fa fa-cog"></i></a>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<div class="row mb-3">
					<div class="col-md-auto mx-0 px-1">
						<a href="<?= base_url('admin/finance/accounts'); ?>" target="" class="btn btn-light"><i class="fa fa-caret-left" aria-hidden="true"></i> <?= $this->lang->line('ms_title_back'); ?></a>
					</div>
					<div class="col-md-auto mx-0 px-1">
						<div class="dropdown">
							<button class="btn btn-light dropdown-toggle" type="button" id="triggerTrans" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i class="fas fa-exchange-alt"></i> <?= $this->lang->line('ms_title_add_trans'); ?>
							</button>
							<div class="dropdown-menu" aria-labelledby="triggerTrans">
								<a class="dropdown-item" href="<?= base_url('admin/finance/accounts/create_trans?type=transfer') ?>"><i class="fa fa-exchange-alt fa-fw mr-3" aria-hidden="true"></i><?= $this->lang->line('ms_title_transfer'); ?></a>
								<!-- <br> -->
								<a class="dropdown-item" href="<?= base_url('admin/finance/accounts/create_trans?type=spend') ?>" class="btn btn-block btn-light text-left"><i class="fa fa-paper-plane fa-fw mr-3" aria-hidden="true"></i><?= $this->lang->line('ms_title_spend'); ?></a>
								<!-- <br> -->
								<a class="dropdown-item" href="<?= base_url('admin/finance/accounts/create_trans?type=receive') ?>" class="btn btn-block btn-light text-left"><i class="fas fa-hand-holding-usd fa-fw mr-3"></i><?= $this->lang->line('ms_title_receive'); ?></a>
								<!-- <br> -->
							</div>
						</div>
					</div>
					<div class="col-md-auto mx-0 px-1">
						<a href="<?= base_url('admin/finance/accounts/print?id=' . $id) ?>" target="" class="btn btn-light"><i class="fa fa-print" aria-hidden="true"></i> <?= $this->lang->line('ms_title_print_trans'); ?></a>
					</div>
					<div class="col-md-auto mx-0 px-1">
						<a href="#" target="" class="btn btn-transparent"><i class="fa fa-cog"></i></a>
					</div>
				</div>
				<!-- <hr> -->
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