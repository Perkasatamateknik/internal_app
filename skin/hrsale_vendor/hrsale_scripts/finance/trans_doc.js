$(document).ready(function () {
	$(".btn-ajax-trans").on("click", function () {
		var type = $(this).attr("id");

		alert(type);
	});

	var id = getUrlParameter("id");
	$("#ms_table").DataTable({
		ajax: {
			url: site_url + "finance/accounts/get_ajax_account_transfer/",
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
