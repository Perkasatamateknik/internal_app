<?php
/* Employees view
*/
?>
<?php $session = $this->session->userdata('username'); ?>
<?php $get_animate = $this->Xin_model->get_content_animate(); ?>
<?php $role_resources_ids = $this->Xin_model->user_role_resource(); ?>
<?php $user_info = $this->Xin_model->read_user_info($session['user_id']); ?>
<?php $system = $this->Xin_model->read_setting_info(1); ?>
<?php $id = $this->input->get('id') ?? false; ?>
<div class="row">

	<div class="col-12">
		<div class="form-group">
			<h4><?= $breadcrumbs; ?></h4>
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
								<th><?= $this->lang->line('ms_title_number_document'); ?></th>
								<th><?= $this->lang->line('ms_title_transfer'); ?></th>
								<th><?= $this->lang->line('ms_title_ref'); ?></th>
								<th><?= $this->lang->line('ms_title_status'); ?></th>
								<th style="min-width: 150px;"><?= $this->lang->line('ms_title_amount'); ?></th>
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