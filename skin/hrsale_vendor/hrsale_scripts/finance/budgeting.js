$(function () {
	$('[data-plugin="select_hrm"]').select2({
		width: "100%",
	});

	// modal add account
	$("#add-modal-data").on("show.bs.modal", function (event) {
		$.ajax({
			url: site_url + "finance/budgeting/modal_budgeting/",
			type: "GET",
			success: function (response) {
				if (response) {
					$("#add_ajax_modal").html(response);
				}
			},
		});
	});

	$("#show_modal").on("show.bs.modal", function () {
		$.ajax({
			url: site_url + "finance/budgeting/modal_budgeting_data/",
			type: "GET",
			data: {
				id: budget_id,
			},
			success: function (response) {
				if (response) {
					$("#show_modal_data").html(response);
				}
			},
		});
	});
});

function addRow() {
	// Get the table body element
	var card = "#table_budget";
	var tbody = $(card + " > tbody");

	// Get the number of rows in the table body
	var rowCount = tbody.children().length;

	var data_plugin = "data-plugin='select_account_" + rowCount + "'";

	var urutan = rowCount + 1;
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
				<input type="text" name="row_amount[]" id="row_amount_${rowCount}" class="form-control" value="0">
			</div>
		</td>
		<td style="text-align:center">
			<input type="hidden" class="row_item_pi_id" name="row_item_pi_id[]" value="0">
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
			data: function (params) {
				var queryParameters = {
					query: params.term,
				};
				return queryParameters;
			},
			processResults: function (data) {
				return {
					results: data,
				};
			},
			cache: false,
			transport: function (params, success, failure) {
				var $request = $.ajax(params);

				$request.then(success);
				$request.fail(failure);

				return $request;
			},
		},
		width: "100%",
	});
}
