$(function () {
	$('[data-plugin="select_account"]').select2({
		ajax: {
			delay: 250,
			url: site_url + "ajax_request/get_accounts",
			data: function (params) {
				var queryParameters = {
					query: params.term,
				};
				return queryParameters;
			},

			processResults: function (data) {
				return {
					results: data,
				};
			},
			cache: true,
			transport: function (params, success, failure) {
				var $request = $.ajax(params);

				$request.then(success);
				$request.fail(failure);

				return $request;
			},
		},
		width: "100%",
		// language: {
		// 	noResults: function () {
		// 		return `<li><button style="width: 100%" type="button"
		//     class="btn btn-tranparent btn-sm"
		//     onClick='addVendor()'><span class="ion ion-md-add"></span> Add Vendor</button>
		//     </li>`;
		// 	},
		// },
	});
});
