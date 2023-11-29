$(function () {
	$('[data-plugin="select_account"]').select2({
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
			cache: true,
			transport: function (params, success, failure) {
				var $request = $.ajax(params);

				$request.then(success);
				$request.fail(failure);

				return $request;
			},
		},
		width: "100%",
		// language: {
		// 	noResults: function () {
		// 		return `<li><button style="width: 100%" type="button"
		//     class="btn btn-tranparent btn-sm"
		//     onClick='addVendor()'><span class="ion ion-md-add"></span> Add Vendor</button>
		//     </li>`;
		// 	},
		// },
	});

	$('[data-plugin="terget_account"]').select2({
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
				console.log(data);
				return {
					results: data,
				};
			},
			cache: true,
			transport: function (params, success, failure) {
				var $request = $.ajax(params);

				$request.then(success);
				$request.fail(failure);

				return $request;
			},
		},
		width: "100%",
	});

	// submit insert or edit
	$("#transfers_form").submit(function (e) {
		/*Form Submit*/
		e.preventDefault();
		var obj = $(this),
			action = obj.attr("name");
		var formData = new FormData(obj[0]);
		formData.append("form", action);
		jQuery(".save").prop("disabled", true);
		$(".icon-spinner3").show();
		jQuery.ajax({
			type: "POST",
			enctype: "multipart/form-data",
			url: e.target.action,
			data: formData,
			cache: false,
			processData: false, // Important for FormData
			contentType: false, // Important for FormData
			success: function (JSON) {
				if (JSON.error != "") {
					toastr.error(JSON.error);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					$(".save").prop("disabled", false);
					$(".icon-spinner3").hide();
					Ladda.stopAll();
				} else {
					toastr.options = {
						timeOut: 500,
						onHidden: function () {
							window.location.href =
								site_url +
								"finance/accounts/transactions?id=" +
								$("input[name='trans_number']").val();
						},
					};
					console.log(JSON.result);
					toastr.success(JSON.result);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					$(".icon-spinner3").hide();
					// $("#purchase_orders")[0].reset(); // To reset form fields
					$(".save").prop("disabled", false);
					Ladda.stopAll();
				}
			},
			error: function (xhr, status, error) {
				toastr.error("Error: " + status + " | " + error);
				$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
				$(".icon-spinner3").hide();
				$(".save").prop("disabled", false);
				Ladda.stopAll();
			},
		});
	});
});

//required fields

$(document).ready(function () {
	var span = '<span class="text-danger">*</span>';
	var required = $(".form-control[required]");
	required.closest(".form-group").find("label").append(span);
});

$(document).ready(function () {
	var id = getUrlParameter("id");
	$("#ms_table").DataTable({
		ajax: {
			url: site_url + "finance/accounts/get_ajax_account_transfer/",
			data: {
				id: id,
			},
			type: "GET",
		},
		fnDrawCallback: function (settings) {
			$('[data-toggle="tooltip"]').tooltip();
		},
	});
});

/* ----------------------- CALCULATE ITEMS ----------------------------- */

var formatter = new Intl.NumberFormat("id-ID", {
	style: "currency",
	currency: type_currency,
	minimumFractionDigits: 2,
});
function formatCurrency(number) {
	return formatter.format(number);
}

function formatCurrencyNumber(number) {
	return number.toLocaleString("id-ID", { minimumFractionDigits: 2 });
}

// Function to add a new row to the table body
function addRow() {
	// Get the table body element
	var tbody = $("#target_accounts > tbody");

	// Get the number of rows in the table body
	var rowCount = tbody.children().length;

	var rowId = "row-" + rowCount;
	// Create a new row element with a unique id attribute
	var newRow = `<tr class="item-row" id="item-row-${rowCount}" data-id="${rowCount}">
		<td>
			<select class="form-control row_target_id" data-plugin="select_target_account" name="row_target_id[]" id="row_target_id_${rowCount}" onchange="select_product(this)" value="0" data-placeholder="Select Account" required>
			</select>
		</td>
		<td>
			<input type="text" name="row_note" id="row_note" class="form-control" required>
		</td>
		<td>
			<select class="form-control row_tax_id" id="row_tax_id_${rowCount}" data-plugin="select_tax" name="row_tax_id[]" onchange="select_tax(this)">
				<option value="">${ms_select_tax}</option>
			</select>
			<input type="hidden" class="row_tax_rate" name="row_tax_rate[]" id="row_tax_rate_${rowCount}" value="0">
			<input type="hidden" class="data_tax_rate" value="0">
			<input type="hidden" class="data_tax_type" value="fixed"><br>
			<strong class="row_tax_rate_show currency" style="font-size:10px"></strong>
		</td>

		<td>
			<input type="number" min="0" class="row_amount form-control" name="row_amount[]" id="row_amount_${rowCount}" value="0">
		</td>
		<td style="text-align:center">
			<input type="hidden" class="row_item_pi_id" name="row_item_pi_id[]" value="0">
			<input type="hidden" class="row_type" name="row_type[]" value="INSERT">
			<button type="button" class="btn icon-btn btn-danger waves-effect waves-light remove-item"> <span class="fa fa-minus"></span></button>
		</td>
		</tr>`;

	// Add the row to the table body
	tbody.append(newRow);

	$('[data-plugin="select_target_account"]').select2({
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

	$('[data-plugin="select_tax"]').select2({
		ajax: {
			delay: 250,
			url: site_url + "ajax_request/find_tax",
			data: function (params) {
				var queryParameters = {
					query: params.term,
				};
				return queryParameters;
			},
			processResults: function (data) {
				// return {
				// 	results: data,
				// };
				var options = [];

				data.forEach(function (item) {
					options.push({
						id: item.id,
						text: item.text,
						customAttribute: item.rate,
					});
				});

				return {
					results: options,
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

	var rowAmountSelect = $("#row_amount_" + rowCount);
	rowAmountSelect
		.closest("td")
		.find(".currency")
		.text(formatCurrency(rowAmountSelect.val()));

	// update_total();
}

// remove item
$(document).on("click", ".remove-item", function () {
	// if ($(this).data("ajax")) {
	// 	var conf = confirm("Are you sure you want to delete this item?");
	// 	if (conf == true) {
	// 		var id = $(this).data("id");
	// 		var row = $(this).closest("tr");
	// 		$.ajax({
	// 			url: site_url + "ajax_request/delete_item_pi",
	// 			type: "POST",
	// 			data: {
	// 				csrf_hrsale: $('input[name="csrf_hrsale"]').val(),
	// 				id: id,
	// 			},
	// 			success: function (JSON) {
	// 				row.fadeOut(300, function () {
	// 					$(this).remove();
	// 					update_total();
	// 				});
	// 				toastr.success(JSON.result);
	// 				$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
	// 			},
	// 			error: function (jqXHR, textStatus, errorThrown) {
	// 				toastr.error("Error: " + textStatus + " | " + error);
	// 				$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
	// 			},
	// 		});
	// 	}
	// } else {
	// }
	$(this)
		.closest(".item-row")
		.fadeOut(300, function () {
			$(this).remove();
			update_total();
		});
});

// Fungsi edit otomatic kaluasi saat load
$(document).on("load", function () {
	addRow();
});
