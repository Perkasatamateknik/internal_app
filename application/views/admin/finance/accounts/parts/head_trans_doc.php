<?php
$type = $this->input->get('type') ?? ""; ?>

<div class="row">
	<div class="col-md-4">
		<div class="form-group">
			<h4><?= $breadcrumbs; ?></h4>
		</div>
	</div>
	<div class="col-md-8">
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
					<input type="text" class="form-control" placeholder="Search">
				</div>
			</div>
			<div class="col-md-auto mx-0 px-1">
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
<hr>
<div class="row">
	<div class="col-md-auto mr-0 mb-2 pr-0">
		<a href="<?= base_url('admin/finance/accounts/trans_doc') ?>" target="" class="btn btn-ajax-trans btn-<?= $type == "" ? "primary" : "white"; ?>"><?= $this->lang->line('ms_title_all'); ?></a>
	</div>
	<div class="col-md-auto mr-0 mb-2 pr-0">
		<a href="<?= base_url('admin/finance/accounts/trans_doc?type=transfer') ?>" id="transfer" class="btn btn-ajax-trans btn-<?= $type == "transfer" ? "primary" : "white"; ?>"><?= $this->lang->line('ms_title_transfer'); ?></a>
	</div>
	<div class="col-md-auto mr-0 mb-2 pr-0">
		<a href="<?= base_url('admin/finance/accounts/trans_doc?type=spend') ?>" id="spend" class="btn btn-ajax-trans btn-<?= $type == "spend" ? "primary" : "white"; ?>"><?= $this->lang->line('ms_title_spend'); ?></a>
	</div>
	<div class="col-md-auto mr-0 mb-2 pr-0">
		<a href="<?= base_url('admin/finance/accounts/trans_doc?type=receive') ?>" id="receive" class="btn btn-ajax-trans btn-<?= $type == "receive" ? "primary" : "white"; ?>"><?= $this->lang->line('ms_title_receive'); ?></a>
	</div>
</div>