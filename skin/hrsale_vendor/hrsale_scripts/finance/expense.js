$(function () {
	$('[data-plugin="select_account"]').select2({
		ajax: {
			delay: 250,
			url: site_url + "ajax_request/get_bank_account",
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
	});

	$('[data-plugin="target_contact"]').select2({
		ajax: {
			delay: 250,
			url: site_url + "ajax_request/find_contact",
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
		templateResult: function (data) {
			return data.html;
		},
		templateSelection: function (data) {
			return data.text;
		},
		language: {
			noResults: function () {
				return `</li><button style="width: 100%" type="button"
        class="btn btn-transparent" 
        onclick='addContact()'><span class="ion ion-md-add"></span> Add Contact</button>
        </li>`;
			},
		},

		escapeMarkup: function (markup) {
			return markup;
		},
		width: "100%",
	});

	$('[data-plugin="select_hrm"]').select2({
		width: "100%",
	});

	// submit insert or edit
	$("#expense_form").submit(function (e) {
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
								"finance/expenses/view?id=" +
								$("input[name='trans_number']").val();
						},
					};
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

	$("#selectAll").click(function (e) {
		if ($(this).is(":checked")) {
			$(".select_id").prop("checked", true);
		} else {
			$(".select_id").prop("checked", false);
		}
	});
});

$(function () {
	$("#select_due_date").on("change", function () {
		var duration = parseInt($("#select_due_date").val());
		var startDate = new Date($("#date").val());
		var durationType = $("#select_due_date").find(":selected").data("type");

		if (isNaN(duration) || isNaN(startDate.getTime())) {
			alert("Please enter valid input");
			return;
		}

		var dueDate = calculateDueDate(startDate, duration, durationType);
		$("#due_date").val(dueDate.toISOString().split("T")[0]);
	});
	$("#due_date").on("change", function () {
		$("#select_due_date").val(0).trigger();
	});
});

function modalImport() {
	$.ajax({
		type: "GET",
		url: site_url + "/finance/expenses/ajax_modal_import",
		dataType: "json",
		success: function (response) {
			$("#modal-view").html(response.data);
			$("#modal-import").modal({
				backdrop: "static",
				keyboard: false,
			});

			$("#modal-import").modal("show");
		},
	});
}

function modalBulkPayment(id) {
	console.log(id);
	$.ajax({
		type: "GET",
		url: site_url + "/finance/expenses/ajax_modal_bulk_payment",
		data: {
			id: id,
		},
		dataType: "json",
		success: function (response) {
			$("#modal-view").html(response.data);
			$("#modal-import").modal({
				backdrop: "static",
				keyboard: false,
			});

			$("#modal-import").modal("show");
		},
	});
}

// $(document).ready(function () {
// 	$("#table_form").submit(function (event) {
// 		event.preventDefault();
// 		var selected = [];
// 		$(".select_id:checked").each(function () {
// 			selected.push($(this).val());
// 		});

// 		if (selected.length > 0) {
// 			$.ajax({
// 				url: site_url + "/finance/expenses/ajax_modal_bulk_payment",
// 				type: "GET",
// 				data: { expense_ids: selected },
// 				success: function (data) {},
// 			});
// 		} else {
// 			alert("Please select at least one expense.");
// 		}
// 	});
// });

$("#bulk_payment").click(function () {
	var selected = [];
	$(".select_id:checked").each(function () {
		selected.push($(this).val());
	});

	if (selected.length > 0) {
		$.ajax({
			url: site_url + "/finance/expenses/ajax_modal_bulk_payment",
			type: "get",
			dataType: "json",
			data: { select_id: selected },
			success: function (response) {
				$("#modal-view").html(response.data);
				$("#modal-bulk").modal({
					backdrop: "static",
					keyboard: false,
				});

				$("#modal-bulk").modal("show");
				$("#selectAll").prop("checked", false);
			},
		});
	} else {
		toastr.error("Please select at least one expense.");
	}
});

function handleBulkAction(actionUrl) {
	var selected = [];
	$(".selected_id:checked").each(function () {
		selected.push($(this).val());
	});

	if (selected.length > 0) {
		$.ajax({
			url: actionUrl,
			type: "GET",
			data: { expense_ids: selected },
			success: function (data) {
				var response = JSON.parse(data);
				if (response.status === "success") {
					alert("Action completed successfully.");
					location.reload(); // Reload the page to reflect changes
				}
			},
		});
	} else {
		alert("Please select at least one expense.");
	}
}

function calculateDueDate(startDate, duration, durationType) {
	var dueDate = new Date(startDate);

	if (durationType === "days") {
		dueDate.setDate(dueDate.getDate() + duration);
	} else if (durationType === "months") {
		dueDate.setMonth(dueDate.getMonth() + duration);
	} else if (durationType === "years") {
		dueDate.setFullYear(dueDate.getFullYear() + duration);
	}

	return dueDate;
}

//required fields

$(document).ready(function () {
	var span = '<span class="text-danger">*</span>';
	var required = $(".form-control[required]");
	required.closest(".form-group").find("label").append(span);
});

$(document).ready(function () {
	// On page load:
	var otable = $("#ms_table").DataTable({
		bDestroy: true,
		ajax: {
			url: site_url + "finance/expenses/get_ajax_expenses/",
			type: "GET",
			data: {
				filter: $('input[name="filter"]').val(),
			},
		},
		fnDrawCallback: function (settings) {
			$('[data-toggle="tooltip"]').tooltip();
		},
	});

	$("#ms_table_filter").hide();
	$("#cari_data").keyup(function () {
		otable.search($(this).val()).draw();
	});

	$("#delete_record").submit(function (e) {
		/*Form Submit*/

		e.preventDefault();
		var obj = $(this),
			action = obj.attr("name");
		$.ajax({
			type: "POST",
			url: e.target.action,
			data: obj.serialize() + "&is_ajax=2&form=" + action,
			cache: false,
			success: function (JSON) {
				if (JSON.error != "") {
					toastr.error(JSON.error);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					Ladda.stopAll();
				} else {
					$(".delete-modal").modal("toggle");
					otable.ajax.reload(function () {
						toastr.success(JSON.result);
					}, true);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
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
			<select class="form-control row_target_id" data-plugin="select_target_account" name="row_target_id[]" id="row_target_id_${rowCount}" value="0" data-placeholder="Select Account" required>
			</select>
		</td>
		<td>
			<input type="text" name="row_note[]" id="row_note" class="form-control row_note">
		</td>
		<td>
			<select class="form-control row_tax_id" id="row_tax_id_${rowCount}" data-plugin="select_tax" name="row_tax_id[]" onchange="select_tax(this)">
				<option value="">${ms_select_tax}</option>
			</select>
			<input type="text" class="row_tax_rate" name="row_tax_rate[]" id="row_tax_rate_${rowCount}" value="0">
			<input type="text" class="data_tax_rate" value="0">
			<input type="text" class="data_tax_type" value="fixed" name="data_tax_type[]"><br>
			<strong class="row_tax_rate_show currency" style="font-size:10px"></strong>
		</td>

		<td>
			<input type="number" min="0" class="row_amount form-control" name="row_amount[]" id="row_amount_${rowCount}" value="0">
		</td>
		<td style="text-align:center">
			<input type="text" class="row_type" name="row_type[]" value="INSERT">
			<button type="button" class="btn icon-btn btn-danger waves-effect waves-light remove-item"> <span class="fa fa-minus"></span></button>
		</td>
		</tr>`;

	// Add the row to the table body
	tbody.append(newRow);

	$('[data-plugin="select_target_account"]').select2({
		ajax: {
			delay: 250,
			url: site_url + "ajax_request/get_expenses_account",
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

function select_tax(x) {
	var selectedRow = $("#" + x.id).closest("tr");
	var query = x.value;
	var rowId = selectedRow.attr("data-id");
	$.get({
		url: site_url + "ajax_request/find_tax_by_id",
		data: { query: query },
		success: function (result) {
			data_tax_rate = result.rate;
			data_tax_type = result.type;
			var amount = parseFloat(selectedRow.find(".row_amount").val());

			if (data_tax_type == "fixed") {
				var tax = parseFloat(data_tax_rate);
			} else {
				var tax = (data_tax_rate / 100) * amount; // get nilai tax
			}

			selectedRow.find(".data_tax_rate").val(data_tax_rate);
			selectedRow.find(".data_tax_type").val(data_tax_type);

			selectedRow.find(".row_tax_rate").val(tax);
			selectedRow.find(".row_tax_rate_show").text(formatCurrency(tax));

			//update
			update_row_amount(rowId);
		},
	});
}

// Calculate subtotal whenever row_qty or row_item_price is changed
$(document).on(
	"change click keyup load",
	".row_tax_id, .row_amount",
	function () {
		var row = $(this).closest("tr");
		var id = row.attr("data-id");
		update_row_amount(id);
	}
);

function update_row_amount(id) {
	var row = $("#item-row-" + id).closest("tr");

	var data_tax_rate = parseFloat(row.find(".data_tax_rate").val());
	var data_tax_type = row.find(".data_tax_type").val();

	// inisialize the data
	var row_tax_rate = parseFloat(row.find(".row_tax_rate").val());

	var amount = parseFloat(row.find(".row_amount").val());

	// hitung tax
	if (data_tax_type == "fixed") {
		row_tax_rate = data_tax_rate;
	} else {
		row_tax_rate = (data_tax_rate / 100) * amount; // get nilai tax
	}

	row.find(".row_tax_rate").val(row_tax_rate);
	row.find(".row_tax_rate_show").text(formatCurrency(row_tax_rate));
}

$(document).ready(function () {
	$("#payment_form").submit(function (e) {
		e.preventDefault();
		var obj = $(this),
			action = obj.attr("name");
		var formData = new FormData(obj[0]);
		formData.append("form", action);
		jQuery(".save").prop("disabled", true);
		$(".icon-spinner3").show();

		$.ajax({
			type: "POST",
			enctype: "multipart/form-data",
			url: e.target.action,
			data: formData,
			cache: false,
			processData: false, // Important for FormData
			contentType: false, // Important for FormData
			success: function (JSON) {
				// jika ada error
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
							window.location.reload();
						},
					};
					toastr.success(JSON.result);

					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					$(".icon-spinner3").hide();
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

// edit data
$(window).on("load", function () {
	let type = $("input[name='expense']").val() ?? "";
	if (type == "UPDATE") {
		let token = $("input[name='_token']").val();
		$.ajax({
			type: "GET",
			url: site_url + "/finance/expenses/get_ajax_items_expense",
			data: "_token=" + token,
			dataType: "JSON",
			success: function (response) {
				console.table(response.items);
				if (response) {
					$.each(response.items, function (key, value) {
						addRow();
						var row = $("#item-row-" + key).closest("tr");
						var selectedOptionId = value.account_id;
						var selectedOptionText =
							value.account_code + " | " + value.account_name;

						var option = new Option(
							selectedOptionText,
							selectedOptionId,
							true,
							true
						);
						$("#row_target_id_" + key)
							.append(option)
							.trigger("change");

						if (value.tax_name != null) {
							$("#row_tax_id_" + key).append(
								new Option(value.tax_name, value.tax_id, true, true)
							);

							row.find(".row_tax_rate").val(value.tax_rate);
							row
								.find(".row_tax_rate_show")
								.text(formatCurrency(value.tax_rate));
						}
						row.find(".row_note").val(value.note);
						row.find(".row_amount").val(value.amount);
						row.find(".row_amount_show").text(formatCurrency(value.amount));
					});

					// // set another
					$("#ref_delivery_fee").val(response.data.ref_delivery_fee);
					$("#amount").val(response.data.amount);
					$("#amount_show").text(formatCurrency(response.data.amount));
					// update_total();
				}
			},
		});
	} else {
		addRow();
	}
});

// remove item
$(document).on("click", ".remove-item", function () {
	$(this)
		.closest(".item-row")
		.fadeOut(300, function () {
			$(this).remove();
		});
});
