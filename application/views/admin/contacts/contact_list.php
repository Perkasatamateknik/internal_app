<?php $session = $this->session->userdata('username'); ?>
<?php $get_animate = $this->Xin_model->get_content_animate(); ?>
<?php $role_resources_ids = $this->Xin_model->user_role_resource(); ?>
<?php $user_info = $this->Xin_model->read_user_info($session['user_id']); ?>
<?php $system = $this->Xin_model->read_setting_info(1); ?>
<?php
// reports to 

$reports_to = get_reports_team_data($session['user_id']); ?>
<script src="<?php echo base_url(); ?>skin/hrsale_vendor/assets/vendor/libs/chartjs/chartjs.js"></script>

<div class="row mb-3">
	<div class="col-md-12">
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-md-10">
						<div class="d-flex align-content-center">
							<div class="col-md-4">
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
									<input type="text" class="form-control" placeholder="Search" id="cari_data">
								</div>
							</div>
							<div class="col-md-auto mx-0 px-1">
								<div class="dropdown open">
									<button class="btn btn-light dropdown-toggle" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										Filter
									</button>
									<div class="dropdown-menu" aria-labelledby="triggerId">
										<button class="dropdown-item" href="#">Action</button>
										<button class="dropdown-item disabled" href="#">Disabled action</button>
									</div>
								</div>
							</div>
							<div class="col-md-auto">
								<b class="text-large"><?= $count_contacts; ?></b><br>
								<span><?= $this->lang->line('ms_title_contact'); ?></span>
							</div>
						</div>
					</div>
					<div class="col-md-2">
						<a name="" id="" class="btn btn-primary" href="#" role="button" onclick="modalAdd()">
							<i class="fa fa-plus" aria-hidden="true"></i>
							<?= $this->lang->line('xin_add_new'); ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-body">
				<div class="table-responsive">
					<table class="table" id="ms_table">
						<thead>
							<tr>
								<th><?= $this->lang->line('xin_action'); ?></th>
								<th><?= $this->lang->line('ms_title_name_and_type'); ?></th>
								<th><?= $this->lang->line('ms_title_company'); ?></th>
								<th><?= $this->lang->line('ms_title_contact'); ?></th>
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

<div id="modal-view"></div>