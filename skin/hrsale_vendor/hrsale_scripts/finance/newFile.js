$(document).ready(function () {
	$("#payment_form").submit(function (e) {
		e.preventDefault();
		var obj = $(this),
			action = obj.attr("name");
		var formData = new FormData(obj[0]);
		formData.append("form", action);
		jQuery(".save").prop("disabled", true);
		$(".icon-spinner3").show();

		$.ajax({
			type: "POST",
			enctype: "multipart/form-data",
			url: e.target.action,
			data: formData,
			cache: false,
			processData: false, // Important for FormData
			contentType: false, // Important for FormData
			success: function (JSON) {
				// jika ada error
				if (JSON.error != "") {
					toastr.error(JSON.error);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					$(".save").prop("disabled", false);
					$(".icon-spinner3").hide();
					Ladda.stopAll();
				} else {
					toastr.options = {
						timeOut: 500,
						onHidden: function () {
							window.location.reload();
						},
					};
					toastr.success(JSON.result);

					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					$(".icon-spinner3").hide();
					$(".save").prop("disabled", false);
					Ladda.stopAll();
				}
			},
			error: function (xhr, status, error) {
				toastr.error("Error: " + status + " | " + error);
				$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
				$(".icon-spinner3").hide();
				$(".save").prop("disabled", false);
				Ladda.stopAll();
			},
		});
	});
});
