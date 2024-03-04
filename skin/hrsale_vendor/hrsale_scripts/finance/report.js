$(document).ready(function () {
	var otable = $("#ms_table").DataTable({
		bPaginate: false,
		ajax: {
			url: site_url + "finance/reports/get_ajax_report/",
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
});
