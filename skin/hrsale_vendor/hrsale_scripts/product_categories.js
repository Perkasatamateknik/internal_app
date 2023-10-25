$(document).ready(function () {
	$('[data-plugin="select_hrm"]').select2($(this).attr("data-options"));
	$('[data-plugin="select_hrm"]').select2({ width: "100%" });
	// listing
	// On page load:

	// update 9-5-2023
	var product_categories = $("#xin_table_product_categories").dataTable({
		bDestroy: true,
		iDisplayLength: 100,
		aLengthMenu: [
			[10, 30, 50, 100, -1],
			[10, 30, 50, 100, "All"],
		],
		ajax: {
			url: site_url + "product_categories/get_ajax_table/",
			type: "GET",
		},
		fnDrawCallback: function (settings) {
			$('[data-toggle="tooltip"]').tooltip();
		},
	});

	var product_sub_categories = $("#xin_table_product_sub_categories").dataTable(
		{
			bDestroy: true,
			iDisplayLength: 100,
			aLengthMenu: [
				[10, 30, 50, 100, -1],
				[10, 30, 50, 100, "All"],
			],
			ajax: {
				url: site_url + "product_categories/get_ajax_table_sub/",
				type: "GET",
			},
			fnDrawCallback: function (settings) {
				$('[data-toggle="tooltip"]').tooltip();
			},
		}
	);

	// update 9-5-2023
	jQuery("#product_categories").submit(function (e) {
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
				"&is_ajax=471&data=product_categories&type=create&form=" +
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
					product_categories.api().ajax.reload(function () {
						toastr.success(JSON.result);
					}, true);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					$(".icon-spinner3").hide();
					jQuery("#product_categories")[0].reset(); // To reset form fields
					jQuery(".save").prop("disabled", false);
					Ladda.stopAll();
				}
			},
		});
	});

	// update 9-5-2023
	jQuery("#product_sub_categories").submit(function (e) {
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
				"&is_ajax=471&data=product_sub_categories&type=create&form=" +
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
					product_sub_categories.api().ajax.reload(function () {
						toastr.success(JSON.result);
					}, true);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					$(".icon-spinner3").hide();
					jQuery("#product_sub_categories")[0].reset(); // To reset form fields
					jQuery(".save").prop("disabled", false);
					Ladda.stopAll();
				}
			},
		});
	});

	/* Delete data categroy*/
	// $("#delete_record").submit(function (e) {
	// 	/*Form Submit*/
	// 	e.preventDefault();
	// 	var obj = $(this),
	// 		action = obj.attr("name");
	// 	$.ajax({
	// 		type: "POST",
	// 		url: e.target.action,
	// 		data: obj.serialize() + "&is_ajax=2&type=delete&form=" + action,
	// 		cache: false,
	// 		success: function (JSON) {
	// 			if (JSON.error != "") {
	// 				toastr.error(JSON.error);
	// 				$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
	// 				Ladda.stopAll();
	// 			} else {
	// 				$(".delete-modal").modal("toggle");
	// 				product_categories.api().ajax.reload(function () {
	// 					toastr.success(JSON.result);
	// 				}, true);
	// 				$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
	// 				Ladda.stopAll();
	// 			}
	// 		},
	// 	});
	// });

	/* Delete data sub category*/
	$("#delete_record").submit(function (e) {
		var tk_type = $("#token_type").val();
		$(".icon-spinner3").show();

		if (tk_type == "product_categories") {
			var field_add =
				"&is_ajax=9&data=delete_product_categories&type=delete_record&";
			var tb_name = "xin_table_" + tk_type;
		} else if (tk_type == "product_sub_categories") {
			var field_add =
				"&is_ajax=10&data=delete_product_sub_categories&type=delete_record&";
			var tb_name = "xin_table_" + tk_type;
		}

		/*Form Submit*/
		e.preventDefault();
		var obj = $(this),
			action = obj.attr("name");
		$.ajax({
			url: e.target.action,
			type: "post",
			data: "?" + obj.serialize() + field_add + "form=" + action,
			cache: false,
			success: function (JSON) {
				if (JSON.error != "") {
					toastr.error(JSON.error);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					$(".icon-spinner3").hide();
					Ladda.stopAll();
				} else {
					$(".delete-modal").modal("toggle");
					$(".icon-spinner3").hide();
					$("#" + tb_name)
						.dataTable()
						.api()
						.ajax.reload(function () {
							toastr.success(JSON.result);
						}, true);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					Ladda.stopAll();
				}
			},
		});

		/*Form Submit*/
		// e.preventDefault();
		// var obj = $(this),
		// 	action = obj.attr("name");
		// $.ajax({
		// 	type: "POST",
		// 	url: e.target.action,
		// 	data: obj.serialize() + "&is_ajax=2&type=delete&form=" + action,
		// 	cache: false,
		// 	success: function (JSON) {
		// 		if (JSON.error != "") {
		// 			toastr.error(JSON.error);
		// 			$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
		// 			Ladda.stopAll();
		// 		} else {
		// 			$(".delete-modal").modal("toggle");
		// 			product_sub_categories.api().ajax.reload(function () {
		// 				toastr.success(JSON.result);
		// 			}, true);
		// 			$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
		// 			Ladda.stopAll();
		// 		}
		// 	},
		// });
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

	// edit
	// $(".edit-modal-data").on("show.bs.modal", function (event) {
	// 	var button = $(event.relatedTarget);
	// 	var category_id = button.data("category_id");
	// 	var modal = $(this);
	// 	$.ajax({
	// 		url: base_url + "/read/",
	// 		type: "GET",
	// 		data:
	// 			"jd=1&is_ajax=1&mode=modal&data=product_category&category_id=" +
	// 			category_id,
	// 		success: function (response) {
	// 			if (response) {
	// 				$("#ajax_modal").html(response);
	// 			}
	// 		},
	// 	});
	// });
});

$(document).on("click", ".delete", function () {
	$("input[name=_token]").val($(this).data("record-id"));
	$("input[name=token_type]").val($(this).data("token_type"));
	$("#delete_record").attr(
		"action",
		site_url +
			"product_categories/delete_" +
			$(this).data("token_type") +
			"/" +
			$(this).data("record-id")
	) + "/";
});

// $(document).on("click", ".delete", function () {
// 	$("input[name=_token]").val($(this).data("record-id"));
// 	$("#delete_record").attr(
// 		"action",
// 		site_url + "product_categories/delete/" + $(this).data("record-id")
// 	);
// });
