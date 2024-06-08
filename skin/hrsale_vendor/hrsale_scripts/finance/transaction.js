$(document).ready(function () {
	var id = getUrlParameter("id");
	var otable = $("#ms_table").DataTable({
		bPaginate: false,
		ajax: {
			url: site_url + "finance/accounts/get_ajax_trans_account/",
			data: {
				id: id,
			},
			type: "GET",
		},
		fnDrawCallback: function (settings) {
			$('[data-toggle="tooltip"]').tooltip();
		},
		createdRow: function (row, data, dataIndex) {
			$("td", row).addClass("align-middle");
		},
	});

	$("#ms_table_filter").hide();
	$("#cari_data").keyup(function () {
		otable.search($(this).val()).draw();
	});
});
