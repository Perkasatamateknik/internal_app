$(document).ready(function () {
	$('[data-plugin="select_hrm"]').select2($(this).attr("data-options"));
	$('[data-plugin="select_hrm"]').select2({ width: "100%" });
	// listing
	// On page load:

	// update 9-5-2023
	// $("#xin_table_php").dataTable({
	// 	bDestroy: true,
	// 	iDisplayLength: 10,
	// 	aLengthMenu: [
	// 		[5, 10, 30, 50, 100, -1],
	// 		[5, 10, 30, 50, 100, "All"],
	// 	],
	// });
	var ms_table_project_costs = $("#xin_table_project_costs").dataTable({
		bDestroy: true,
		iDisplayLength: 100,
		aLengthMenu: [
			[5, 10, 30, 50, 100, -1],
			[5, 10, 30, 50, 100, "All"],
		],
		ajax: {
			url: site_url + "project_costs/get_ajax_table_transactions",
			type: "GET",
		},
		fnDrawCallback: function (settings) {
			$('[data-toggle="tooltip"]').tooltip();
		},
	});

	var ms_detail_trans = $("#xin_table_project_cost_detail").dataTable({
		bDestroy: true,
		bFilter: false,
		bLengthChange: false,
		iDisplayLength: 5,
		aLengthMenu: [
			[5, 10, 30, 50, 100, -1],
			[5, 10, 30, 50, 100, "All"],
		],
		ajax: {
			url:
				site_url +
				"project_costs/get_ajax_table_transaction_detail/" +
				$("#project_cost_id").val(),
			type: "GET",
		},
		fnDrawCallback: function (settings) {
			$('[data-toggle="tooltip"]').tooltip();
		},
	});

	// update 9-5-2023
	jQuery("#transactions").submit(function (e) {
		/*Form Submit*/
		e.preventDefault();
		var obj = jQuery(this),
			action = obj.attr("name");
		jQuery(".save").prop("disabled", true);
		$(".icon-spinner3").show();
		jQuery.ajax({
			type: "POST",
			url: e.target.action,
			data:
				obj.serialize() +
				"&is_ajax=471&data=transaction&type=transaction&form=" +
				action,
			cache: false,
			success: function (JSON) {
				if (JSON.error != "") {
					toastr.error(JSON.error);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					jQuery(".save").prop("disabled", false);
					$(".icon-spinner3").hide();
					Ladda.stopAll();
				} else {
					ms_detail_trans.api().ajax.reload(function () {
						toastr.success(JSON.result);
					}, true);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					$(".icon-spinner3").hide();
					jQuery("#transactions")[0].reset(); // To reset form fields
					$("#item_product tbody").empty(); // To reset form fields table
					$("#add_role_form").removeClass("show"); // To reset form fields table

					jQuery(".save").prop("disabled", false);
					Ladda.stopAll();
				}
			},
			error: function (xhr, status, error) {
				// Handle AJAX error
				toastr.error("Error: " + error);
				jQuery(".save").prop("disabled", false);
				$(".icon-spinner3").hide();
				Ladda.stopAll();
			},
		});
	});

	$("#delete_record").submit(function (e) {
		/*Form Submit*/
		e.preventDefault();
		var obj = $(this),
			action = obj.attr("name");
		$.ajax({
			type: "POST",
			url: e.target.action,
			data: obj.serialize() + "&is_ajax=2&type=delete&form=" + action,
			cache: false,
			success: function (JSON) {
				if (JSON.error != "") {
					toastr.error(JSON.error);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					Ladda.stopAll();
				} else {
					$(".delete-modal").modal("toggle");
					ms_table_project_costs.api().ajax.reload(function () {
						toastr.success(JSON.result);
					}, true);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					Ladda.stopAll();
				}
			},
		});
	});
});

$(document).on("click", ".delete", function () {
	$("input[name=_token]").val($(this).data("record-id"));
	$("#delete_record").attr(
		"action",
		site_url + "project_costs/delete_transaction/"
	);
});

$(".edit-modal-data").on("show.bs.modal", function (event) {
	var button = $(event.relatedTarget);
	var vendor_id = button.data("vendor_id");
	var modal = $(this);
	$.ajax({
		url: base_url + "/edit_transaction/",
		type: "GET",
		data: "jd=1&is_ajax=1&mode=modal&data=vendors&vendor_id=" + vendor_id,
		success: function (response) {
			if (response) {
				$("#ajax_modal").html(response);
			}
		},
	});
});

// /
// /
// 	/
// 	/
// 	/
// 	/
// 	//

function set_tax(id) {
	$.ajax({
		url: site_url + "ajax_request/get_taxs", // Ubah 'get_data.php' sesuai dengan URL yang sesuai untuk memperoleh data dari PHP
		type: "GET",
		dataType: "json",
		success: function (data) {
			var res = "";
			// Melakukan perulangan untuk setiap data yang diperoleh
			$.each(data, function (key, value) {
				// Membuat opsi baru dengan menggunakan data
				res +=
					'<option value="' +
					parseInt(value.tax_id) +
					'" tax-type="' +
					value.type +
					'" tax-rate="' +
					parseInt(value.rate) +
					'">' +
					value.name +
					"</option>";
			});
			var i = "#tax-" + id;
			$(i).append(res);
		},
		error: function (xhr, status, error) {
			console.log(error); // Menampilkan pesan kesalahan jika terjadi error
		},
	});
}
function set_discount(id) {
	$.ajax({
		url: site_url + "ajax_request/get_discounts", // Ubah 'get_data.php' sesuai dengan URL yang sesuai untuk memperoleh data dari PHP
		type: "GET",
		dataType: "json",
		success: function (data) {
			var res = "";
			// Melakukan perulangan untuk setiap data yang diperoleh
			$.each(data, function (key, value) {
				// Membuat opsi baru dengan menggunakan data
				res +=
					'<option value="' +
					parseInt(value.discount_id) +
					'" discount-type="' +
					value.discount_type +
					'" discount-rate="' +
					parseInt(value.discount_value) +
					'">' +
					value.discount_name +
					"</option>";
			});
			var i = "#discount-" + id;
			$(i).append(res);
		},
		error: function (xhr, status, error) {
			console.log(error); // Menampilkan pesan kesalahan jika terjadi error
		},
	});
}
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
					'<option value="' + parseInt(val.id) + '">' + val.value + "</option>";
			});
			var i = "#project-" + id;
			$(i).append(res);
		},
		error: function (xhr, status, error) {
			console.log(error); // Menampilkan pesan kesalahan jika terjadi error
		},
	});
}

// //
// 	/
// 	/
// 	/
// 	/
// 	/

var rowCounter = $("#item_product >tbody >tr").length;
function addRow() {
	const table = document.getElementById("item_product");
	// let rowCounter = 1;
	const rowId = "row-" + rowCounter;

	const newRow = document.createElement("tr");
	newRow.setAttribute("class", "item-row");
	newRow.id = rowId;
	newRow.innerHTML =
		"</td > " +
		'<td><input type="hidden" name="product_id[]" value="0" class="product_id">' +
		'<input type="hidden" name="sub_category_id[]" value="0" class="sub_category_id">' +
		'<input type="hidden" name="uom_id[]" value="0" class="uom_id">' +
		'<input type="hidden" name="product_number[]" value="0" class="product_number">' +
		'<input type="text" class="form-control form-control-sm item_name" name="item_name[]" id="item_name" placeholder="Item Name"><span class="product_number font-weight-bold" style="font-size:10px;margin-top:2px"></span></td>' +
		"<td>" +
		"<select class='form-control form-control-sm' name='project_id[]' id='project-" +
		rowCounter +
		"'></select>" +
		"</td>" +
		"<td>" +
		"<select class='form-control form-control-sm tax_type' name='tax_type[]'id='tax-" +
		rowCounter +
		"'></select>" +
		'<td><input type="number" readonly="readonly" class="form-control form-control-sm tax-rate-item" name="tax_rate_item[]" value="0" /></td>' +
		"<td>" +
		"<select class='form-control form-control-sm discount_type' name='discount_type[]' id='discount-" +
		rowCounter +
		"'></select>" +
		'<td><input type="number" class="form-control form-control-sm discount-rate-item" readonly="readonly" name="discount_rate_item[]" value="0" /></td>' +
		'<td><input type="number" name="price[]" class="form-control form-control-sm price" value="0" id="price" /></td>' +
		'<td><input type="number" class="form-control form-control-sm qty" name="qty[]" id="qty" value="1"></td>' +
		'<td><input type="number" class="form-control form-control-sm sub-total-item" name="sub-total-item[]" readonly="readonly" value="0" /></td>' +
		'<td style="text-align:center"><button type="button" class="btn icon-btn btn-xs btn-danger waves-effect waves-light remove-item" data-repeater-delete="" onclick="removeRow(' +
		rowId +
		')"> <span class="fa fa-trash"></span></button></td>';
	table.appendChild(newRow);
	set_project(rowCounter);
	set_tax(rowCounter);
	set_discount(rowCounter);
	rowCounter++;
}

$(function () {
	// Event delegation for dynamically added elements
	$("#item_product").on("focus", ".item_name", function () {
		var row = $(this).closest("tr");
		$(this).autocomplete({
			source: function (request, response) {
				// Make your AJAX request here based on the input value
				// and get the autocomplete options
				var inputVal = request.term;
				// Example AJAX request (replace with your own implementation)
				$.ajax({
					url: site_url + "ajax_request/get_products", // Ubah 'get_data.php' sesuai dengan URL yang sesuai untuk memperoleh data dari PHP
					type: "GET",
					dataType: "json",
					cache: false,
					data: {
						query: inputVal,
					},
					success: function (data) {
						response(data);
					},
					error: function (jqXHR, textStatus, errorThrown) {
						// Handle error
						console.error(
							"AJAX request failed: " + textStatus + ", " + errorThrown
						);
					},
				});
			},
			minLength: 2,
			select: function (event, ui) {
				row.find(".product_id").val(parseInt(ui.item.product_id));
				row.find(".sub_category_id").val(parseInt(ui.item.sub_category_id));
				row.find(".price").val(parseInt(ui.item.price));
				row.find(".uom_id").val(parseInt(ui.item.uom_id));
				row.find(".product_number").val(ui.item.product_number);
				row.find(".product_number").html(ui.item.product_number);
				update_total();
			},
		});
	});
});

// Fungsi untuk menghapus baris tabel
$(document).on("click", ".remove-item", function () {
	$(this).closest("tr").remove();
	// updateFormCount();
});

// Fungsi edit otomatic kaluasi saat load
$(document).on("load", function () {
	update_total();
});

//
// /
// //
// 	/
// //
// //
/// Fungsi untuk memperbarui nomor urutan form
function updateFormCount() {
	$(".row").each(function (index) {
		let formCount = index + 1;
		$(this).closest("tr").find("td:first-child").text(formCount);
	});
}

function update_total() {
	var sub_total = 0;
	var st_tax = 0;
	var grand_total = 0;
	var gdTotal = 0;
	var rdiscount = 0;

	i = 1;

	//
	$(".sub-total-item").each(function (i) {
		var total = $(this).val();

		total = parseFloat(total);

		sub_total = total + sub_total;
	});

	$(".tax-rate-item").each(function (i) {
		var tax_rate = $(this).val();

		tax_rate = parseFloat(tax_rate);

		st_tax = tax_rate + st_tax;
	});

	// isi hasil kalkulasi pajak
	$(".tax_total").html(st_tax.toFixed(2));
	$(".ftax_total").val(st_tax.toFixed(2));
	$(".sub_total").html(sub_total.toFixed(2));

	jQuery(".items-tax-total").val(st_tax.toFixed(2));

	var item_sub_total = sub_total;

	var discount_figure = $(".discount_figure").val();
	//var fsub_total = item_sub_total - discount_figure;
	//alert(st_tax);
	//$('.items-tax-total').val(st_tax.toFixed(2));
	$(".items-sub-total").val(item_sub_total.toFixed(2));
	$(".fgrand_total").val(item_sub_total.toFixed(2));
	$(".grand_total").html(item_sub_total.toFixed(2));
} //Update total function ends here.
jQuery(document).on("click", ".remove-invoice-item", function () {
	$(this)
		.closest(".item-row")
		.fadeOut(300, function () {
			$(this).remove();
			update_total();
		});
});

jQuery(document).on("click", ".remove-item", function () {
	var record_id = $(this).data("record-id");
	var invoice_id = $(this).data("invoice-id");
	$(this)
		.closest(".item-row")
		.fadeOut(300, function () {
			// jQuery.get(
			// 	base_url + "/delete_item/" + record_id + "/isajax/",
			// 	function (data, status) {}
			// );
			$(this).remove();
			update_total();
		});
});
// for qty
jQuery(document).on("click keyup change", ".qty, .price", function () {
	var qty = 0;
	var price = 0;
	var tax_rate = 0;
	var qty = $(this).closest(".item-row").find(".qty").val();
	var price = $(this).closest(".item-row").find(".price").val();

	var tax_rate_item = $(this).closest(".item-row").find(".tax-rate-item").val();
	var discount_rate = $(this)
		.closest(".item-row")
		.find(".discount-rate-item")
		.val();

	var element = $(this)
		.closest(".item-row")
		.find(".tax_type")
		.find("option:selected");
	var tax_type = element.attr("tax-type");
	var tax_rate = element.attr("tax-rate");
	if (qty == "") {
		var qty = 0;
	}
	if (price == "") {
		var price = 0;
	}
	if (tax_rate == "") {
		var tax_rate = 0;
	}
	if (discount_rate == "") {
		var discount_rate = 0;
	}
	if (tax_rate_item == "") {
		var tax_rate_item = 0;
	}

	var element2 = $(this)
		.closest(".item-row")
		.find(".discount_type")
		.find("option:selected");
	var discount_type = parseInt(element2.attr("discount-type"));
	var discount_rate = parseInt(element2.attr("discount-rate"));

	// calculation
	var sbT = parseInt(qty * price);

	if (discount_type === 0) {
		var discountPP = (1 / 1) * discount_rate;
		var singleDiscount = discountPP;
		var sub = sbT - discountPP;
		// var subTotal = sub + parseInt(tax_rate_item);
		// var sub_total = subTotal.toFixed(2);
		jQuery(this)
			.closest(".item-row")
			.find(".discount-rate-item")
			.val(singleDiscount.toFixed(2));
	} else {
		var discountPP = (sbT / 100) * discount_rate;
		var singleDiscount = discountPP;
		var sub = sbT - discountPP;
		// var subTotal = sub + parseInt(tax_rate_item);
		// var sub_total = subTotal.toFixed(2);
		jQuery(this)
			.closest(".item-row")
			.find(".discount-rate-item")
			.val(singleDiscount.toFixed(2));
	}

	if (tax_type === "fixed") {
		var taxPP = (1 / 1) * tax_rate;
		var singleTax = taxPP;
		// var subTotal = sbT + taxPP;
		// var sub_total = subTotal.toFixed(2);
		jQuery(this)
			.closest(".item-row")
			.find(".tax-rate-item")
			.val(singleTax.toFixed(2));
	} else {
		var taxPP = (sbT / 100) * tax_rate;
		var singleTax = taxPP;
		// var subTotal = sbT + taxPP;
		// var sub_total = subTotal.toFixed(2);
		jQuery(this)
			.closest(".item-row")
			.find(".tax-rate-item")
			.val(singleTax.toFixed(2));
	}

	var sub1 = sbT - singleDiscount;
	var sub2 = sub1 + singleTax;
	var sub_total = sub2.toFixed(2);

	jQuery(this).closest(".item-row").find(".items-tax-total").val(tax_rate);
	jQuery(this).closest(".item-row").find(".sub-total-item").val(sub_total);
	jQuery(this).closest(".item-row").find(".sub-total-item").val(sub_total);
	update_total();
	//$('.tax-rate-item').html(taxPP.toFixed(2));
});

jQuery(document).on("change click", ".tax_type", function () {
	var qty = 0;
	var price = 0;
	var tax_rate = 0;
	var qty = $(this).closest(".item-row").find(".qty").val();
	var price = $(this).closest(".item-row").find(".price").val();
	var tax_rate_item = $(this).closest(".item-row").find(".tax-rate-item").val();
	var discount_rate = $(this)
		.closest(".item-row")
		.find(".discount-rate-item")
		.val();

	var element = $(this)
		.closest(".item-row")
		.find(".tax_type")
		.find("option:selected");
	var tax_type = element.attr("tax-type");
	var tax_rate = element.attr("tax-rate");

	if (qty == "") {
		var qty = 0;
	}
	if (price == "") {
		var price = 0;
	}
	if (tax_rate == "") {
		var tax_rate = 0;
	}

	if (discount_rate == "") {
		var discount_rate = 0;
	}
	// calculation
	var sbT = parseInt(qty * price) - parseInt(discount_rate);

	if (tax_type === "fixed") {
		var taxPP = (1 / 1) * tax_rate;
		var singleTax = taxPP;
		var subTotal = sbT + taxPP;
		var sub_total = subTotal.toFixed(2);
		jQuery(this)
			.closest(".item-row")
			.find(".tax-rate-item")
			.val(singleTax.toFixed(2));
		jQuery(this).closest(".item-row").find(".sub-total-item").val(sub_total);
		update_total();
	} else {
		var taxPP = (sbT / 100) * tax_rate;
		var singleTax = taxPP;
		var subTotal = sbT + taxPP;
		var sub_total = subTotal.toFixed(2);
		jQuery(this)
			.closest(".item-row")
			.find(".tax-rate-item")
			.val(singleTax.toFixed(2));
		jQuery(this).closest(".item-row").find(".sub-total-item").val(sub_total);
		update_total();
	}

	jQuery(this).closest(".item-row").find(".sub-total-item").val(sub_total);
	update_total();
});

jQuery(document).on("change click", ".discount_type", function () {
	var qty = 0;
	var price = 0;
	var tax_rate = 0;
	var discount_rate = 0;
	var qty = $(this).closest(".item-row").find(".qty").val();
	var price = $(this).closest(".item-row").find(".price").val();
	var tax_rate_item = $(this).closest(".item-row").find(".tax-rate-item").val();
	var discount_rate = $(this).closest(".item-row").find(".discount_type").val();

	var element = $(this)
		.closest(".item-row")
		.find(".discount_type")
		.find("option:selected");
	var discount_type = parseInt(element.attr("discount-type"));
	var discount_rate = parseInt(element.attr("discount-rate"));
	if (qty == "") {
		var qty = 0;
	}
	if (price == "") {
		var price = 0;
	}
	if (tax_rate == "") {
		var tax_rate = 0;
	}
	if (discount_rate == "") {
		var discount_rate = 0;
	}

	// calculation
	var sbT = parseInt(qty * price);

	if (discount_type === 0) {
		var discountPP = (1 / 1) * discount_rate;
		var singleDiscount = discountPP;
		var sub = sbT - discountPP;
		var subTotal = sub + parseInt(tax_rate_item);
		var sub_total = subTotal.toFixed(2);
		jQuery(this)
			.closest(".item-row")
			.find(".discount-rate-item")
			.val(singleDiscount.toFixed(2));
		jQuery(this).closest(".item-row").find(".sub-total-item").val(sub_total);
		update_total();
	} else {
		var discountPP = (sbT / 100) * discount_rate;
		var singleDiscount = discountPP;
		var sub = sbT - discountPP;
		var subTotal = sub + parseInt(tax_rate_item);
		var sub_total = subTotal.toFixed(2);
		jQuery(this)
			.closest(".item-row")
			.find(".discount-rate-item")
			.val(singleDiscount.toFixed(2));
		jQuery(this).closest(".item-row").find(".sub-total-item").val(sub_total);
		update_total();
	}

	jQuery(this).closest(".item-row").find(".sub-total-item").val(sub_total);
	update_total();
});

$(document).ready(function () {
	$("#select_due_date").change(function () {
		var duration = parseInt($("#select_due_date").val());
		var startDate = new Date($("#invoice_date").val());
		var durationType = $("#select_due_date").find(":selected").data("type");

		if (isNaN(duration) || isNaN(startDate.getTime())) {
			alert("Please enter valid input");
			return;
		}

		var dueDate = calculateDueDate(startDate, duration, durationType);
		$("#invoice_due_date").val(dueDate.toISOString().split("T")[0]);
	});
	$("#invoice_due_date").change(function () {
		$("#select_due_date").val(0).change();
	});
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

// chart
$(window).on("load", function () {
	var ctx_trans_vendor = $("#last_month_trans_vendor");
	Chart.defaults.global.legend.display = true;
	$.ajax({
		url: site_url + "project_costs/get_last_month_trans_vendor/",
		type: "get",
		contentType: "application/json; charset=utf-8",
		dataType: "json",
		success: function (response_tv) {
			// console.log(response_tv);
			var bgcolor_tv = [];
			var final_tv = [];
			var final_tv2 = [];
			for (i = 0; i < response_tv.c_name.length; i++) {
				final_tv.push(response_tv.chart_data[i].value);
				final_tv2.push(response_tv.chart_data[i].label);
				bgcolor_tv.push(response_tv.chart_data[i].bgcolor);
			}

			new Chart(ctx_trans_vendor, {
				type: "pie",
				options: {
					responsive: true,
					maintainAspectRatio: false,
					responsiveAnimationDuration: 500,
				},
				data: {
					labels: final_tv2,
					datasets: [
						{
							label: "project status",
							data: final_tv,
							backgroundColor: bgcolor_tv,
						},
					],
				},
			});

			var table = $(".table_last_month_trans_vendor tbody");
			// Append rows to the table
			$.each(response_tv.chart_data, function (index, item) {
				var label_tv = item.label;
				var value_tv = item.value;
				var bgcolor_tv = item.bgcolor;

				var row_tv =
					"<tr class='align-baseline'>" +
					"<td><span class='badge badge-pill' style='background: " +
					bgcolor_tv +
					"'>&nbsp;</span></td><td>" +
					label_tv +
					" (" +
					value_tv +
					")" +
					"</td>" +
					"</tr>";

				table.append(row_tv);
			});
		},
		error: function (data) {
			console.error(data);
		},
	});
});

$(window).on("load", function () {
	var ctx_trans = $("#last_month_trans");
	Chart.defaults.global.legend.display = true;
	$.ajax({
		url: site_url + "project_costs/get_last_month_trans/",
		type: "get",
		contentType: "application/json; charset=utf-8",
		dataType: "json",
		success: function (response_t) {
			var bgcolor_t = [];
			var final_t = [];
			var final_t2 = [];
			for (i = 0; i < response_t.c_name.length; i++) {
				final_t.push(response_t.chart_data[i].value);
				final_t2.push(response_t.chart_data[i].label);
				bgcolor_t.push(response_t.chart_data[i].bgcolor);
			}

			new Chart(ctx_trans, {
				type: "doughnut",
				options: {
					responsive: true,
					maintainAspectRatio: false,
					responsiveAnimationDuration: 500,
				},
				data: {
					labels: final_t2,
					datasets: [
						{
							label: "project status",
							data: final_t,
							backgroundColor: bgcolor_t,
						},
					],
				},
			});

			var table = $(".table_last_month_trans tbody");
			// Append rows to the table
			$.each(response_t.chart_data, function (index, item) {
				var label_t = item.label;
				var value_t = item.format_value;
				var bgcolor_t = item.bgcolor;

				var row_t =
					"<tr class='align-baseline'>" +
					"<td><span class='badge badge-pill' style='background: " +
					bgcolor_t +
					"'>&nbsp;</span></td><td>" +
					label_t +
					"<br><small>" +
					value_t +
					"</small>" +
					"</td>" +
					"</tr>";

				table.append(row_t);
			});
		},
		error: function (data) {
			console.error(data);
		},
	});
});
