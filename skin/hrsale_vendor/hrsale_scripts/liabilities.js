$(function () {
	// submit insert or edit
	$("#liabilities_form").submit(function (e) {
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
								"contacts/liability_view/" +
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
});

$(document).ready(function () {
	var span = '<span class="text-danger">*</span>';
	var required = $(".form-control[required]");
	required.closest(".form-group").find("label").append(span);
});

$(document).ready(function () {
	// On page load:
	var otable = $("#ms_table").dataTable({
		bDestroy: true,
		ajax: {
			url: site_url + "contacts/get_ajax_liabilities/",
			type: "GET",
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
					otable.api().ajax.reload(function () {
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
			<input type="text" name="row_note[]" id="row_note" class="form-control row_note" value="">
		</td>
		<td>
			<input type="number" min="0" class="row_amount form-control" name="row_amount[]" id="row_amount_${rowCount}" value="0">
		</td>
		<td style="text-align:center">
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

	var rowAmountSelect = $("#row_amount_" + rowCount);
	rowAmountSelect
		.closest("td")
		.find(".currency")
		.text(formatCurrency(rowAmountSelect.val()));

	// update_total();
}

// Calculate subtotal whenever row_qty or row_item_price is changed
$(document).on("change click keyup load", ".row_amount", function () {
	update_total();
});

function update_total() {
	var total = 0;
	$(".row_amount").each(function () {
		var sub_total = $(this).val();
		total += parseFloat(sub_total);
	});

	$("#amount").val(total);
	$("#amount_show").text(formatCurrency(total));
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

// delete all
$(document).on("click", ".delete", function () {
	$("input[name=_token]").val($(this).data("record-id"));
	$("#data-message").addClass("pt-3 font-weight-bold");

	let del_file = $(this).data("record-id");
	$("#data-message").html(del_file);

	$("#delete_record").attr("action", site_url + "contacts/liabilities_delete/");
});

// edit data
$(window).on("load", function () {
	let type = $("input[name='type']").val() ?? "";
	if (type == "UPDATE") {
		let token = $("input[name='_token']").val();
		$.ajax({
			type: "GET",
			url: site_url + "/contacts/get_ajax_items_liability",
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

						row.find(".row_note").val(value.note);
						row.find(".row_amount").val(value.amount);
					});

					update_total();
				}
			},
		});
	} else {
		addRow();
	}
});

// remove item
$(document).on("click", ".remove-item", function () {
	if ($(this).data("ajax")) {
		var conf = confirm("Are you sure you want to delete this item?");
		if (conf == true) {
			var id = $(this).data("id");
			var row = $(this).closest("tr");
			console.log($('input[name="csrf_hrsale"]').val());

			$.ajax({
				url: site_url + "ajax_request/delete_item_liabilities",
				type: "POST",
				data: {
					csrf_hrsale: $('input[name="csrf_hrsale"]').val(),
					id: id,
				},
				success: function (JSON) {
					// jika ada error
					if (JSON.error != "") {
						toastr.error(JSON.error);
						$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					} else {
						row.fadeOut(300, function () {
							$(this).remove();
							update_total();
						});
						toastr.success(JSON.result);
						$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					toastr.error("Error: " + textStatus + " | " + jqXHR);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
				},
			});
		}
	} else {
		$(this)
			.closest(".item-row")
			.fadeOut(300, function () {
				$(this).remove();
				update_total();
			});
	}
});

$(document).ready(function () {
	$('[data-plugin="select_accounts"]').select2({
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
});

function ajaxItem(id) {
	$.ajax({
		type: "GET",
		url: base_url + "/ajax_modal_liability_item",
		data: {
			_token: id,
		},
		dataType: "json",
		success: function (response) {
			$("#modal-view").html(response.data);
			$("#modal-result").modal({
				backdrop: "static",
				keyboard: false,
			});

			$("#modal-result").modal("show");
		},
	});
}
