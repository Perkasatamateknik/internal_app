$(document).ready(function () {
	// On page load:
	var xin_table_purchase_requisitions = $(
		"#xin_table_purchase_requisitions"
	).dataTable({
		bDestroy: true,
		iDisplayLength: 30,
		aLengthMenu: [
			[10, 30, 50, 100, -1],
			[10, 30, 50, 100, "All"],
		],
		ajax: {
			url: site_url + "purchase_requisitions/get_ajax_table/",
			type: "GET",
		},
		fnDrawCallback: function (settings) {
			$('[data-toggle="tooltip"]').tooltip();
		},
	});

	// update 9-5-2023
	$("#purchase_requisitions").submit(function (e) {
		/*Form Submit*/
		e.preventDefault();
		var obj = jQuery(this),
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
					jQuery(".save").prop("disabled", false);
					$(".icon-spinner3").hide();
					Ladda.stopAll();
				} else {
					toastr.options = {
						timeOut: 500,
						onHidden: function () {
							window.location.href =
								base_url + "/view/" + $("input[name='pr_number']").val();
						},
					};
					toastr.success(JSON.result);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					$(".icon-spinner3").hide();
					jQuery("#purchase_requisitions")[0].reset(); // To reset form fields
					jQuery(".save").prop("disabled", false);
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

	//role submit edit
	$("#purchase_requisitions_edit").submit(function (e) {
		/*Form Submit*/
		e.preventDefault();
		var obj = jQuery(this),
			action = obj.attr("name");
		jQuery(".save").prop("disabled", true);
		$(".icon-spinner3").show();
		jQuery.ajax({
			type: "POST",
			url: e.target.action,
			data: obj.serialize() + "&form=" + action,
			cache: false,
			success: function (JSON) {
				if (JSON.error != "") {
					toastr.error(JSON.error);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					jQuery(".save").prop("disabled", false);
					$(".icon-spinner3").hide();
					Ladda.stopAll();
				} else {
					toastr.options = {
						timeOut: 500,
						onHidden: function () {
							window.location.href =
								base_url + "/view/" + $("input[name='pr_number']").val();
						},
					};
					toastr.success(JSON.result);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					$(".icon-spinner3").hide();
					jQuery(".save").prop("disabled", false);
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

	/* reject data */
	$("#btnReject").on("click", function (e) {
		var id = $(this).data("id");
		var msg = $(this).data("msg");
		var conf = confirm(msg);
		toastr.options = {
			onHidden: function () {
				location.reload();
			},
			timeOut: 500,
		};
		if (conf == true) {
			$.ajax({
				type: "POST",
				url: base_url + "/reject",
				data: {
					csrf_hrsale: $('input[name="csrf_hrsale"]').val(),
					_token: id,
				},
				cache: false,
				success: function (JSON) {
					if (JSON.error != "") {
						toastr.error(JSON.error);
					} else {
						toastr.success(JSON.result);
					}
				},
				error: function (xhr, status, error) {
					toastr.error("Error: " + status + " | " + error);
				},
			});
		}
	});
	/* Delete data */
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
					xin_table_purchase_requisitions.api().ajax.reload(function () {
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

	// $("#edit_setting_datail").on("show.bs.modal", function (event) {
	$(".edit-modal-data").on("show.bs.modal", function (event) {
		var button = $(event.relatedTarget);
		var field_id = button.data("field_id");
		var field_type = button.data("field_type");

		$(".icon-spinner3").show();

		var modal = $(this);
		$.ajax({
			url: base_url + "/read_" + field_type,
			type: "GET",
			data: "jd=1&field_id=" + field_id,
			success: function (response) {
				if (response) {
					$(".icon-spinner3").hide();
					$("#ajax_modal").html(response);
				}
			},
		});
	});
});

$(document).on("click", ".delete", function () {
	$("input[name=_token]").val($(this).data("record-id"));
	$("#delete_record").attr("action", base_url + "/delete/");
});

function set_project(id) {
	$.ajax({
		url: site_url + "ajax_request/get_projects?completed=true", // Ubah 'get_data.php' sesuai dengan URL yang sesuai untuk memperoleh data dari PHP
		type: "GET",
		dataType: "json",
		success: function (data) {
			var res = "";
			// Melakukan perulangan untuk setiap data yang diperoleh
			$.each(data, function (key, val) {
				// Membuat opsi baru dengan menggunakan data
				res +=
					'<option value="' +
					parseFloat(val.id) +
					'">' +
					val.value +
					"</option>";
			});
			var i = "#project-" + id;
			$(i).append(res);
		},
		error: function (xhr, status, error) {
			console.log(error); // Menampilkan pesan kesalahan jika terjadi error
		},
	});
}

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

	// $(".currency").prepend(formatCurrency(value));

	$(".row_amount").each(function () {
		var sub_total = $(this).val();
		sub_total = parseFloat(sub_total);
		total = parseFloat(total) + parseFloat(sub_total);
	});

	var ref_delivery_fee = $("#ref_delivery_fee").val();
	total = parseFloat(total) + parseFloat(ref_delivery_fee);

	$("#amount").val(total);
	$("#amount_show").text(formatCurrency(total));
}

//call
update_total();

// var rowCounter = $("#item_product >tbody >tr").length;

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
			<select class="form-control row_ref_item" data-plugin="select_item" name="row_ref_item[]" id="row_ref_item_${rowCount}" onchange="select_product(this)" required>
				<option value="" selected disabled>${ms_select_item}</option>
			</select>
			<br><strong class="product_number" style="font-size:10px">No Selected</strong>
			<input type="hidden" name="row_item_name[]" class="row_item_name" value="">
			<input type="hidden" name="row_product_number[]" class="row_product_number" value="">

			<input type="hidden" name="row_sub_category_id[]" class="row_sub_category_id" value="">
			<input type="hidden" name="row_sub_category_name[]" class="row_sub_category_name" value="">
			<input type="hidden" name="row_category_id[]" class="row_category_id" value="">
			<input type="hidden" name="row_category_name[]" class="row_category_name" value="">
			<input type="hidden" name="row_uom_id[]" class="row_uom_id" value="">
			<input type="hidden" name="row_uom_name[]" class="row_uom_name" value="">
		</td>
		<td>
			<select class="form-control" data-plugin="select_project" name="row_project_id[]" id="row_project_id_${rowCount}">
				<option value="">${ms_select_project}</option>
			</select>
		</td>
		<td>
			<input type="number" class="form-control row_qty" name="row_qty[]" id="row_qty" min="1" value="1" required></td>
		<td><input type="number" class="form-control row_ref_price" name="row_ref_price[]" step="0.01" data-type="currency" id="row_ref_price" min="1" value="0" required></td>
		<td class="text-right align-middle"><input type="hidden" class="row_amount" name="row_amount[]" id="row_amount_${rowCount}" value="0"><strong class="row_amount_show currency">0</strong></td>
		<td style="text-align:center"><input type="hidden" class="row_item_id" name="row_item_id[]" value="0"><input type="hidden" class="row_type" name="row_type[]" value="INSERT">
		<button type="button" class="btn icon-btn btn-danger waves-effect waves-light remove-item" data-id="0" data-ajax="false"> <span class="fa fa-minus"></span></button></td>
		</tr>`;
	// Add the row to the table body
	tbody.append(newRow);

	// rowCount++;
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
				return `<button style="width: 100%" type="button"
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
		width: "100%",
	});

	var rowAmountSelect = $("#row_amount_" + rowCount);
	rowAmountSelect
		.closest("td")
		.find(".currency")
		.text(formatCurrency(rowAmountSelect.val()));

	update_total();
}

// Call the addRow function to add a new row to the table body

$(document).on("click", ".remove-item", function () {
	if ($(this).data("ajax")) {
		var conf = confirm("Are you sure you want to delete this item?");
		if (conf == true) {
			var id = $(this).data("id");
			var row = $(this).closest("tr");
			$.ajax({
				url: site_url + "ajax_request/delete_item_pr",
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

$(document).ready(function () {
	var span = '<span class="text-danger">*</span>';
	var required = $(".form-control[required]");
	required.closest(".form-group").find("label").append(span);
});

// Calculate subtotal whenever row_qty or row_ref_price is changed
$(document).on(
	"change keyup click",
	".row_qty, .row_ref_price, .ref_delivery_fee, .row_ref_item, .row_project_id",
	function () {
		var row = $(this).closest("tr");
		var qty = parseFloat(row.find(".row_qty").val());
		var refPrice = parseFloat(row.find(".row_ref_price").val());
		var subtotal = qty * refPrice;
		row.find(".row_amount").val(subtotal);
		row.find(".row_amount_show").text(formatCurrency(subtotal));

		update_total();
	}
);

function select_product(x) {
	var selectedRow = $("#" + x.id).closest("tr");
	var query = x.value;
	$.get({
		url: site_url + "ajax_request/find_product_by_id",
		data: { query: query },
		success: function (result) {
			selectedRow.find(".product_number").text(result.product_number);
			selectedRow.find(".row_product_number").val(result.product_number);
			selectedRow.find(".row_item_name").val(result.product_name);
			selectedRow.find(".row_ref_price").val(parseFloat(result.price));

			selectedRow.find(".row_sub_category_id").val(result.sub_category_id);
			selectedRow.find(".row_sub_category_name").val(result.sub_category_name);
			selectedRow.find(".row_category_id").val(result.category_id);
			selectedRow.find(".row_category_name").val(result.category_name);
			selectedRow.find(".row_uom_id").val(result.uom_id);
			selectedRow.find(".row_uom_name").val(result.uom_name);

			//update row amount
			var qty = parseFloat(selectedRow.find(".row_qty").val());
			var refPrice = parseFloat(selectedRow.find(".row_ref_price").val());
			var subtotal = qty * refPrice;
			selectedRow.find(".row_amount").val(subtotal);
			selectedRow.find(".row_amount_show").text(formatCurrency(subtotal));

			update_total();
		},
	});
	// if ($.isNumeric(query)) {
	// } else {
	// 	selectedRow.find(".product_number").text("add new: " + query); // bug first click tags new not called
	// 	selectedRow.find(".row_ref_price").val(0);
	// 	selectedRow.find(".row_item_name").val(query); // replace existing
	// 	selectedRow.find(".row_amount").val(0);
	// 	selectedRow.find(".row_amount_show").text(formatCurrency(0));
	// 	update_total();
	// }
}

// edit data
$(window).on("load", function () {
	let type = $("input[name='purchase_requisitions']").val() ?? "";
	if (type == "UPDATE") {
		let pr_number = $("input[name='pr_number']").val();
		$.ajax({
			type: "GET",
			url: base_url + "/get_ajax_pr",
			data: "pr_number=" + pr_number,
			dataType: "JSON",
			success: function (response) {
				if (response) {
					$.each(response.items, function (key, value) {
						addRow();
						var row = $("#item-row-" + key).closest("tr");

						var selectedOptionId = value.ref_item;
						var selectedOptionText = value.item_name;

						var option = new Option(
							selectedOptionText,
							selectedOptionId,
							true,
							true
						);
						$("#row_ref_item_" + key).append(option);
						row.find(".row_item_name").val(value.item_name);

						row.find(".product_number").text(value.product_number); // view product number
						row.find(".row_product_number").val(value.product_number); // view product number

						row.find(".row_sub_category_id").val(value.sub_category_id);
						row.find(".row_sub_category_name").val(value.sub_category_name);
						row.find(".row_category_id").val(value.category_id);
						row.find(".row_category_name").val(value.category_name);
						row.find(".row_uom_id").val(value.uom_id);
						row.find(".row_uom_name").val(value.uom_name);

						row.find(".row_qty").val(value.quantity);
						row.find(".row_ref_price").val(value.ref_price);
						row.find(".row_amount").val(value.amount);
						row.find(".row_item_id").val(value.item_pr_id);

						row.find(".row_amount_show").text(formatCurrency(value.amount));

						// set tombol delete to ajax
						row.find(".remove-item").attr("data-ajax", "true"); // set button remove to ajax delete item
						row.find(".remove-item").attr("data-id", value.item_pr_id); // set the id of item
						row.find(".fa").removeClass("fa-minus"); // remove class btn danger
						row.find(".fa").addClass("fa-trash"); // remove class btn danger

						row.find(".row_type").val("UPDATE"); // set the type of item (UPDATE)

						// set selected project
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
					});

					// set another
					$("#ref_delivery_fee").val(response.data.ref_delivery_fee);
					$("#amount").val(response.data.amount);
					$("#amount_show").text(formatCurrency(response.data.amount));
					update_total();
				}
			},
		});
	} else {
		addRow();
	}
});
