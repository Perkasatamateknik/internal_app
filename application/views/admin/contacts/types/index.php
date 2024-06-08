<?php
$role_resources_ids = $this->Xin_model->user_role_resource();; ?>

<div class="row">
	<div class="col-12">
		<h4 class="font-weight-bold mt-3"><?php echo $breadcrumbs; ?></h4>
	</div>
</div>
<div class="row">
	<?php if ($form) { ?>
		<div class="col-md-4">
			<?php echo $form; ?>
		</div>
	<?php }; ?>

	<div class="col-md-<?= $form == false ? 12 : 8 ?>">
		<div class=" card">
			<div class="card-body">
				<table class="table table-md table-hover table-stripped" id="table_preview">
					<thead class="thead-light">
						<tr>
							<th><?= $this->lang->line('xin_action'); ?></th>
							<th><?= $this->lang->line('ms_title_contact_type'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($records->result() as $i => $r) {; ?>
							<tr>
								<td>
									<?php if (in_array('539', $role_resources_ids)) { ?>
										<span data-toggle="tooltip" data-placement="top" title="<?= $this->lang->line('xin_edit') ?>">
											<a class="btn icon-btn btn-sm btn-outline-secondary waves-effect waves-light" href="<?= site_url() . 'admin/contacts/types?edit=' . $r->type_id  ?>">
												<span class="fas fa-pencil-alt"></span>
											</a>
										</span>
									<?php }; ?>

									<?php if (in_array('540', $role_resources_ids)) {; ?>
										<?php if ($r->is_editable) {; ?>
											<span data-toggle="tooltip" data-placement="top" title="<?= $this->lang->line('xin_delete') ?>"><button type="button" class="btn icon-btn btn-sm btn-outline-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="<?= $r->type_id ?>" data-record="<?= $r->contact_type; ?>" data-token_type="contact_type"><span class="fas fa-trash-restore"></span></button></span>
										<?php }; ?>
									<?php }; ?>
								</td>
								<td><?= $r->contact_type; ?></td>
							</tr>
						<?php }; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>