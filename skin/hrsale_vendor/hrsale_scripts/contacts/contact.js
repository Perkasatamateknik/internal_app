$(document).ready(function () {
	$('[data-plugin="select_hrm"]').select2({ width: "100%" });

	var ms_table = $("#ms_table").DataTable({
		bDestroy: true,

		ajax: {
			url: base_url + "/ajax_contact_list/",
			type: "GET",
			data: {
				filter: $('input[name="filter_contact"]').val(),
			},
		},
		fnDrawCallback: function (settings) {
			$('[data-toggle="tooltip"]').tooltip();
		},
	});

	$("#ms_table_trans").DataTable({
		bDestroy: true,

		ajax: {
			url: base_url + "/ajax_all_trans/",
			type: "GET",
			data: {
				_token: $("#ms_table_trans").data("id"),
			},
		},
		fnDrawCallback: function (settings) {
			$('[data-toggle="tooltip"]').tooltip();
		},
	});

	$("#ms_table_filter").hide();
	$("#cari_data").keyup(function () {
		ms_table.search($(this).val()).draw();
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
					// ms_table.api().ajax.reload(function () {
					// 	toastr.success(JSON.result);
					// }, true);
					toastr.options = {
						timeOut: 700,
						onHidden: function () {
							window.location.reload();
						},
					};
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

function modalAdd() {
	$.ajax({
		type: "GET",
		url: base_url + "/ajax_modal_add",
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

function modalEdit(id) {
	$.ajax({
		type: "GET",
		url: base_url + "/ajax_modal_edit",
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

$(document).ready(function () {
	$("#type_form").submit(function (e) {
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
							window.location.href = site_url + "/contacts/types";
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

// button set delete
$(document).on("click", ".delete", function () {
	$("input[name=_token]").val($(this).data("record-id"));
	$("#data-message").addClass("pt-3 font-weight-bold");

	let del_file = $(this).data("record-name");

	$("#data-message").html(del_file);
	$("#warning-message").html($(this).data("warning"));
	$("#warning-message").addClass("pt-3 text-danger text-sm");

	$("#delete_record").attr("action", site_url + "contacts/delete/");
});
