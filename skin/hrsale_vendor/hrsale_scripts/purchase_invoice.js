$(document).ready(function () {
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

	$('[data-plugin="select_hrm"]').select2({ width: "100%" });

	var xin_table_purchase_invoices = $("#xin_table_purchase_invoices").dataTable(
		{
			bDestroy: true,
			iDisplayLength: 30,
			aLengthMenu: [
				[10, 30, 50, 100, -1],
				[10, 30, 50, 100, "All"],
			],
			ajax: {
				url: site_url + "purchase_invoices/get_ajax_table/",
				type: "GET",
			},
			fnDrawCallback: function (settings) {
				$('[data-toggle="tooltip"]').tooltip();
			},
		}
	);

	// submit insert or edit
	$("#purchase_invoices").submit(function (e) {
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
								base_url + "/view/" + $("input[name='pi_number']").val();
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

	$("#payment_form").submit(function (e) {
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
							window.location.href = "";
						},
					};
					toastr.success(JSON.result);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					$(".icon-spinner3").hide();
					$("#payment_form")[0].reset(); // To reset form fields
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

	// ajax delete
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
					xin_table_purchase_invoices.api().ajax.reload(function () {
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

$(document).on("click", ".delete", function () {
	$("input[name=_token]").val($(this).data("record-id"));
	$("#delete_record").attr("action", base_url + "/delete/");
});

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

// =====================================================

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

function update_total() {
	var total = 0;
	$(".row_amount").each(function () {
		var sub_total = $(this).val();
		sub_total = parseFloat(sub_total);
		total = parseFloat(total) + parseFloat(sub_total);
	});

	var delivery_fee = $("#delivery_fee").val();
	var service_fee = $("#service_fee").val();
	total =
		parseFloat(total) + parseFloat(delivery_fee) + parseFloat(service_fee);

	$("#amount").val(total);
	$("#amount_show").text(formatCurrency(total));
}

// Fungsi edit otomatic kaluasi saat load
$(document).on("load", function () {
	update_total();
});

// Calculate subtotal whenever row_qty or row_item_price is changed
$(document).on(
	"change click keyup load",
	".row_qty, .row_item_price, .delivery_fee, .service_fee, .row_tax_price, .row_discount_price, [data-plugin='select_item'], .row_discount_rate_show, .row_discount_tax_show",
	function () {
		var row = $(this).closest("tr");
		var id = row.attr("data-id");
		update_row_amount(id);
		update_total();
	}
);

function update_row_amount(id) {
	var row = $("#item-row-" + id).closest("tr");

	// master data each row
	var data_discount_rate = parseFloat(row.find(".data_discount_rate").val());
	var data_discount_type = row.find(".data_discount_type").val();

	var data_tax_rate = parseFloat(row.find(".data_tax_rate").val());
	var data_tax_type = row.find(".data_tax_type").val();

	// data each row
	var row_qty = parseFloat(row.find(".row_qty").val());
	var row_item_price = parseFloat(row.find(".row_item_price").val());

	// inisialize the data
	var row_tax_rate = parseFloat(row.find(".row_tax_rate").val());
	var row_discount_rate = parseFloat(row.find(".row_discount_rate").val());

	var subtotal_1 = row_qty * row_item_price; // cari harga x jumlah

	// kalkulasi diskon
	if (data_discount_type == 0) {
		row_discount_rate = data_discount_rate;
	} else {
		row_discount_rate = (data_discount_rate / 100) * subtotal_1; // get nilai diskon
	}

	row.find(".row_discount_rate").val(row_discount_rate);
	row.find(".row_discount_rate_show").text(formatCurrency(row_discount_rate));

	// hitung tax
	if (data_tax_type == "fixed") {
		row_tax_rate = data_tax_rate;
	} else {
		row_tax_rate = (data_tax_rate / 100) * (subtotal_1 - row_discount_rate); // get nilai tax
	}

	row.find(".row_tax_rate").val(row_tax_rate);
	row.find(".row_tax_rate_show").text(formatCurrency(row_tax_rate));

	subtotal_1 = subtotal_1 - row_discount_rate + row_tax_rate;

	row.find(".row_amount").val(subtotal_1);
	row.find(".row_amount_show").text(formatCurrency(subtotal_1));
}

/* ----------------------- END CALCULATE ITEMS ----------------------------- */

/* ----------------------- ADD ITEMS ----------------------------- */

// =========================================

$(document).ready(function () {
	var span = '<span class="text-danger">*</span>';
	var required = $(".form-control[required]");
	required.closest(".form-group").find("label").append(span);
});

$(function () {
	$('[data-plugin="select_contact"]').select2({
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

	// var selected_contact = $("[name='selected_contact']").val();
	// if (selected_contact != null) {
	// 	// set selected vendor
	// 	$.get({
	// 		url: site_url + "ajax_request/find_contact_by_id",
	// 		data: "query=" + selected_contact,
	// 		dataType: "JSON",
	// 		success: function (res) {
	// 			var selectedOptionId = res.contact_id;
	// 			var selectedOptionText = res.contact_name;

	// 			var option = new Option(
	// 				selectedOptionText,
	// 				selectedOptionId,
	// 				true,
	// 				true
	// 			);
	// 			$("[name='contact_id']").append(option).trigger("change");
	// 		},
	// 		error: function (xhr, status, error) {
	// 			toastr.error("Error: " + status + " | " + error);
	// 			$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
	// 			$(".icon-spinner3").hide();
	// 			$(".save").prop("disabled", false);
	// 			Ladda.stopAll();
	// 		},
	// 	});
	// }
});

/* ----------------------- ADD ITEMS ----------------------------- */

// Function to add a new row to the table body
function addRow() {
	// Get the table body element
	var tbody = $("#item_product > tbody");

	// Get the number of rows in the table body
	var rowCount = tbody.children().length;

	var rowId = "row-" + rowCount;
	// Create a new row element with a unique id attribute
	var newRow = `<tr class="item-row" id="item-row-${rowCount}" data-id="${rowCount}">
		<td>
			<select class="form-control row_item_id" data-plugin="select_item" name="row_item_id[]" id="row_item_id_${rowCount}" onchange="select_product(this)" value="0" required>
				<option value="" selected disabled>${ms_select_item}</option>
			</select>
			<br><strong class="product_number" style="font-size:10px">No Selected</strong>
			<input type="hidden" name="row_item_name[]" class="row_item_name" value="" required>

			<input type="hidden" name="row_product_number[]" class="row_product_number" value="">
			<input type="hidden" name="row_sub_category_id[]" class="row_sub_category_id" value="">
			<input type="hidden" name="row_sub_category_name[]" class="row_sub_category_name" value="">
			<input type="hidden" name="row_category_id[]" class="row_category_id" value="">
			<input type="hidden" name="row_category_name[]" class="row_category_name" value="">
			<input type="hidden" name="row_uom_id[]" class="row_uom_id" value="">
			<input type="hidden" name="row_uom_name[]" class="row_uom_name" value="">
		</td>
		<td>
			<select class="form-control row_project_id" data-plugin="select_project" name="row_project_id[]" id="row_project_id_${rowCount}">
				<option value="">${ms_select_project}</option>
			</select>
		</td>
		<td>
			<select class="form-control row_tax_id" id="row_tax_id_${rowCount}" data-plugin="select_tax" name="row_tax_id[]" onchange="select_tax(this)">
				<option value="">${ms_select_tax}</option>
			</select>
			<input type="hidden" class="row_tax_rate" name="row_tax_rate[]" id="row_tax_rate_${rowCount}" value="0">
			<input type="hidden" class="data_tax_rate" value="0">
			<input type="hidden" class="data_tax_type" value="fixed" name="data_tax_type[]"><br>
			<strong class="row_tax_rate_show currency" style="font-size:10px"></strong>
		</td>

		<td>
			<select class="form-control row_discount_id" id="row_discount_id_${rowCount}" data-plugin="select_discount" name="row_discount_id[]" onchange="select_discount(this)">
				<option value="">${ms_select_discount}</option>
			</select>
			<input type="hidden" class="row_discount_rate" name="row_discount_rate[]" id="row_discount_rate_${rowCount}" value="0">
			<input type="hidden" class="data_discount_type" value="0" name="data_discount_type[]">
			<input type="hidden" class="data_discount_rate" value="0"><br>
			<strong class='row_discount_rate_show currency' style='font-size:10px'></strong>
		</td>

		<td><input type="number" class="form-control row_qty" name="row_qty[]" id="row_qty" min="1" value="1" required></td>
		<td><input type="number" class="form-control row_item_price" name="row_item_price[]" step="0.01" data-type="currency" id="row_item_price" min="1" value="0" required></td>
		<td class="text-right align-middle">
			<input type="hidden" class="row_amount" name="row_amount[]" id="row_amount_${rowCount}" value="0">
			<strong class="row_amount_show currency">0</strong>
		</td>
		<td style="text-align:center">
			<input type="hidden" class="row_item_pi_id" name="row_item_pi_id[]" value="0">
			<input type="hidden" class="row_type" name="row_type[]" value="INSERT">
			<button type="button" class="btn icon-btn btn-danger waves-effect waves-light remove-item"> <span class="fa fa-minus"></span></button>
		</td>
		</tr>`;

	// Add the row to the table body
	tbody.append(newRow);

	$('[data-plugin="select_item"]').select2({
		ajax: {
			delay: 250,
			url: site_url + "ajax_request/find_product",
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
		language: {
			noResults: function () {
				return `<li><button style="width: 100%" type="button"
            class="btn btn-tranparent btn-sm" 
            onClick='addItemProduct()'><span class="ion ion-md-add"></span> Add Product</button>
            </li>`;
			},
		},

		escapeMarkup: function (markup) {
			return markup;
		},
	});

	$('[data-plugin="select_project"]').select2({
		ajax: {
			delay: 250,
			url: site_url + "ajax_request/find_project",
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
		width: "200px",
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

	$('[data-plugin="select_discount"]').select2({
		ajax: {
			delay: 250,
			url: site_url + "ajax_request/find_discount",
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

	update_total();
}

// remove item
$(document).on("click", ".remove-item", function () {
	if ($(this).data("ajax")) {
		var conf = confirm("Are you sure you want to delete this item?");
		if (conf == true) {
			var id = $(this).data("id");
			var row = $(this).closest("tr");
			$.ajax({
				url: site_url + "ajax_request/delete_item_pi",
				type: "POST",
				data: {
					csrf_hrsale: $('input[name="csrf_hrsale"]').val(),
					id: id,
				},
				success: function (JSON) {
					row.fadeOut(300, function () {
						$(this).remove();
						update_total();
					});
					toastr.success(JSON.result);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
				},
				error: function (jqXHR, textStatus, errorThrown) {
					toastr.error("Error: " + textStatus + " | " + error);
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

// buat label berbintang required
// show data for view from PO or PD
$(window).on("load", function () {
	if ($("#po_number").val() != 0) {
		$("#add_role_form").addClass("show");
		$.ajax({
			type: "GET",
			url: base_url + "/get_ajax_po",
			data: "po_number=" + $("#po_number").val(),
			dataType: "JSON",
			success: function (response) {
				if (response) {
					$.each(response.items, function (key, value) {
						addRow();

						var row = $("#item-row-" + key).closest("tr");
						row.find(".row_qty").val(value.quantity);
						row.find(".row_item_price").val(value.price);
						row.find(".row_amount").val(value.amount);

						row.find(".row_amount_show").text(formatCurrency(value.amount));
						$.get({
							url: site_url + "ajax_request/find_product_by_id",
							data: "query=" + value.product_id,
							dataType: "JSON",
							success: function (res) {
								if (res != null) {
									var selectedOptionId = res.product_id;
									var selectedOptionText = res.product_name;

									var option = new Option(
										selectedOptionText,
										selectedOptionId,
										true,
										true
									);
									$("#row_item_id_" + key).append(option);

									row.find(".row_sub_category_id").val(res.sub_category_id);
									row.find(".row_sub_category_name").val(res.sub_category_name);
									row.find(".row_category_id").val(res.category_id);
									row.find(".row_category_name").val(res.category_name);
									row.find(".row_uom_id").val(res.uom_id);
									row.find(".row_uom_name").val(res.uom_name);
									row.find(".row_product_number").val(res.product_number);
									row.find(".product_number").html(res.product_number);
									row.find(".row_item_name").val(res.product_name);
								} else {
									row.find(".row_product_number").val(value.product_number);
									row.find(".product_number").html(value.product_number);
									row.find(".row_item_name").val(value.product_name);
								}
							},
						});

						// set selected project
						if (value.project_id != 0) {
							$.get({
								url: site_url + "ajax_request/find_project_by_id",
								data: "query=" + value.project_id,
								dataType: "JSON",
								success: function (res) {
									if (res != null) {
										var selectedOptionId = res.project_id;
										var selectedOptionText = res.title;

										var option = new Option(
											selectedOptionText,
											selectedOptionId,
											true,
											true
										);
										$("#row_project_id_" + key)
											.append(option)
											.trigger("change");
									}
								},
							});
						}

						if (value.tax_id != 0) {
							$.get({
								url: site_url + "ajax_request/find_tax_by_id",
								data: "query=" + value.tax_id,
								dataType: "JSON",
								success: function (res) {
									if (res != null) {
										var selectedOptionId = res.tax_id;
										var selectedOptionText = res.name;

										var option = new Option(
											selectedOptionText,
											selectedOptionId,
											true,
											true
										);
										$("#row_tax_id_" + key)
											.append(option)
											.trigger("change");
									}
								},
							});
						}

						if (value.discount_id != 0) {
							$.get({
								url: site_url + "ajax_request/find_discount_by_id",
								data: "query=" + value.discount_id,
								dataType: "JSON",
								success: function (res) {
									if (res != null) {
										var selectedOptionId = res.discount_id;
										var selectedOptionText = res.discount_name;

										var option = new Option(
											selectedOptionText,
											selectedOptionId,
											true,
											true
										);
										$("#row_discount_id_" + key)
											.append(option)
											.trigger("change");
									}
								},
							});
						}
					});

					// set another
					$("#delivery_fee").val(response.data.delivery_fee);
					$("#amount").val(response.data.amount);
					$("#amount_show").text(formatCurrency(response.data.amount));
				}
			},
		});
	} else {
		// Call the addRow function to add a new row to the table body
		addRow();
	}
});
/* ----------------------- END ADD ITEMS ----------------------------- */

$(function () {
	$('[data-plugin="select_vendor"]').select2({
		ajax: {
			delay: 250,
			url: site_url + "ajax_request/find_vendor",
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
		language: {
			noResults: function () {
				return `</li><button style="width: 100%" type="button"
        class="btn btn-transparent" 
        onclick='addVendor()'><span class="ion ion-md-add"></span> Add Vendor</button>
        </li>`;
			},
		},

		escapeMarkup: function (markup) {
			return markup;
		},
	});
});

function select_product(x) {
	var selectedRow = $("#" + x.id).closest("tr");
	var query = x.value;
	$.get({
		url: site_url + "ajax_request/find_product_by_id",
		data: { query: query },
		success: function (result) {
			if (result != null) {
				selectedRow.find(".product_number").text(result.product_number);
				selectedRow.find(".row_item_name").val(result.product_name);
				selectedRow.find(".row_item_price").val(parseFloat(result.price));

				selectedRow.find(".row_sub_category_id").val(result.sub_category_id);
				selectedRow
					.find(".row_sub_category_name")
					.val(result.sub_category_name);
				selectedRow.find(".row_category_id").val(result.category_id);
				selectedRow.find(".row_category_name").val(result.category_name);
				selectedRow.find(".row_uom_id").val(result.uom_id);
				selectedRow.find(".row_uom_name").val(result.uom_name);
				selectedRow.find(".row_product_number").val(result.product_number);
				selectedRow.find(".product_number").html(result.product_number);

				//update row amount
				var qty = parseFloat(selectedRow.find(".row_qty").val());
				var refPrice = parseFloat(selectedRow.find(".row_item_price").val());
				var subtotal = qty * refPrice;
				selectedRow.find(".row_amount").val(subtotal);
				selectedRow.find(".row_amount_show").text(formatCurrency(subtotal));
				update_total();
			}
		},
	});
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

			// var row_amount = parseFloat(selectedRow.find(".row_amount").val());
			var discount = parseFloat(selectedRow.find(".row_discount_rate").val());
			var item_qty = parseFloat(selectedRow.find(".row_qty").val());
			var item_price = parseFloat(selectedRow.find(".row_item_price").val());

			// // tipe 0 adalah flat
			var proses_1 = item_price * item_qty;
			var proses_2 = proses_1 - discount;

			if (data_tax_type == "fixed") {
				var tax = parseFloat(data_tax_rate);
			} else {
				var tax = (data_tax_rate / 100) * proses_2; // get nilai tax
			}

			selectedRow.find(".data_tax_rate").val(data_tax_rate);
			selectedRow.find(".data_tax_type").val(data_tax_type);

			selectedRow.find(".row_tax_rate").val(tax);
			selectedRow.find(".row_tax_rate_show").text(formatCurrency(tax));

			//update
			update_row_amount(rowId);
			update_total();
		},
	});
}

function select_discount(x) {
	var selectedRow = $("#" + x.id).closest("tr");
	var query = x.value;
	var rowId = selectedRow.attr("data-id");

	$.get({
		url: site_url + "ajax_request/find_discount_by_id",
		data: { query: query },
		success: function (result) {
			var discount_rate = result.discount_value;
			var discount_type = result.discount_type;

			var item_price = selectedRow.find(".row_item_price").val();
			var item_qty = selectedRow.find(".row_qty").val();

			// tipe 0 adalah flat
			if (discount_type == 0) {
				var discount = parseFloat(discount_rate);
			} else {
				var total_item = parseFloat(item_price) * parseFloat(item_qty);
				var discount = (discount_rate / 100) * total_item; // get nilai diskon
				// var discount = total_item - parseFloat(proses_1);
			}

			selectedRow.find(".data_discount_rate").val(discount_rate);
			selectedRow.find(".data_discount_type").val(discount_type);

			selectedRow.find(".row_discount_rate").val(discount);
			selectedRow
				.find(".row_discount_rate_show")
				.text(formatCurrency(discount));

			//update
			update_row_amount(rowId);
			update_total();
		},
	});
}

// function to edit show
$(window).on("load", function () {
	let type = $("input[name='purchase_invoices']").val() ?? "";
	if (type == "UPDATE") {
		let pi_number = $("input[name='pi_number']").val();
		$.ajax({
			type: "GET",
			url: base_url + "/get_ajax_pi",
			data: "pi_number=" + pi_number,
			dataType: "JSON",
			success: function (response) {
				if (response) {
					$.each(response.items, function (key, value) {
						addRow();

						var row = $("#item-row-" + key).closest("tr");
						row.find(".row_qty").val(value.quantity);
						row.find(".row_item_price").val(value.price);
						row.find(".row_amount").val(value.amount);

						row.find(".row_amount_show").text(formatCurrency(value.amount));

						$.get({
							url: site_url + "ajax_request/find_product_by_id",
							data: "query=" + value.product_id,
							dataType: "JSON",
							success: function (res) {
								if (res != null) {
									var selectedOptionId = res.product_id;
									var selectedOptionText = res.product_name;

									var option = new Option(
										selectedOptionText,
										selectedOptionId,
										true,
										true
									);
									$("#row_item_id_" + key).append(option);

									row.find(".row_sub_category_id").val(res.sub_category_id);
									row.find(".row_sub_category_name").val(res.sub_category_name);
									row.find(".row_category_id").val(res.category_id);
									row.find(".row_category_name").val(res.category_name);
									row.find(".row_uom_id").val(res.uom_id);
									row.find(".row_uom_name").val(res.uom_name);
									row.find(".row_product_number").val(res.product_number);
									row.find(".product_number").html(res.product_number);
									row.find(".row_item_name").val(res.product_name);
								} else {
									var option = new Option(
										value.product_name,
										value.product_id,
										true,
										true
									);
									$("#row_item_id_" + key).append(option);
									row.find(".product_number").html(value.product_number);
									row.find(".row_item_name").val(value.product_name);
									row.find(".row_product_number").val(value.product_number);
								}
							},
						});

						if (value.tax_id != 0) {
							$.get({
								url: site_url + "ajax_request/find_tax_by_id",
								data: "query=" + value.tax_id,
								dataType: "JSON",
								success: function (res) {
									if (res != null) {
										var selectedOptionId = res.tax_id;
										var selectedOptionText = res.name;

										var option = new Option(
											selectedOptionText,
											selectedOptionId,
											true,
											true
										);
										$("#row_tax_id_" + key)
											.append(option)
											.trigger("change");
									}
								},
							});
						}

						if (value.discount_id != 0) {
							$.get({
								url: site_url + "ajax_request/find_discount_by_id",
								data: "query=" + value.discount_id,
								dataType: "JSON",
								success: function (res) {
									if (res != null) {
										var selectedOptionId = res.discount_id;
										var selectedOptionText = res.discount_name;

										var option = new Option(
											selectedOptionText,
											selectedOptionId,
											true,
											true
										);
										$("#row_discount_id_" + key)
											.append(option)
											.trigger("change");
									}
								},
							});
						}

						// set selected project
						if (value.project_id != 0) {
							$.get({
								url: site_url + "ajax_request/find_project_by_id",
								data: "query=" + value.project_id,
								dataType: "JSON",
								success: function (res) {
									if (res != null) {
										var selectedOptionId = res.project_id;
										var selectedOptionText = res.title;

										var option = new Option(
											selectedOptionText,
											selectedOptionId,
											true,
											true
										);
										$("#row_project_id_" + key)
											.append(option)
											.trigger("change");
									}
								},
							});
						}

						// set tombol delete to ajax
						row.find(".remove-item").attr("data-ajax", "true"); // set button remove to ajax delete item
						row.find(".remove-item").attr("data-id", value.item_pi_id); // set the id of item
						row.find(".fa").removeClass("fa-minus"); // remove class btn danger
						row.find(".fa").addClass("fa-trash"); // remove class btn danger

						row.find(".row_type").val("UPDATE"); // set the type of item (UPDATE)

						//set id each item
						row.find(".row_item_pi_id").val(value.item_pi_id);
					});

					// set another
					$("#pi_number_show").val(response.data.pi_number);
					$("#faktur_number").val(response.data.faktur_number);
					$("#warehouse_assign").val(response.data.warehouse_assign);
					$("#date").val(response.data.date);
					$("#due_date").val(response.data.due_date);
					$("#delivery_date").val(response.data.delivery_date);
					$("#delivery_name").val(response.data.delivery_name);
					$("#delivery_number").val(response.data.delivery_number);
					$("#delivery_fee").val(response.data.delivery_fee);
					$("#service_fee").val(response.data.service_fee);
					$("#reference").val(response.data.reference);
					$("#notes").val(response.data.notes);

					$("#amount").val(response.data.amount);
					$("#amount_show").text(formatCurrency(response.data.amount));
					update_total();
				}

				// set vendor
				$.get({
					url: site_url + "ajax_request/find_contact_by_id",
					data: "query=" + response.data.contact_id,
					dataType: "JSON",
					success: function (res) {
						if (res != null) {
							var selectedOptionId = res.contact_id;
							var selectedOptionText = res.contact_name;

							var option = new Option(
								selectedOptionText,
								selectedOptionId,
								true,
								true
							);
							$("[name='contact_id']").append(option).trigger("change");
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
			},
		});
	}
});
