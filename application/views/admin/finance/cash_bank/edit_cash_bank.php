<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>


<div class="modal-header">
	<h5 class="modal-title"><?= $this->lang->line('xin_edit'); ?> <?= $this->lang->line('ms_accounts'); ?></h5>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<?php $attributes = array('name' => 'accounts', 'id' => 'edit_account', 'autocomplete' => 'off', 'class' => 'form'); ?>
<?php $hidden = ['account_id' => $record->account_id]; ?>
<?php echo form_open('admin/finance/cash_bank/update', $attributes, $hidden); ?>
<div class="modal-body">
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label for="account_name">Nama Akun</label>
				<input type="text" name="account_name" id="account_name" class="form-control" value="<?= $record->account_name; ?>" required>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label for="account_code">Code</label>
				<input type="text" name="account_code" id="account_code" class="form-control" value="<?= $record->account_code; ?>" required>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label for="account_number">Account Number</label>
				<input type="text" name="account_number" id="account_number" class="form-control" value="<?= $record->account_number; ?>" required>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label for="account_origin">Account Origin</label>
				<select class="form-control" name="account_origin" id="account_origin" data-plugin="select_account" data-placeholder="Pilih Akun">
				</select>
			</div>
			<button type="button" class="btn btn-sm btn-warning" id="reset"><?= $this->lang->line('xin_reset'); ?> Account Origin</button>
		</div>
	</div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	<button type="submit" class="btn btn-primary save"><?= $this->lang->line('xin_save'); ?></button>
</div>
<?php echo form_close(); ?>

<script>
	$('[data-plugin="select_hrm"]').select2();

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

	$("#edit_account").submit(function(e) {
		var fd = new FormData(this);
		var obj = $(this),
			action = obj.attr("name");
		fd.append("is_ajax", 1);
		fd.append("form", action);
		e.preventDefault();
		$(".icon-spinner3").show();
		$(".save").prop("disabled", true);
		$.ajax({
			url: e.target.action,
			type: "POST",
			data: fd,
			contentType: false,
			cache: false,
			processData: false,
			success: function(JSON) {
				if (JSON.error != "") {
					toastr.error(JSON.error);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					$(".save").prop("disabled", false);
					$(".icon-spinner3").hide();
					Ladda.stopAll();
				} else {
					// On page load: datatable
					toastr.options = {
						timeOut: 500,
						onHidden: function() {
							window.location.href = '';
						},
					};
					toastr.success(JSON.result);
					$("#show_modal_data").modal("hide");
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					$(".icon-spinner3").hide();
					$(".save").prop("disabled", false);
					Ladda.stopAll();


				}
			},
			error: function(xhr, status, error) {
				toastr.error("Error: " + status + " | " + error);
				$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
				$(".icon-spinner3").hide();
				$(".save").prop("disabled", false);
				Ladda.stopAll();
			},
		});
	});

	$('#reset').on('click', function() {
		$('[data-plugin="select_account"]').val('').trigger('change');
		e.preventDefault();
	})
</script>