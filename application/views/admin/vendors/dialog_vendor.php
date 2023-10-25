<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="<?php echo $this->lang->line('xin_close'); ?>"> <span aria-hidden="true">×</span> </button>
	<h4 class="modal-title" id="edit-modal-data"><?php echo $this->lang->line('ms_edit_vendor') . ' #' . $vendor_name; ?></h4>
</div>
<?php $attributes = array('name' => 'edit_vendor', 'id' => 'edit_vendor', 'autocomplete' => 'off', 'class' => 'm-b-1'); ?>
<?php $hidden = array('_method' => 'EDIT', '_token' => $vendor_id, 'ext_name' => $vendor_id); ?>
<?php echo form_open('admin/vendors/update/' . $vendor_id, $attributes, $hidden); ?>
<div class="modal-body">
	<div class="form-group">
		<label class="form-label"><?php echo $this->lang->line('ms_vendor_name'); ?></label>
		<input type="text" class="form-control" name="vendor_name" placeholder="<?php echo $this->lang->line('ms_vendor_name'); ?>" value="<?= $vendor_name ?>">
	</div>
	<div class="form-group">
		<label class="form-label"><?php echo $this->lang->line('ms_vendor_contact'); ?></label>
		<input type="text" class="form-control" name="vendor_contact" placeholder="<?php echo $this->lang->line('ms_vendor_contact'); ?>" value="<?= $vendor_contact ?>">
	</div>
	<div class="form-group">
		<label class="form-label"><?php echo $this->lang->line('ms_vendor_address'); ?></label>
		<textarea class="form-control" name="vendor_address" placeholder="<?php echo $this->lang->line('ms_vendor_address'); ?>"><?= $vendor_address; ?></textarea>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-md-4">
				<label class="form-label"><?php echo $this->lang->line('xin_city'); ?></label>
				<input class="form-control" placeholder="<?php echo $this->lang->line('xin_city'); ?>" name="city" type="text" value="<?= $city ?>">
			</div>
			<div class="col-md-4">
				<label class="form-label"><?php echo $this->lang->line('xin_state'); ?></label>
				<input class="form-control" placeholder="<?php echo $this->lang->line('xin_state'); ?>" name="state" type="text" value="<?= $state ?>">
			</div>
			<div class="col-md-4">
				<label class="form-label"><?php echo $this->lang->line('xin_zipcode'); ?></label>
				<input class="form-control" placeholder="<?php echo $this->lang->line('xin_zipcode'); ?>" name="zipcode" type="text" value="<?= $zipcode ?>">
			</div>
		</div>
	</div>
	<div class="form-group">
		<label class="form-label"><?php echo $this->lang->line('xin_country'); ?></label>
		<select class="form-control" name="country" data-plugin="select_hrm" data-placeholder="<?php echo $this->lang->line('xin_country'); ?>">
			<option value=""><?php echo $this->lang->line('xin_select_one'); ?></option>
			<?php foreach ($all_countries as $c) { ?>
				<option value="<?php echo $c->country_id; ?>" <?= $country === $c->country_id ? 'selected' : ''; ?>> <?php echo $c->country_name; ?></option>
			<?php } ?>
		</select>
	</div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $this->lang->line('xin_close'); ?></button>
	<button type="submit" class="btn btn-primary"><?php echo $this->lang->line('xin_update'); ?></button>
</div>
<?php echo form_close(); ?>
<script type="text/javascript">
	$(document).ready(function() {

		$('[data-plugin="select_hrm"]').select2($(this).attr("data-options"));
		$('[data-plugin="select_hrm"]').select2({
			width: "100%"
		});

		/* Edit data */
		$("#edit_vendor").submit(function(e) {
			var fd = new FormData(this);
			var obj = $(this),
				action = obj.attr('name');
			fd.append("is_ajax", 1);
			fd.append("edit_type", 'vendors');
			fd.append("form", action);
			e.preventDefault();
			$('.icon-spinner3').show();
			$('.save').prop('disabled', true);
			$.ajax({
				url: e.target.action,
				type: "POST",
				data: fd,
				contentType: false,
				cache: false,
				processData: false,
				success: function(JSON) {
					if (JSON.error != '') {
						toastr.error(JSON.error);
						$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
						$('.save').prop('disabled', false);
						$('.icon-spinner3').hide();
						Ladda.stopAll();
					} else {
						// On page load: datatable
						var xin_table = $('#xin_table_vendors').dataTable({
							"bDestroy": true,
							"ajax": {
								url: "<?php echo site_url("admin/vendors/get_ajax_table") ?>",
								type: 'GET'
							},
							"fnDrawCallback": function(settings) {
								$('[data-toggle="tooltip"]').tooltip();
							}
						});
						xin_table.api().ajax.reload(function() {
							toastr.success(JSON.result);
						}, true);
						$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
						$('.icon-spinner3').hide();
						$('.edit-modal-data').modal('toggle');
						$('.save').prop('disabled', false);
						Ladda.stopAll();
					}
				},
				error: function() {
					toastr.error(JSON.error);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					$('.icon-spinner3').hide();
					$('.save').prop('disabled', false);
					Ladda.stopAll();
				}
			});
		});
	});
</script>