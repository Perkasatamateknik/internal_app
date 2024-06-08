$(document).ready(function () {
	$('[data-plugin="select_hrm"]').select2({ width: "100%" });

	var xin_table_purchase_deliveries = $(
		"#xin_table_purchase_deliveries"
	).dataTable({
		bDestroy: true,
		iDisplayLength: 30,
		aLengthMenu: [
			[10, 30, 50, 100, -1],
			[10, 30, 50, 100, "All"],
		],
		ajax: {
			url: site_url + "purchase_deliveries/get_ajax_table/",
			type: "GET",
		},
		fnDrawCallback: function (settings) {
			$('[data-toggle="tooltip"]').tooltip();
		},
	});

	$("#purchase_deliveries").submit(function (e) {
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
						timeOut: 1000,
						onHidden: function () {
							window.location.href =
								base_url + "/view/" + $("input[name='pd_number']").val();
						},
					};
					toastr.success(JSON.result);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					$(".icon-spinner3").hide();
					$("#purchase_orders")[0].reset(); // To reset form fields
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
					xin_table_purchase_deliveries.api().ajax.reload(function () {
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

// set modal delete
$(document).on("click", ".delete", function () {
	$("input[name=_token]").val($(this).data("record-id"));
	$("#delete_record").attr("action", base_url + "/delete/");
});

// set modal edit add item
$(document).on("click", ".edit-add-item", function () {
	$("input[name=_token]").val($(this).data("record-id"));
	$("#delete_record").attr("action", base_url + "/delete/");
});

$(document).ready(function () {
	var span = '<span class="text-danger">*</span>';
	var required = $(".form-control[required]");
	required.closest(".form-group").find("label").append(span);
});

$(function () {
	$('[data-plugin="select_contacts"]').select2({
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
});

$(function () {
	// set selected vendor
	$.get({
		url: site_url + "ajax_request/find_contact_by_id",
		data: "query=" + $("[name='selected_contact']").val(),
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
});

// function to edit show
$(window).on("load", function () {
	let type = $("input[name='purchase_deliveries']").val() ?? "";
	if (type == "UPDATE") {
		let pd_number = $("input[name='pd_number']").val();
		$.ajax({
			type: "GET",
			url: base_url + "/get_ajax_pd",
			data: "pd_number=" + pd_number,
			dataType: "JSON",
			success: function (response) {
				if (response) {
					// set selected vendor
					$.get({
						url: site_url + "ajax_request/find_vendor_by_id",
						data: "query=" + response.data.vendor_id,
						dataType: "JSON",
						success: function (res) {
							if (res != null) {
								var selectedOptionId = res.vendor_id;
								var selectedOptionText = res.vendor_name;

								var option = new Option(
									selectedOptionText,
									selectedOptionId,
									true,
									true
								);
								$("[name='vendor']").append(option).trigger("change");
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

					// set another
					$("#pd_number").val(response.data.pd_number);
					$("#faktur_number").val(response.data.faktur_number);
					$("#warehouse_assign").val(response.data.warehouse_assign);
					$("#date").val(response.data.date);
					$("#delivery_date").val(response.data.delivery_date);
					$("#delivery_name").val(response.data.delivery_name);
					$("#delivery_number").val(response.data.delivery_number);
					$("#delivery_fee").val(response.data.delivery_fee);
					$("#reference").val(response.data.reference);
					$("#notes").text(response.data.notes);
					$("#amount").val(response.data.amount);
					$("#amount_show").text(formatCurrency(response.data.amount));
					update_total();
				}
			},
		});
	}
});
