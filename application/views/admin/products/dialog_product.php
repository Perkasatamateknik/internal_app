<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="<?php echo $this->lang->line('xin_close'); ?>"> <span aria-hidden="true">×</span> </button>
	<h4 class="modal-title" id="edit-modal-data"><?php echo $this->lang->line('ms_edit_product') . ' #' . $product_number; ?></h4>
</div>
<?php $attributes = array('name' => 'edit_product', 'id' => 'edit_product', 'autocomplete' => 'off', 'class' => 'm-b-1'); ?>
<?php $hidden = array('_method' => 'EDIT', '_token' => $product_id, 'ext_name' => $product_id); ?>
<?php echo form_open('admin/products/update/' . $product_id, $attributes, $hidden); ?>
<div class="modal-body">
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label class="form-label"><?php echo $this->lang->line('ms_product_number'); ?></label>
				<input type="text" class="form-control" name="product_number" id="product_number" placeholder="<?php echo $this->lang->line('ms_product_number'); ?>" value="KD<?= time() ?>" readonly>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label class="form-label"><?php echo $this->lang->line('ms_product_name'); ?></label>
				<input type="text" class="form-control" name="product_name" id="product_name" placeholder="<?php echo $this->lang->line('ms_product_name'); ?>" value="<?= $product_name ?>">
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label class="form-label"><?php echo $this->lang->line('ms_product_uom'); ?></label>
				<select class="form-control" name="uom_id" data-plugin="select_hrm" data-placeholder="<?php echo $this->lang->line('ms_measurement_units'); ?>">
					<option value=""><?php echo $this->lang->line('xin_select_one'); ?></option>
					<?php foreach ($uoms->result() as $u) { ?>
						<option value="<?php echo $u->uom_id; ?>" <?= $uom_id == $u->uom_id ? 'selected' : '' ?>> <?php echo $u->uom_name; ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label class="form-label"><?php echo $this->lang->line('ms_product_sub_categories'); ?></label>
				<select class="form-control" name="sub_category_id" data-plugin="select_hrm" data-placeholder="<?php echo $this->lang->line('ms_product_sub_categories'); ?>">
					<option value=""><?php echo $this->lang->line('xin_select_one'); ?></option>
					<?php foreach ($sub_categories->result() as $c) { ?>
						<option value="<?php echo $c->sub_category_id; ?>" <?= $c->sub_category_id == $sub_category_id ? 'selected' : ''; ?>> <?php echo $c->sub_category_name; ?></option>
					<?php } ?>
				</select>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label class="form-label"><?php echo $this->lang->line('ms_product_price'); ?></label>
				<input type="number" class="form-control" name="price" placeholder="<?php echo $this->lang->line('ms_product_price'); ?>" value="<?= $price ?>">
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label class="form-label"><?php echo $this->lang->line('ms_product_desc'); ?></label>
				<input type="text" class="form-control" name="product_desc" placeholder="<?php echo $this->lang->line('ms_product_desc'); ?>" value="<?= $product_desc ?>">
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

		$('[data-plugin="select_hrm"]').select2($(this).attr("data-options"));
		$('[data-plugin="select_hrm"]').select2({
			width: "100%"
		});

		/* Edit data */
		$("#edit_product").submit(function(e) {
			var fd = new FormData(this);
			var obj = $(this),
				action = obj.attr('name');
			fd.append("is_ajax", 1);
			fd.append("edit_type", 'products');
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
						var xin_table = $('#xin_table_products').dataTable({
							"bDestroy": true,
							"ajax": {
								url: "<?php echo site_url("admin/products/product_list") ?>",
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