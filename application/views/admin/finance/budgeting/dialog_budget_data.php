<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>


<div class="modal-header">
	<h5 class="modal-title"><?= $this->lang->line('ms_title_add_budget_data'); ?></h5>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<?php $attributes = array('name' => 'budgeting', 'id' => 'add_budget_data', 'autocomplete' => 'off', 'class' => 'form'); ?>
<?php echo form_open('admin/finance/budgeting/insert_budget_data', $attributes); ?>
<div class="modal-body">
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label for="department_id"><?= $this->lang->line('xin_departments'); ?></label><br>
				<select name="department_id" id="department_id" data-plugin="department_data" class="form-control">

				</select>
			</div>
		</div>
	</div>
	<div class=" row mb-3">
		<div class="col-md-12">
			<table class="table w-100" id="table_budget">
				<thead>
					<tr>
						<th style="min-width: 30%;"><?= $this->lang->line('ms_title_budget_name'); ?></th>
						<th style="min-width: 30%;"><?= $this->lang->line('ms_title_accounts'); ?></th>
						<th style="min-width: 30%;"><?= $this->lang->line('ms_title_amount'); ?></th>
						<td style="min-width: 10%;"></td>
					</tr>
				</thead>
				<tbody>

				</tbody>
				<tfoot>
					<tr>
						<td colspan="2" class="text-center">
							<b><?= $this->lang->line('xin_title_total'); ?></b>
						</td>
						<td>
							<b><?= $this->Xin_model->currency_sign(0); ?></b>
						</td>
					</tr>
				</tfoot>
			</table>
			<input type="hidden" name="budget_id" value="<?= $this->input->get('id'); ?>">
		</div>
	</div>
	<div class="row mb-3">
		<div class="col-md-12">
			<a href="#" class="btn btn-primary" onclick="addRow()">
				<i class="fa fa-plus" aria-hidden="true"></i>
				<?= $this->lang->line('xin_add_new'); ?>
			</a>
		</div>
	</div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	<button type="submit" class="btn btn-primary save"><?= $this->lang->line('xin_save'); ?></button>
</div>
<?php echo form_close(); ?>

<script>
	var ms_title_select_department = "<?= $this->lang->line('ms_title_select_department'); ?>";
	var ms_title_budget_name = "<?= $this->lang->line('ms_title_budget_name'); ?>";
	var ms_title_account = "<?= $this->lang->line('ms_title_account'); ?>";
	var ms_select_account = "<?= $this->lang->line('ms_select_account'); ?>";
	var ms_title_amount = "<?= $this->lang->line('ms_title_amount'); ?>";
	var xin_title_add_item = "<?= $this->lang->line('xin_title_add_item'); ?>";
</script>

<script>
	$('[data-plugin="select_hrm"]').select2();
	$("#add_budget_data").submit(function(e) {
		var fd = new FormData(this);
		var obj = $(this),
			action = obj.attr("name");
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
							window.location.href =
								site_url +
								"finance/budgeting/view?id=" +
								JSON.id;
						},
					};
					toastr.success(JSON.result);
					$("#add_ajax_modal").modal("hide");

					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					$(".icon-spinner3").hide();
					$(".add-modal-data").modal("toggle");
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

	function addRow() {
		// Get the table body element
		var card = "#table_budget";
		var tbody = $(card + " > tbody");

		// Get the number of rows in the table body
		var rowCount = tbody.children().length;
		var urutan = rowCount + 1;

		var data_plugin =
			"data-plugin='select_account_" + "_" + rowCount + "'";

		var rowId = "row-" + rowCount;
		// Create a new row element with a unique id attribute
		var newRow = `<tr class="item-row" id="item-row-${rowCount}" data-id="${rowCount}">
		<td>
			<div class="form-group">
				<input type="text" name="row_budget_name[]" id="row_budget_name_${rowCount}" class="form-control" placeholder="${ms_title_budget_name} ${urutan}">
			</div>
		</td>
		<td>
			<select class="form-control row_account" ${data_plugin} name="row_account[]" id="row_account_${rowCount}">
				<option value="" selected disabled>${ms_select_account}</option>
			</select>
		</td>
		<td>
			<div class="form-group">
				<input type="number" name="row_amount[]" id="row_amount_${rowCount}" class="form-control" value="0">
			</div>
		</td>
		<td style="text-align:center">
			<input type="hidden" class="row_type" name="row_type[]" value="INSERT">
			<button type="button" class="btn icon-btn btn-danger waves-effect waves-light remove-item"> <span class="fa fa-minus"></span></button>
		</td>
		</tr>`;

		// Add the row to the table body
		tbody.append(newRow);

		// $('[data-plugin="select_account"]').select2({
		$("[" + data_plugin + "]").select2({
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
				cache: false,
				transport: function(params, success, failure) {
					var $request = $.ajax(params);

					$request.then(success);
					$request.fail(failure);

					return $request;
				},
			},
			width: "100%",
		});
	}

	$(document).ready(function() {
		addRow();
	});

	$(function() {
		$('[data-plugin="department_data"]').select2({
			ajax: {
				delay: 250,
				url: site_url + "/finance/budgeting/find_department/",
				data: function(params) {
					var queryParameters = {
						query: params.term,
						id: <?= $this->input->get('id'); ?>
					};
					return queryParameters;
				},

				processResults: function(data) {
					console.log(data);
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
	});
</script>