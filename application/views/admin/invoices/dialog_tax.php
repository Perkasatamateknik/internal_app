<?php
defined('BASEPATH') or exit('No direct script access allowed');
if (isset($_GET['jd']) && isset($_GET['tax_id']) && $_GET['data'] == 'tax') {
?>

	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
		<h4 class="modal-title" id="edit-modal-data"><?php echo $this->lang->line('xin_title_edit_tax'); ?></h4>
	</div>
	<?php $attributes = array('name' => 'edit_tax', 'id' => 'edit_tax', 'autocomplete' => 'off', 'class' => 'm-b-1'); ?>
	<?php $hidden = array('_method' => 'EDIT', '_token' => $tax_id); ?>
	<?php echo form_open('admin/invoices/update_tax/' . $tax_id, $attributes, $hidden); ?>
	<div class="modal-body">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="tax_name"><?php echo $this->lang->line('xin_title_tax_name'); ?></label>
					<input type="text" class="form-control" name="tax_name" placeholder="<?php echo $this->lang->line('xin_title_tax_name'); ?>" value="<?php echo $name; ?>">
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="tax_rate"><?php echo $this->lang->line('xin_title_tax_rate'); ?></label>
					<input type="text" class="form-control" name="tax_rate" placeholder="<?php echo $this->lang->line('xin_title_tax_rate'); ?>" value="<?php echo $rate; ?>">
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="tax_type"><?php echo $this->lang->line('xin_invoice_tax_type'); ?></label>
					<select class="form-control" name="tax_type" data-plugin="select_hrm" data-placeholder="<?php echo $this->lang->line('xin_invoice_tax_type'); ?>">
						<option value=""></option>
						<option value="fixed" <?php if ($type == 'fixed') { ?> selected="selected" <?php } ?>><?php echo $this->lang->line('xin_title_tax_fixed'); ?></option>
						<option value="percentage" <?php if ($type == 'percentage') { ?> selected="selected" <?php } ?>><?php echo $this->lang->line('xin_title_tax_percent'); ?></option>
					</select>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="tax_rate"><?php echo $this->lang->line('ms_title_tax_withholding'); ?></label>
					<br>
					<input type="checkbox" name="is_withholding" data-toggle="toggle" data-size="sm" value="1" <?= $is_withholding == 1 ? 'selected' : ''; ?>>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="pembelian_account_id"><?php echo $this->lang->line('ms_title_tax_buy_account'); ?></label>
					<select class="form-control" name="pembelian_account_id" data-plugin="select_account" data-placeholder="<?php echo $this->lang->line('ms_title_tax_buy_account'); ?>" data-old="<?= $buy_account; ?>">
					</select>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="penjualan_account_id"><?php echo $this->lang->line('ms_title_tax_sell_account'); ?></label>
					<select class="form-control" name="penjualan_account_id" data-plugin="select_account" data-placeholder="<?php echo $this->lang->line('ms_title_tax_sell_account'); ?>" data-old="<?= $sell_account; ?>">
					</select>
				</div>
			</div>
			<div class="col-md-6">

				<div class="form-group">
					<label for="message"><?php echo $this->lang->line('xin_description'); ?></label>
					<textarea class="form-control" placeholder="<?php echo $this->lang->line('xin_description'); ?>" name="description" id="description2"><?php echo $description; ?></textarea>
				</div>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $this->lang->line('xin_close'); ?></button>
		<button type="submit" class="btn btn-primary"><?php echo $this->lang->line('xin_update'); ?></button>
	</div>
	<?php echo form_close(); ?>

	<script>
		$.get({
			url: site_url + "ajax_request/find_account_by_id",
			data: "query=" + <?= $buy_account; ?>,
			dataType: "JSON",
			success: function(res) {
				var selectedOptionId = res.account_id;
				var selectedOptionText = res.account_name;

				var option = new Option(
					selectedOptionText,
					selectedOptionId,
					true,
					true
				);
				$("[name='pembelian_account_id']").append(option).trigger("change");
			},
			error: function(xhr, status, error) {
				toastr.error("Error: " + status + " | " + error);
				$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
				$(".icon-spinner3").hide();
				$(".save").prop("disabled", false);
				Ladda.stopAll();
			},
		});
	</script>
	<script>
		$.get({
			url: site_url + "ajax_request/find_account_by_id",
			data: "query=" + <?= $sell_account; ?>,
			dataType: "JSON",
			success: function(res) {
				var selectedOptionId = res.account_id;
				var selectedOptionText = res.account_name;

				var option = new Option(
					selectedOptionText,
					selectedOptionId,
					true,
					true
				);
				$("[name='penjualan_account_id']").append(option).trigger("change");
			},
			error: function(xhr, status, error) {
				toastr.error("Error: " + status + " | " + error);
				$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
				$(".icon-spinner3").hide();
				$(".save").prop("disabled", false);
				Ladda.stopAll();
			},
		});
	</script>

	<script>
		$('[data-plugin="select_account"]').select2({
			ajax: {
				delay: 250,
				url: site_url + "ajax_request/get_accounts",
				data: function(params) {
					var queryParameters = {
						query: params.term,
					};
					return queryParameters;
				},

				processResults: function(data) {
					return {
						results: data,
					};
				},
				cache: true,
				transport: function(params, success, failure) {
					var $request = $.ajax(params);

					$request.then(success);
					$request.fail(failure);

					return $request;
				},
			},
			width: "100%",
		});
	</script>
	<script type="text/javascript">
		$(document).ready(function() {

			// On page load: datatable
			var xin_table = $('#xin_table').dataTable({
				"bDestroy": true,
				"ajax": {
					url: "<?php echo site_url("admin/invoices/taxes_list") ?>",
					type: 'GET'
				},
				"fnDrawCallback": function(settings) {
					$('[data-toggle="tooltip"]').tooltip();
				}
			});

			$('[data-plugin="select_hrm"]').select2($(this).attr('data-options'));
			$('[data-plugin="select_hrm"]').select2({
				width: '100%'
			});

			/* Edit data */
			$("#edit_tax").submit(function(e) {
				/*Form Submit*/
				e.preventDefault();
				var obj = $(this),
					action = obj.attr('name');
				$('.save').prop('disabled', true);
				$.ajax({
					type: "POST",
					url: e.target.action,
					data: obj.serialize() + "&is_ajax=2&edit_type=tax&form=" + action,
					cache: false,
					success: function(JSON) {
						if (JSON.error != '') {
							toastr.error(JSON.error);
							$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
							$('.save').prop('disabled', false);
						} else {
							$('.edit-modal-data').modal('toggle');
							xin_table.api().ajax.reload(function() {
								toastr.success(JSON.result);
								$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
							}, true);
							$('.save').prop('disabled', false);
						}
					}
				});
			});
		});
	</script>

	<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>

<?php }
?>