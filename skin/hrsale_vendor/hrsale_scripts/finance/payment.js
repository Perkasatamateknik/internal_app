$(document).ready(function () {
	$("#ms_table").DataTable({
		ajax: {
			url: site_url + "finance/payments/get_ajax_account_payments/",
			type: "GET",
		},
		fnDrawCallback: function (settings) {
			$('[data-toggle="tooltip"]').tooltip();
		},
	});
});
