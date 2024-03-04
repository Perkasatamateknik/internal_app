$(document).ready(function () {
	$("#xin_table").DataTable({
		bPaginate: false,
		ajax: {
			url: site_url + "finance/accounts/get_ajax_table/",
			type: "GET",
		},
		fnDrawCallback: function (settings) {
			$('[data-toggle="tooltip"]').tooltip();
		},
	});

	$("#xin_table_trans").DataTable({
		ajax: {
			url: site_url + "finance/accounts/get_ajax_table_trans/",
			data: { id: $("#account_id").val() },
			type: "GET",
		},
		fnDrawCallback: function (settings) {
			$('[data-toggle="tooltip"]').tooltip();
		},
	});

	// modal add account
	$("#add-modal-data").on("show.bs.modal", function (event) {
		$.ajax({
			url: site_url + "finance/accounts/get_modal_add_account/",
			type: "GET",
			success: function (response) {
				if (response) {
					$("#add_ajax_modal").html(response);
				}
			},
		});
	});

	/* Edit data */
});
