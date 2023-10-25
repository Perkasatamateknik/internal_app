<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="<?php echo $this->lang->line('xin_close'); ?>"> <span aria-hidden="true">×</span> </button>
	<h4 class="modal-title" id="edit-modal-data"><?php echo $this->lang->line('ms_edit_product_sub_categories') . ' #' . $sub_category_name; ?></h4>
</div>
<?php $attributes = array('name' => 'edit_product_sub_categories', 'id' => 'edit_product_sub_categories', 'autocomplete' => 'off', 'class' => 'm-b-1'); ?>
<?php $hidden = array('_method' => 'EDIT', '_token' => $sub_category_id, 'ext_name' => $sub_category_id); ?>
<?php echo form_open('admin/product_categories/update_product_sub_category/' . $sub_category_id, $attributes, $hidden); ?>
<div class="modal-body">
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label class="form-label"><?php echo $this->lang->line('ms_product_categories'); ?></label>
				<select class="form-control" name="category_id" data-plugin="select_hrm" data-placeholder="<?php echo $this->lang->line('ms_product_categories'); ?>">
					<option value=""><?php echo $this->lang->line('xin_select_one'); ?></option>
					<?php foreach ($categories->result() as $c) { ?>
						<option value="<?php echo $c->category_id; ?>" <?= $c->category_id == $category_id ? 'selected' : ''; ?>> <?php echo $c->category_name; ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label class="form-label"><?php echo $this->lang->line('ms_product_sub_categories'); ?></label>
				<input type="text" class="form-control" name="sub_category_name" placeholder="<?php echo $this->lang->line('ms_product_sub_categories'); ?>" value="<?= $sub_category_name ?>">
			</div>
		</div>
	</div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $this->lang->line('xin_close'); ?></button>
	<button type="submit" class="btn btn-primary"><?php echo $this->lang->line('xin_update'); ?></button>
</div>
<?php echo form_close(); ?>
<script type="text/javascript">
	$(document).ready(function() {
		$('[data-plugin="select_hrm"]').select2({
			width: "100%"
		});
		/* Edit data */
		$("#edit_product_sub_categories").submit(function(e) {
			var fd = new FormData(this);
			var obj = $(this),
				action = obj.attr('name');
			fd.append("is_ajax", 1);
			fd.append("edit_type", 'product_sub_category');
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
						var xin_table = $('#xin_table_product_sub_categories').dataTable({
							"bDestroy": true,
							"ajax": {
								url: "<?php echo site_url("admin/product_categories/get_ajax_table_sub/") ?>",
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