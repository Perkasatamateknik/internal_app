$(function () {
	$("#add-modal-data").on("show.bs.modal", function (event) {
		$.ajax({
			url: site_url + "finance/cash_bank/get_modal_add_cash_bank/",
			type: "GET",
			success: function (response) {
				if (response) {
					$("#add_ajax_modal").html(response);
				}
			},
		});
	});

	// edit
	$(".edit-modal-data").on("show.bs.modal", function (event) {
		var button = $(event.relatedTarget);
		var id = button.data("id");
		var modal = $(this);
		$.ajax({
			url: site_url + "finance/cash_bank/get_modal_edit_cash_bank/",
			type: "GET",
			data: {
				id: id,
			},
			success: function (response) {
				if (response) {
					$("#ajax_modal").html(response);
				}
			},
		});
	});
});
