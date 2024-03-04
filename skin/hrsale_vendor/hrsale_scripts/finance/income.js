$(document).ready(function () {
	var id = getUrlParameter("id");
	$("#ms_table").DataTable({
		ajax: {
			url: site_url + "finance/incomes/get_ajax_invoices/",
			type: "GET",
		},
		fnDrawCallback: function (settings) {
			$('[data-toggle="tooltip"]').tooltip();
		},
		columnDefs: [
			{
				orderable: false,
				targets: "_all",
			},
		],
	});

	$("#ms_table thead").hide();
});
