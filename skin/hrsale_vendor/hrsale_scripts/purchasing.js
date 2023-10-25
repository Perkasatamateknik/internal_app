var formatter = new Intl.NumberFormat("id-ID", {
	style: "currency",
	currency: type_currency,
	minimumFractionDigits: 2,
});
function formatCurrency(number) {
	return formatter.format(number);
}

function formatCurrencyNumber(number) {
	return number.toLocaleString("id-ID", { minimumFractionDigits: 2 });
}

function chart_vendors() {
	var ctx_trans = $("#get_purchase_by_vendors");
	Chart.defaults.global.legend.display = false;
	$.ajax({
		url: site_url + "purchasing/get_purchase_by_vendors/",
		data: {
			month: $("#monthSelectorVendor").val(),
		},
		type: "get",
		contentType: "application/json; charset=utf-8",
		dataType: "json",
		success: function (res) {
			var bgcolor_t = [];
			var final_t = [];
			var final_t2 = [];
			for (i = 0; i < res.chart_data.length; i++) {
				final_t.push(res.chart_data[i].value);
				final_t2.push(res.chart_data[i].label);
				bgcolor_t.push(res.chart_data[i].bgcolor);
			}

			chart_vendors = new Chart(ctx_trans, {
				type: "doughnut",
				options: {
					responsive: true,
					maintainAspectRatio: false,
					responsiveAnimationDuration: 500,
				},
				data: {
					labels: final_t2,
					datasets: [
						{
							label: final_t2,
							data: final_t,
							backgroundColor: bgcolor_t,
						},
					],
				},
			});

			var tbody = $("#table_purchase_by_vendors tbody");
			var tfoot = $("#table_purchase_by_vendors tfoot");

			var amount = 0;
			$.each(res.chart_data, function (index, item) {
				var row = $("<tr>");
				row.html(
					`<td>${item.label}</td><td class='text-right'><small>${item.format_value}</small></td>`
				);
				tbody.append(row);

				amount += parseFloat(item.value);
			});

			tfoot.html(
				`<tr class="tfoot-light"><td>Amount</td><td class='text-right'><strong>${formatCurrency(
					amount
				)}</strong></td></tr>`
			);
		},
		error: function (data) {
			console.error(data);
		},
	});
}

function chart_select() {
	var ctx_trans = $("#get_purchase_selected");
	Chart.defaults.global.legend.display = false;

	var selectedVal = $('[name="selected"]').val();
	$.ajax({
		url: site_url + "purchasing/get_purchase_by_selected/",
		type: "get",
		data: {
			selected: selectedVal,
			month: $('[name="month"]').val(),
		},
		contentType: "application/json; charset=utf-8",
		dataType: "json",
		success: function (res) {
			var bgcolor_s = [];
			var final_s = [];
			var final_s2 = [];
			for (i = 0; i < res.chart_data.length; i++) {
				final_s.push(res.chart_data[i].value);
				final_s2.push(res.chart_data[i].label);
				bgcolor_s.push(res.chart_data[i].bgcolor);
			}

			myChart = new Chart(ctx_trans, {
				type: "doughnut",
				options: {
					responsive: true,
					maintainAspectRatio: false,
					responsiveAnimationDuration: 500,
				},
				data: {
					labels: final_s2,
					datasets: [
						{
							label: final_s2,
							data: final_s,
							backgroundColor: bgcolor_s,
						},
					],
				},
			});

			// Redraw the chart
			myChart.update();

			var tbody = $("#table_purchase_selected tbody");
			var tfoot = $("#table_purchase_selected tfoot");

			var amount = 0;
			var rows;
			$.each(res.chart_data, function (index, item) {
				rows += `<tr><td>${item.label}</td><td class='text-right text-bold'><small>${item.format_value}</small></td></tr>`;

				amount += parseFloat(item.value);
			});
			tbody.html(rows);
			tfoot.html(
				`<tr class="bg-secondary text-bold"><td>Amount</td><td class='text-right'><strong>${formatCurrency(
					amount
				)}</strong></td></tr>`
			);

			if (selectedVal == "category") {
				$("#setname").html("Kategori");
			} else if (selectedVal == "sub-category") {
				$("#setname").html("Sub Kategori");
			} else if (selectedVal == "uom") {
				$("#setname").html("Masurement Unit");
			}
		},
		error: function (data) {
			console.error(data);
		},
	});
}

$(document).ready(function () {
	chart_vendors();
	chart_select();
});

$("#valueSelector, #monthSelector").on("change", function () {
	return chart_select;
});

$("#monthSelectorVendor").on("change", function () {
	alert($(this).val());
	var ctx_trans = $("#get_purchase_by_vendors");
	Chart.defaults.global.legend.display = false;
	$.ajax({
		url: site_url + "purchasing/get_purchase_by_vendors/",
		data: {
			month: $(this).val(),
		},
		type: "get",
		contentType: "application/json; charset=utf-8",
		dataType: "json",
		success: function (res) {
			var bgcolor_t = [];
			var final_t = [];
			var final_t2 = [];
			for (i = 0; i < res.chart_data.length; i++) {
				final_t.push(res.chart_data[i].value);
				final_t2.push(res.chart_data[i].label);
				bgcolor_t.push(res.chart_data[i].bgcolor);
			}

			chart_vendors = new Chart(ctx_trans, {
				type: "doughnut",
				options: {
					responsive: true,
					maintainAspectRatio: false,
					responsiveAnimationDuration: 500,
				},
				data: {
					labels: final_t2,
					datasets: [
						{
							label: final_t2,
							data: final_t,
							backgroundColor: bgcolor_t,
						},
					],
				},
			});

			var tbody = $("#table_purchase_by_vendors tbody");
			var tfoot = $("#table_purchase_by_vendors tfoot");

			var amount = 0;
			$.each(res.chart_data, function (index, item) {
				var row = $("<tr>");
				row.html(
					`<td>${item.label}</td><td class='text-right'><small>${item.format_value}</small></td>`
				);
				tbody.append(row);

				amount += parseFloat(item.value);
			});

			tfoot.html(
				`<tr class="tfoot-light"><td>Amount</td><td class='text-right'><strong>${formatCurrency(
					amount
				)}</strong></td></tr>`
			);
		},
		error: function (data) {
			console.error(data);
		},
	});
});
