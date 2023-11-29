$(document).ready(function () {
	var id = getUrlParameter("id");
	$("#ms_table").DataTable({
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
	});
});
