<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>


<div class="modal-header">
	<h5 class="modal-title"><?= $this->lang->line('xin_add_new'); ?> <?= $this->lang->line('ms_title_cash_bank'); ?></h5>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<?php $attributes = array('name' => 'accounts', 'id' => 'add_account', 'autocomplete' => 'off', 'class' => 'form'); ?>
<?php echo form_open('admin/finance/accounts/insert', $attributes); ?>
<div class="modal-body">
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label for="account_name">Nama Akun</label>
				<input type="text" name="account_name" id="account_name" class="form-control" required>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label for="account_code">Code</label>
				<input type="text" name="account_code" id="account_code" class="form-control" required>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label for="account_number">Account Number</label>
				<input type="text" name="account_number" id="account_number" class="form-control" required>
				<input type="hidden" name="expenditure_type" value="null">
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label for="category_id">Select Category</label>
				<select class="form-control" name="category_id" id="category_id" data-plugin="select_cat" data-placeholder="Pilih">

				</select>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label for="account_origin">Account Origin</label>
				<select class="form-control" name="account_origin" id="account_origin" data-plugin="select_account" data-placeholder="Pilih Akun">
					<option><?= $this->lang->line('ms_title_select_options'); ?></option>
				</select>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label for="saldo_awal">Saldo Awal</label>
				<input type="number" min="0" name="saldo_awal" id="saldo_awal" class="form-control">
			</div>
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
	$('[data-plugin="select_cat"]').select2({
		ajax: {
			delay: 250,
			url: site_url + "ajax_request/find_account_category_cash_bank",
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

	$('[data-plugin="select_account"]').select2({
		ajax: {
			delay: 250,
			url: site_url + "ajax_request/get_bank_account",
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

	$("#add_account").submit(function(e) {
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
					toastr.success(JSON.result);
					$("#show_modal_data").modal("hide");
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					$(".icon-spinner3").hide();
					$(".add-modal-data").modal("toggle");
					$(".save").prop("disabled", false);
					Ladda.stopAll();
					toastr.options = {
						timeOut: 500,
						onHidden: function() {
							window.location.href = '';
						},
					};
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
</script>